<?php

use App\Services\ImageGenerationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->service = new ImageGenerationService;
});

it('throws exception when provider is not configured', function () {
    // Mock missing API key
    config(['image-generation.providers.openai.api_key' => null]);

    expect(fn () => $this->service->generate('test prompt', 'openai'))
        ->toThrow(\Exception::class, "Image generation provider 'openai' is not configured or missing API key");
});

it('throws exception when provider does not exist', function () {
    // Provider doesn't exist in config, so it's not configured
    expect(fn () => $this->service->generate('test prompt', 'nonexistent'))
        ->toThrow(\Exception::class, "Image generation provider 'nonexistent' is not configured or missing API key");
});

it('checks if provider is configured correctly', function () {
    config(['image-generation.providers.openai.api_key' => 'test-key']);

    expect($this->service->isProviderConfigured('openai'))->toBeTrue();
    expect($this->service->isProviderConfigured('nonexistent'))->toBeFalse();
});

it('returns only configured providers', function () {
    config([
        'image-generation.providers.openai.api_key' => 'test-key',
        'image-generation.providers.stability.api_key' => null,
    ]);

    $available = $this->service->getAvailableProviders();

    expect($available)->toContain('openai');
    expect($available)->not->toContain('stability');
});

describe('OpenAI DALL-E generation', function () {
    beforeEach(function () {
        Storage::fake('s3');

        config([
            'image-generation.providers.openai.api_key' => 'test-openai-key',
            'image-generation.providers.openai.endpoint' => 'https://api.openai.com/v1/images/generations',
            'image-generation.providers.openai.model' => 'dall-e-3',
            'image-generation.providers.openai.size' => '1024x1024',
            'image-generation.providers.openai.quality' => 'standard',
            'image-generation.providers.openai.style' => 'vivid',
            'image-generation.providers.openai.n' => 1,
            'image-generation.default_provider' => 'openai',
            'image-generation.timeout' => 60,
            'image-generation.storage.disk' => 's3',
            'image-generation.storage.path' => 'images/generated',
            'image-generation.storage.visibility' => 'public',
            'filesystems.disks.s3.url' => 'https://s3.example.com',
        ]);
    });

    it('generates image successfully with OpenAI and saves to S3', function () {
        $imageContent = 'fake-image-binary-content';

        Http::fake([
            'api.openai.com/v1/images/generations' => Http::response([
                'data' => [
                    [
                        'url' => 'https://example.com/image.png',
                        'revised_prompt' => 'Revised prompt text',
                    ],
                ],
            ], 200),
            'example.com/image.png' => Http::response($imageContent, 200, ['Content-Type' => 'image/png']),
        ]);

        $result = $this->service->generate('A beautiful planet in space');

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['url', 'path', 'disk', 'provider'])
            ->and($result['provider'])->toBe('openai')
            ->and($result['disk'])->toBe('s3')
            ->and($result['path'])->toStartWith('images/generated/')
            ->and($result['path'])->toEndWith('.png')
            ->and($result['revised_prompt'])->toBe('Revised prompt text');

        // Verify image was downloaded and saved
        Http::assertSent(fn ($request) => $request->url() === 'https://example.com/image.png');

        // Verify image was saved to storage
        Storage::disk('s3')->assertExists($result['path']);
        expect(Storage::disk('s3')->get($result['path']))->toBe($imageContent);
    });

    it('sends correct request to OpenAI API', function () {
        Http::fake([
            'api.openai.com/v1/images/generations' => Http::response([
                'data' => [['url' => 'https://example.com/image.png']],
            ], 200),
            'example.com/image.png' => Http::response('image-content', 200),
        ]);

        $this->service->generate('Test prompt');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.openai.com/v1/images/generations'
                && $request->hasHeader('Authorization', 'Bearer test-openai-key')
                && $request->hasHeader('Content-Type', 'application/json')
                && $request['prompt'] === 'Test prompt'
                && $request['model'] === 'dall-e-3'
                && $request['size'] === '1024x1024';
        });
    });

    it('throws exception when OpenAI API returns invalid response', function () {
        Http::fake([
            'api.openai.com/v1/images/generations' => Http::response([
                'data' => [], // Missing URL
            ], 200),
        ]);

        expect(fn () => $this->service->generate('test prompt'))
            ->toThrow(\Exception::class, 'Invalid response from OpenAI API: missing image URL');
    });

    it('handles OpenAI API errors', function () {
        Http::fake([
            'api.openai.com/v1/images/generations' => Http::response([
                'error' => [
                    'message' => 'Invalid API key',
                    'type' => 'invalid_request_error',
                ],
            ], 401),
        ]);

        expect(fn () => $this->service->generate('test prompt'))
            ->toThrow(\Exception::class, 'Failed to generate image');
    });
});

describe('Stability AI generation', function () {
    beforeEach(function () {
        Storage::fake('s3');

        config([
            'image-generation.providers.stability.api_key' => 'test-stability-key',
            'image-generation.providers.stability.endpoint' => 'https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/text-to-image',
            'image-generation.providers.stability.engine_id' => 'stable-diffusion-xl-1024-v1-0',
            'image-generation.providers.stability.width' => 1024,
            'image-generation.providers.stability.height' => 1024,
            'image-generation.providers.stability.steps' => 30,
            'image-generation.providers.stability.cfg_scale' => 7,
            'image-generation.default_provider' => 'stability',
            'image-generation.timeout' => 60,
            'image-generation.storage.disk' => 's3',
            'image-generation.storage.path' => 'images/generated',
            'image-generation.storage.visibility' => 'public',
            'filesystems.disks.s3.url' => 'https://s3.example.com',
        ]);
    });

    it('generates image successfully with Stability AI and saves to S3', function () {
        $base64Image = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        $decodedImage = base64_decode($base64Image);

        Http::fake([
            'api.stability.ai/*' => Http::response([
                'artifacts' => [
                    [
                        'base64' => $base64Image,
                        'finishReason' => 'SUCCESS',
                    ],
                ],
            ], 200),
        ]);

        $result = $this->service->generate('A beautiful planet in space', 'stability');

        expect($result)
            ->toBeArray()
            ->toHaveKeys(['url', 'path', 'disk', 'provider'])
            ->and($result['provider'])->toBe('stability')
            ->and($result['disk'])->toBe('s3')
            ->and($result['path'])->toStartWith('images/generated/')
            ->and($result['path'])->toEndWith('.png');

        // Verify image was decoded and saved to storage
        Storage::disk('s3')->assertExists($result['path']);
        expect(Storage::disk('s3')->get($result['path']))->toBe($decodedImage);
    });

    it('sends correct request to Stability AI API', function () {
        Http::fake([
            'api.stability.ai/*' => Http::response([
                'artifacts' => [
                    [
                        'base64' => base64_encode('test-image-content'),
                        'finishReason' => 'SUCCESS',
                    ],
                ],
            ], 200),
        ]);

        $this->service->generate('Test prompt', 'stability');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.stability.ai')
                && $request->hasHeader('Authorization', 'Bearer test-stability-key');
        });
    });

    it('throws exception when Stability AI returns non-success finish reason', function () {
        Http::fake([
            'api.stability.ai/*' => Http::response([
                'artifacts' => [
                    [
                        'base64' => 'test',
                        'finishReason' => 'CONTENT_FILTERED',
                    ],
                ],
            ], 200),
        ]);

        expect(fn () => $this->service->generate('test prompt', 'stability'))
            ->toThrow(\Exception::class, 'Stability AI generation finished with reason: CONTENT_FILTERED');
    });

    it('throws exception when Stability AI returns invalid response', function () {
        Http::fake([
            'api.stability.ai/*' => Http::response([
                'artifacts' => [], // Missing base64
            ], 200),
        ]);

        expect(fn () => $this->service->generate('test prompt', 'stability'))
            ->toThrow(\Exception::class, 'Invalid response from Stability AI: missing image data');
    });
});

describe('S3 Storage', function () {
    beforeEach(function () {
        Storage::fake('s3');

        config([
            'image-generation.providers.openai.api_key' => 'test-openai-key',
            'image-generation.providers.openai.endpoint' => 'https://api.openai.com/v1/images/generations',
            'image-generation.providers.openai.model' => 'dall-e-3',
            'image-generation.providers.openai.size' => '1024x1024',
            'image-generation.providers.openai.quality' => 'standard',
            'image-generation.providers.openai.style' => 'vivid',
            'image-generation.providers.openai.n' => 1,
            'image-generation.default_provider' => 'openai',
            'image-generation.timeout' => 60,
            'image-generation.storage.disk' => 's3',
            'image-generation.storage.path' => 'images/generated',
            'image-generation.storage.visibility' => 'public',
        ]);
    });

    it('handles download failure when saving OpenAI image', function () {
        Http::fake([
            'api.openai.com/v1/images/generations' => Http::response([
                'data' => [
                    [
                        'url' => 'https://example.com/image.png',
                    ],
                ],
            ], 200),
        ]);

        // Mock Storage to throw exception when saving
        Storage::shouldReceive('disk')
            ->with('s3')
            ->andReturnSelf();

        Storage::shouldReceive('put')
            ->andThrow(new \Exception('Storage error'));

        expect(fn () => $this->service->generate('test prompt'))
            ->toThrow(\Exception::class, 'Failed to save image to storage');
    });

    it('handles empty base64 when saving Stability AI image', function () {
        config([
            'image-generation.providers.stability.api_key' => 'test-stability-key',
            'image-generation.providers.stability.endpoint' => 'https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/text-to-image',
            'image-generation.providers.stability.engine_id' => 'stable-diffusion-xl-1024-v1-0',
            'image-generation.providers.stability.width' => 1024,
            'image-generation.providers.stability.height' => 1024,
            'image-generation.providers.stability.steps' => 30,
            'image-generation.providers.stability.cfg_scale' => 7,
        ]);

        Http::fake([
            'api.stability.ai/*' => Http::response([
                'artifacts' => [
                    [
                        'base64' => '', // Empty base64
                        'finishReason' => 'SUCCESS',
                    ],
                ],
            ], 200),
        ]);

        // Empty base64 will decode to empty string, which should still work
        // The service will save it (even if empty)
        $result = $this->service->generate('test prompt', 'stability');

        expect($result)->toBeArray()
            ->and($result['path'])->toBeString();
    });

    it('generates unique filenames for each image', function () {
        Http::fake([
            'api.openai.com/v1/images/generations' => Http::response([
                'data' => [['url' => 'https://example.com/image1.png']],
            ], 200),
            'example.com/image1.png' => Http::response('image1', 200),
        ]);

        $result1 = $this->service->generate('prompt 1');

        Http::fake([
            'api.openai.com/v1/images/generations' => Http::response([
                'data' => [['url' => 'https://example.com/image2.png']],
            ], 200),
            'example.com/image2.png' => Http::response('image2', 200),
        ]);

        $result2 = $this->service->generate('prompt 2');

        expect($result1['path'])->not->toBe($result2['path']);
    });

    it('uses Laravel Storage url method for generating URLs', function () {
        Http::fake([
            'api.openai.com/v1/images/generations' => Http::response([
                'data' => [['url' => 'https://example.com/image.png']],
            ], 200),
            'example.com/image.png' => Http::response('image-content', 200),
        ]);

        $result = $this->service->generate('test prompt');

        // Laravel Storage::fake() returns local URLs, but in production
        // Storage::disk('s3')->url() will return the correct S3/MinIO URL
        expect($result['url'])->toBeString()->not->toBeEmpty();

        // Verify the URL is generated by Laravel Storage (not manually constructed)
        // With Storage::fake(), it returns /storage/... but in real S3 it will be the S3 URL
        expect($result['url'])->toContain('images/generated');
    });
});
