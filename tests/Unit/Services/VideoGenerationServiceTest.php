<?php

use App\Exceptions\ApiRequestException;
use App\Exceptions\JobTimeoutException;
use App\Exceptions\ProviderConfigurationException;
use App\Exceptions\StorageException;
use App\Exceptions\UnsupportedProviderException;
use App\Services\VideoGenerationService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->service = new VideoGenerationService;
});

it('throws exception when provider is not configured', function () {
    // Mock missing API key
    config(['video-generation.providers.openai.api_key' => null]);

    expect(fn () => $this->service->generate('test prompt', 'openai'))
        ->toThrow(ProviderConfigurationException::class);
});

it('throws exception when provider does not exist', function () {
    // Provider doesn't exist in config, so it's not configured
    expect(fn () => $this->service->generate('test prompt', 'nonexistent'))
        ->toThrow(ProviderConfigurationException::class);
});

it('throws exception for unsupported provider', function () {
    config(['video-generation.providers.unsupported.api_key' => 'test-key']);

    expect(fn () => $this->service->generate('test prompt', 'unsupported'))
        ->toThrow(UnsupportedProviderException::class);
});

it('checks if provider is configured correctly', function () {
    config(['video-generation.providers.openai.api_key' => 'test-key']);

    expect($this->service->isProviderConfigured('openai'))->toBeTrue();
    expect($this->service->isProviderConfigured('nonexistent'))->toBeFalse();
});

it('returns only configured providers', function () {
    config([
        'video-generation.providers.openai.api_key' => 'test-key',
        'video-generation.providers.runway.api_key' => 'test-key',
        'video-generation.providers.pika.api_key' => null,
    ]);

    $available = $this->service->getAvailableProviders();

    expect($available)->toContain('openai')
        ->and($available)->toContain('runway')
        ->and($available)->not->toContain('pika');
});

describe('OpenAI Sora generation', function () {
    beforeEach(function () {
        Storage::fake('s3');

        config([
            'video-generation.providers.openai.api_key' => 'test-openai-key',
            'video-generation.providers.openai.endpoint' => 'https://api.openai.com/v1/videos',
            'video-generation.providers.openai.model' => 'sora-2',
            'video-generation.providers.openai.size' => '1280x720',
            'video-generation.providers.openai.seconds' => 8,
            'video-generation.default_provider' => 'openai',
            'video-generation.timeout' => 300,
            'video-generation.poll_max_attempts' => 3, // Reduced for tests
            'video-generation.poll_interval' => 1, // Reduced for tests
            'video-generation.storage.disk' => 's3',
            'video-generation.storage.path' => 'videos/generated',
            'video-generation.storage.visibility' => 'public',
            'filesystems.disks.s3.url' => 'https://s3.example.com',
        ]);
    });

    it('generates video successfully with OpenAI Sora and saves to S3', function () {
        $videoContent = 'fake-video-binary-content';
        $videoId = 'video_abc123';

        Http::fake(function ($request) use ($videoId, $videoContent) {
            $url = $request->url();

            // POST request to create job
            if ($request->method() === 'POST' && str_contains($url, '/videos') && ! str_contains($url, '/content')) {
                return Http::response(['id' => $videoId, 'status' => 'completed'], 200);
            }

            // GET request to check status (polling)
            if ($request->method() === 'GET' && str_contains($url, '/videos/') && ! str_contains($url, '/content')) {
                return Http::response(['id' => $videoId, 'status' => 'completed'], 200);
            }

            // GET request to download content
            if (str_contains($url, '/content')) {
                return Http::response($videoContent, 200, ['Content-Type' => 'video/mp4']);
            }

            return Http::response(['error' => 'Not found'], 404);
        });

        $result = $this->service->generate('A beautiful planet rotating in space');

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['url', 'path', 'disk', 'provider', 'job_id'])
            ->and($result['provider'])->toBe('openai')
            ->and($result['disk'])->toBe('s3')
            ->and($result['job_id'])->toBe($videoId)
            ->and($result['path'])->toStartWith('videos/generated/')
            ->and($result['path'])->toEndWith('.mp4');

        // Verify video was saved to storage
        Storage::disk('s3')->assertExists($result['path']);
        expect(Storage::disk('s3')->get($result['path']))->toBe($videoContent);
    });

    it('sends correct request to OpenAI API', function () {
        $videoId = 'video_abc123';

        Http::fake(function ($request) use ($videoId) {
            $url = $request->url();

            if ($request->method() === 'POST' && str_contains($url, '/videos') && ! str_contains($url, '/content')) {
                return Http::response(['id' => $videoId, 'status' => 'completed'], 200);
            }

            if ($request->method() === 'GET' && str_contains($url, '/videos/') && ! str_contains($url, '/content')) {
                return Http::response(['id' => $videoId, 'status' => 'completed'], 200);
            }

            if (str_contains($url, '/content')) {
                return Http::response('video-content', 200);
            }

            return Http::response(['error' => 'Not found'], 404);
        });

        $this->service->generate('Test prompt');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.openai.com/v1/videos')
                && $request->hasHeader('Authorization', 'Bearer test-openai-key')
                && $request->isMultipart();
        });
    });

    it('polls job status until completion', function () {
        $videoId = 'video_abc123';
        $callCount = 0;

        Http::fake(function ($request) use ($videoId, &$callCount) {
            $url = $request->url();
            $callCount++;

            if ($request->method() === 'POST' && str_contains($url, '/videos') && ! str_contains($url, '/content')) {
                return Http::response(['id' => $videoId, 'status' => 'queued'], 200);
            }

            if ($request->method() === 'GET' && str_contains($url, '/videos/') && ! str_contains($url, '/content')) {
                if ($callCount === 2) {
                    return Http::response(['id' => $videoId, 'status' => 'in_progress', 'progress' => 30], 200);
                }
                if ($callCount === 3) {
                    return Http::response(['id' => $videoId, 'status' => 'completed'], 200);
                }

                return Http::response(['id' => $videoId, 'status' => 'completed'], 200);
            }

            if (str_contains($url, '/content')) {
                return Http::response('video-content', 200);
            }

            return Http::response(['error' => 'Not found'], 404);
        });

        $result = $this->service->generate('Test prompt');

        expect($result['job_id'])->toBe($videoId);
    });

    it('throws exception when OpenAI API returns invalid response', function () {
        Http::fake([
            'api.openai.com/v1/videos' => Http::response([
                'status' => 'queued', // Missing ID
            ], 200),
        ]);

        expect(fn () => $this->service->generate('test prompt'))
            ->toThrow(ApiRequestException::class, 'Invalid response from OpenAI API: missing video ID');
    });

    it('throws exception when job fails', function () {
        $videoId = 'video_abc123';
        $callCount = 0;

        Http::fake(function ($request) use ($videoId, &$callCount) {
            $url = $request->url();
            $callCount++;

            if ($request->method() === 'POST' && str_contains($url, '/videos') && ! str_contains($url, '/content')) {
                return Http::response(['id' => $videoId, 'status' => 'queued'], 200);
            }

            if ($request->method() === 'GET' && str_contains($url, '/videos/') && ! str_contains($url, '/content')) {
                return Http::response(['id' => $videoId, 'status' => 'failed', 'error' => ['message' => 'Generation failed']], 200);
            }

            return Http::response(['error' => 'Not found'], 404);
        });

        expect(fn () => $this->service->generate('test prompt'))
            ->toThrow(ApiRequestException::class, 'Video generation job failed');
    });

    it('throws exception when job times out', function () {
        $videoId = 'video_abc123';

        config(['video-generation.poll_max_attempts' => 2]);

        Http::fake(function ($request) use ($videoId) {
            $url = $request->url();

            if ($request->method() === 'POST' && str_contains($url, '/videos') && ! str_contains($url, '/content')) {
                return Http::response(['id' => $videoId, 'status' => 'queued'], 200);
            }

            if ($request->method() === 'GET' && str_contains($url, '/videos/') && ! str_contains($url, '/content')) {
                // Always return in_progress to simulate timeout
                return Http::response(['id' => $videoId, 'status' => 'in_progress'], 200);
            }

            return Http::response(['error' => 'Not found'], 404);
        });

        expect(fn () => $this->service->generate('test prompt'))
            ->toThrow(JobTimeoutException::class, 'Video generation job timed out');
    });

    it('handles OpenAI API errors', function () {
        Http::fake([
            'api.openai.com/v1/videos' => Http::response([
                'error' => [
                    'message' => 'Invalid API key',
                    'type' => 'invalid_request_error',
                ],
            ], 401),
        ]);

        expect(fn () => $this->service->generate('test prompt'))
            ->toThrow(ApiRequestException::class, 'Failed to generate video');
    });

    it('handles connection errors', function () {
        Http::fake([
            'api.openai.com/v1/videos' => function () {
                throw new ConnectionException('Connection timeout');
            },
        ]);

        expect(fn () => $this->service->generate('test prompt'))
            ->toThrow(ApiRequestException::class, 'Failed to connect to video generation service');
    });
});

describe('RunwayML generation', function () {
    beforeEach(function () {
        Storage::fake('s3');

        config([
            'video-generation.providers.runway.api_key' => 'test-runway-key',
            'video-generation.providers.runway.endpoint' => 'https://api.runwayml.com/v1/generate',
            'video-generation.providers.runway.duration' => 5,
            'video-generation.providers.runway.aspect_ratio' => '16:9',
            'video-generation.default_provider' => 'runway',
            'video-generation.timeout' => 300,
            'video-generation.poll_max_attempts' => 3,
            'video-generation.poll_interval' => 1,
            'video-generation.storage.disk' => 's3',
            'video-generation.storage.path' => 'videos/generated',
            'video-generation.storage.visibility' => 'public',
            'filesystems.disks.s3.url' => 'https://s3.example.com',
        ]);
    });

    it('generates video successfully with RunwayML and saves to S3', function () {
        $videoUrl = 'https://example.com/video.mp4';
        $videoContent = 'fake-video-content';
        $jobId = 'job_123';

        Http::fake([
            // Initial job creation
            'api.runwayml.com/v1/generate' => Http::response(['id' => $jobId, 'status' => 'processing'], 200),
            // Polling
            "api.runwayml.com/v1/generate/{$jobId}" => Http::sequence()
                ->push(['id' => $jobId, 'status' => 'processing'], 200)
                ->push(['id' => $jobId, 'status' => 'succeeded', 'output' => [$videoUrl]], 200),
            // Video download
            'example.com/video.mp4' => Http::response($videoContent, 200, ['Content-Type' => 'video/mp4']),
        ]);

        $result = $this->service->generate('A beautiful planet rotating in space', 'runway');

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['url', 'path', 'disk', 'provider', 'job_id'])
            ->and($result['provider'])->toBe('runway')
            ->and($result['disk'])->toBe('s3')
            ->and($result['job_id'])->toBe($jobId)
            ->and($result['path'])->toStartWith('videos/generated/')
            ->and($result['path'])->toEndWith('.mp4');

        // Verify video was downloaded and saved
        Storage::disk('s3')->assertExists($result['path']);
        expect(Storage::disk('s3')->get($result['path']))->toBe($videoContent);
    });

    it('sends correct request to RunwayML API', function () {
        $jobId = 'job_123';
        $videoUrl = 'https://example.com/video.mp4';

        Http::fake(function ($request) use ($jobId, $videoUrl) {
            $url = $request->url();

            if ($request->method() === 'POST' && str_contains($url, 'api.runwayml.com/v1/generate') && ! str_contains($url, '/'.$jobId)) {
                return Http::response(['id' => $jobId, 'status' => 'succeeded', 'output' => [$videoUrl]], 200);
            }

            if ($request->method() === 'GET' && str_contains($url, '/'.$jobId)) {
                return Http::response(['id' => $jobId, 'status' => 'succeeded', 'output' => [$videoUrl]], 200);
            }

            if (str_contains($url, 'example.com/video.mp4')) {
                return Http::response('video-content', 200);
            }

            return Http::response(['error' => 'Not found'], 404);
        });

        $this->service->generate('Test prompt', 'runway');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.runwayml.com/v1/generate')
                && $request->hasHeader('Authorization', 'Bearer test-runway-key')
                && $request->method() === 'POST';
        });
    });

    it('throws exception when RunwayML returns invalid response', function () {
        Http::fake([
            'api.runwayml.com/v1/generate' => Http::response([
                'status' => 'processing', // Missing ID
            ], 200),
        ]);

        expect(fn () => $this->service->generate('test prompt', 'runway'))
            ->toThrow(ApiRequestException::class, 'Invalid response from RunwayML API: missing job ID');
    });

    it('throws exception when job fails', function () {
        $jobId = 'job_123';

        Http::fake([
            'api.runwayml.com/v1/generate' => Http::response(['id' => $jobId, 'status' => 'processing'], 200),
            "api.runwayml.com/v1/generate/{$jobId}" => Http::response(['id' => $jobId, 'status' => 'failed', 'error' => 'Generation failed'], 200),
        ]);

        expect(fn () => $this->service->generate('test prompt', 'runway'))
            ->toThrow(ApiRequestException::class, 'Video generation job failed');
    });

    it('throws exception when job completes without output', function () {
        $jobId = 'job_123';

        Http::fake([
            'api.runwayml.com/v1/generate' => Http::response(['id' => $jobId, 'status' => 'processing'], 200),
            "api.runwayml.com/v1/generate/{$jobId}" => Http::response(['id' => $jobId, 'status' => 'succeeded', 'output' => []], 200),
        ]);

        expect(fn () => $this->service->generate('test prompt', 'runway'))
            ->toThrow(ApiRequestException::class, 'Job completed but no video URL in response');
    });
});

describe('Pika Labs generation', function () {
    beforeEach(function () {
        Storage::fake('s3');

        config([
            'video-generation.providers.pika.api_key' => 'test-pika-key',
            'video-generation.providers.pika.endpoint' => 'https://api.pika.art/v1/generate',
            'video-generation.providers.pika.duration' => 4,
            'video-generation.providers.pika.aspect_ratio' => '16:9',
            'video-generation.default_provider' => 'pika',
            'video-generation.timeout' => 300,
            'video-generation.poll_max_attempts' => 3,
            'video-generation.poll_interval' => 1,
            'video-generation.storage.disk' => 's3',
            'video-generation.storage.path' => 'videos/generated',
            'video-generation.storage.visibility' => 'public',
            'filesystems.disks.s3.url' => 'https://s3.example.com',
        ]);
    });

    it('generates video successfully with Pika Labs and saves to S3', function () {
        $videoUrl = 'https://example.com/video.mp4';
        $videoContent = 'fake-video-content';
        $jobId = 'job_456';

        Http::fake([
            // Initial job creation
            'api.pika.art/v1/generate' => Http::response(['id' => $jobId, 'status' => 'processing'], 200),
            // Polling
            "api.pika.art/v1/generate/{$jobId}" => Http::sequence()
                ->push(['id' => $jobId, 'status' => 'processing'], 200)
                ->push(['id' => $jobId, 'status' => 'completed', 'video_url' => $videoUrl], 200),
            // Video download
            'example.com/video.mp4' => Http::response($videoContent, 200, ['Content-Type' => 'video/mp4']),
        ]);

        $result = $this->service->generate('A beautiful planet rotating in space', 'pika');

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['url', 'path', 'disk', 'provider', 'job_id'])
            ->and($result['provider'])->toBe('pika')
            ->and($result['disk'])->toBe('s3')
            ->and($result['job_id'])->toBe($jobId)
            ->and($result['path'])->toStartWith('videos/generated/')
            ->and($result['path'])->toEndWith('.mp4');

        // Verify video was downloaded and saved
        Storage::disk('s3')->assertExists($result['path']);
        expect(Storage::disk('s3')->get($result['path']))->toBe($videoContent);
    });

    it('sends correct request to Pika Labs API', function () {
        $jobId = 'job_456';
        $videoUrl = 'https://example.com/video.mp4';

        Http::fake(function ($request) use ($jobId, $videoUrl) {
            $url = $request->url();

            if ($request->method() === 'POST' && str_contains($url, 'api.pika.art/v1/generate') && ! str_contains($url, '/'.$jobId)) {
                return Http::response(['id' => $jobId, 'status' => 'completed', 'video_url' => $videoUrl], 200);
            }

            if ($request->method() === 'GET' && str_contains($url, '/'.$jobId)) {
                return Http::response(['id' => $jobId, 'status' => 'completed', 'video_url' => $videoUrl], 200);
            }

            if (str_contains($url, 'example.com/video.mp4')) {
                return Http::response('video-content', 200);
            }

            return Http::response(['error' => 'Not found'], 404);
        });

        $this->service->generate('Test prompt', 'pika');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.pika.art/v1/generate')
                && $request->hasHeader('Authorization', 'Bearer test-pika-key')
                && $request->method() === 'POST';
        });
    });

    it('throws exception when Pika Labs returns invalid response', function () {
        Http::fake([
            'api.pika.art/v1/generate' => Http::response([
                'status' => 'processing', // Missing ID
            ], 200),
        ]);

        expect(fn () => $this->service->generate('test prompt', 'pika'))
            ->toThrow(ApiRequestException::class, 'Invalid response from Pika Labs API: missing job ID');
    });

    it('throws exception when job fails', function () {
        $jobId = 'job_456';

        Http::fake([
            'api.pika.art/v1/generate' => Http::response(['id' => $jobId, 'status' => 'processing'], 200),
            "api.pika.art/v1/generate/{$jobId}" => Http::response(['id' => $jobId, 'status' => 'failed', 'error' => 'Generation failed'], 200),
        ]);

        expect(fn () => $this->service->generate('test prompt', 'pika'))
            ->toThrow(ApiRequestException::class, 'Video generation job failed');
    });

    it('throws exception when job completes without video URL', function () {
        $jobId = 'job_456';

        Http::fake([
            'api.pika.art/v1/generate' => Http::response(['id' => $jobId, 'status' => 'processing'], 200),
            "api.pika.art/v1/generate/{$jobId}" => Http::response(['id' => $jobId, 'status' => 'completed'], 200),
        ]);

        expect(fn () => $this->service->generate('test prompt', 'pika'))
            ->toThrow(ApiRequestException::class, 'Job completed but no video URL in response');
    });
});

describe('S3 Storage', function () {
    beforeEach(function () {
        Storage::fake('s3');

        config([
            'video-generation.providers.openai.api_key' => 'test-openai-key',
            'video-generation.providers.openai.endpoint' => 'https://api.openai.com/v1/videos',
            'video-generation.providers.openai.model' => 'sora-2',
            'video-generation.providers.openai.size' => '1280x720',
            'video-generation.providers.openai.seconds' => 8,
            'video-generation.default_provider' => 'openai',
            'video-generation.timeout' => 300,
            'video-generation.poll_max_attempts' => 3,
            'video-generation.poll_interval' => 1,
            'video-generation.storage.disk' => 's3',
            'video-generation.storage.path' => 'videos/generated',
            'video-generation.storage.visibility' => 'public',
        ]);
    });

    it('saves video with subfolder', function () {
        $videoId = 'video_abc123';
        $videoContent = 'fake-video-content';

        Http::fake(function ($request) use ($videoId, $videoContent) {
            $url = $request->url();

            if ($request->method() === 'POST' && str_contains($url, '/videos') && ! str_contains($url, '/content')) {
                return Http::response(['id' => $videoId, 'status' => 'completed'], 200);
            }

            if ($request->method() === 'GET' && str_contains($url, '/videos/') && ! str_contains($url, '/content')) {
                return Http::response(['id' => $videoId, 'status' => 'completed'], 200);
            }

            if (str_contains($url, '/content')) {
                return Http::response($videoContent, 200);
            }

            return Http::response(['error' => 'Not found'], 404);
        });

        $result = $this->service->generate('Test prompt', 'openai', 'planets');

        expect($result['path'])->toStartWith('videos/generated/planets/')
            ->and($result['path'])->toEndWith('.mp4');

        Storage::disk('s3')->assertExists($result['path']);
    });

    it('generates unique filenames for each video', function () {
        $videoId1 = 'video_123';
        $videoId2 = 'video_456';
        $callCount = 0;

        Http::fake(function ($request) use ($videoId1, $videoId2, &$callCount) {
            $url = $request->url();
            $callCount++;

            if ($request->method() === 'POST' && str_contains($url, '/videos') && ! str_contains($url, '/content')) {
                $id = $callCount === 1 ? $videoId1 : $videoId2;

                return Http::response(['id' => $id, 'status' => 'completed'], 200);
            }

            if ($request->method() === 'GET' && str_contains($url, '/videos/') && ! str_contains($url, '/content')) {
                $id = str_contains($url, $videoId1) ? $videoId1 : $videoId2;

                return Http::response(['id' => $id, 'status' => 'completed'], 200);
            }

            if (str_contains($url, '/content')) {
                if (str_contains($url, $videoId1)) {
                    return Http::response('video1', 200);
                }

                return Http::response('video2', 200);
            }

            return Http::response(['error' => 'Not found'], 404);
        });

        $result1 = $this->service->generate('prompt 1');
        $result2 = $this->service->generate('prompt 2');

        expect($result1['path'])->not->toBe($result2['path']);
    });

    it('handles storage failure', function () {
        $videoId = 'video_abc123';

        Http::fake(function ($request) use ($videoId) {
            $url = $request->url();

            if ($request->method() === 'POST' && str_contains($url, '/videos') && ! str_contains($url, '/content')) {
                return Http::response(['id' => $videoId, 'status' => 'completed'], 200);
            }

            if ($request->method() === 'GET' && str_contains($url, '/videos/') && ! str_contains($url, '/content')) {
                return Http::response(['id' => $videoId, 'status' => 'completed'], 200);
            }

            if (str_contains($url, '/content')) {
                return Http::response('video-content', 200);
            }

            return Http::response(['error' => 'Not found'], 404);
        });

        // Mock Storage to throw exception
        Storage::shouldReceive('disk')
            ->with('s3')
            ->andReturnSelf();

        Storage::shouldReceive('put')
            ->andThrow(new \Exception('Storage error'));

        expect(fn () => $this->service->generate('test prompt'))
            ->toThrow(StorageException::class, 'Failed to save video to storage');
    });

});
