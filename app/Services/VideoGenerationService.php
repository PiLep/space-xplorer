<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service for AI-powered video generation.
 *
 * This service generates videos from text prompts using various AI providers
 * (OpenAI Sora, RunwayML, Pika Labs, etc.) via their REST APIs.
 */
class VideoGenerationService
{
    /**
     * Generate a video from a text prompt using the default provider.
     *
     * @param  string  $prompt  The text prompt describing the video to generate
     * @param  string|null  $provider  Optional provider name (defaults to config default)
     * @param  string|null  $subfolder  Optional subfolder within the storage path (e.g., 'planets')
     * @return array Array containing 'url' (S3 URL), 'path' (storage path), and 'provider'
     *
     * @throws \Exception If video generation fails
     */
    public function generate(string $prompt, ?string $provider = null, ?string $subfolder = null): array
    {
        $provider = $provider ?? config('video-generation.default_provider');

        if (! $this->isProviderConfigured($provider)) {
            throw new \Exception("Video generation provider '{$provider}' is not configured or missing API key.");
        }

        try {
            $result = match ($provider) {
                'openai' => $this->generateWithOpenAI($prompt),
                'runway' => $this->generateWithRunway($prompt),
                'pika' => $this->generateWithPika($prompt),
                default => throw new \Exception("Unsupported video generation provider: {$provider}"),
            };

            // Save video to storage (S3) and return S3 URL
            return $this->saveVideoToStorage($result, $provider, $subfolder);
        } catch (RequestException $e) {
            $errorMessage = $e->getMessage();
            $responseBody = $e->response?->json();

            Log::error('Video generation API request failed', [
                'provider' => $provider,
                'prompt' => $prompt,
                'error' => $errorMessage,
                'response' => $responseBody,
            ]);

            throw new \Exception("Failed to generate video: {$errorMessage}", 0, $e);
        } catch (ConnectionException $e) {
            Log::error('Video generation API connection failed', [
                'provider' => $provider,
                'prompt' => $prompt,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception("Failed to connect to video generation service: {$e->getMessage()}", 0, $e);
        } catch (\Exception $e) {
            Log::error('Video generation failed', [
                'provider' => $provider,
                'prompt' => $prompt,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate a video using OpenAI Sora.
     *
     * According to OpenAI Sora API documentation:
     * - Endpoint: POST /v1/videos (multipart/form-data)
     * - Returns a job object with id and status
     * - Must poll GET /v1/videos/{video_id} until status is 'completed'
     * - Download with GET /v1/videos/{video_id}/content
     *
     * @param  string  $prompt  The text prompt
     * @return array Array with 'url' and 'provider'
     */
    private function generateWithOpenAI(string $prompt): array
    {
        $config = config('video-generation.providers.openai');
        $timeout = config('video-generation.timeout');

        // Create video generation job using multipart/form-data
        $response = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => "Bearer {$config['api_key']}",
            ])
            ->asMultipart()
            ->post($config['endpoint'], [
                [
                    'name' => 'model',
                    'contents' => $config['model'],
                ],
                [
                    'name' => 'prompt',
                    'contents' => $prompt,
                ],
                [
                    'name' => 'size',
                    'contents' => $config['size'],
                ],
                [
                    'name' => 'seconds',
                    'contents' => (string) $config['seconds'],
                ],
            ])
            ->throw()
            ->json();

        // OpenAI Sora returns: { "id": "video_...", "status": "queued"|"in_progress", ... }
        if (! isset($response['id'])) {
            throw new \Exception('Invalid response from OpenAI API: missing video ID');
        }

        $videoId = $response['id'];

        // Poll until job is completed, then download the video
        return $this->pollAndDownloadOpenAIVideo($videoId, $config);
    }

    /**
     * Poll OpenAI job status until completion, then download the video content.
     *
     * @param  string  $videoId  The video ID from the create response
     * @param  array  $config  Provider configuration
     * @return array Array with 'url' (temporary download URL) and 'provider'
     */
    private function pollAndDownloadOpenAIVideo(string $videoId, array $config): array
    {
        $maxAttempts = config('video-generation.poll_max_attempts', 60);
        $pollInterval = config('video-generation.poll_interval', 10); // seconds (10-20 recommended)

        $baseUrl = 'https://api.openai.com/v1/videos';

        // Poll until job is completed
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            if ($attempt > 0) {
                sleep($pollInterval);
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$config['api_key']}",
                ])
                ->get("{$baseUrl}/{$videoId}")
                ->throw()
                ->json();

            $status = $response['status'] ?? 'unknown';

            if ($status === 'completed') {
                // Download the video content
                return $this->downloadOpenAIVideoContent($videoId, $config);
            }

            if ($status === 'failed') {
                $errorMessage = $response['error']['message'] ?? $response['error'] ?? 'Unknown error';
                throw new \Exception("Video generation job failed: {$errorMessage}");
            }

            // Log progress if available
            if (isset($response['progress'])) {
                Log::info('OpenAI video generation in progress', [
                    'video_id' => $videoId,
                    'status' => $status,
                    'progress' => $response['progress'],
                ]);
            }
        }

        throw new \Exception('Video generation job timed out after '.($maxAttempts * $pollInterval).' seconds');
    }

    /**
     * Download the video content from OpenAI.
     *
     * @param  string  $videoId  The video ID
     * @param  array  $config  Provider configuration
     * @return array Array with 'url' (temporary download URL) and 'provider'
     */
    private function downloadOpenAIVideoContent(string $videoId, array $config): array
    {
        $baseUrl = 'https://api.openai.com/v1/videos';

        // Download the video content (returns binary MP4)
        $videoContent = Http::timeout(300) // 5 minutes for video download
            ->withHeaders([
                'Authorization' => "Bearer {$config['api_key']}",
            ])
            ->get("{$baseUrl}/{$videoId}/content")
            ->throw()
            ->body();

        // Return the binary content - it will be saved to storage in saveVideoToStorage
        // We'll use a temporary URL structure that saveVideoToStorage can handle
        return [
            'content' => $videoContent, // Binary video content
            'provider' => 'openai',
            'job_id' => $videoId,
        ];
    }

    /**
     * Generate a video using RunwayML.
     *
     * @param  string  $prompt  The text prompt
     * @return array Array with 'url' and 'provider'
     */
    private function generateWithRunway(string $prompt): array
    {
        $config = config('video-generation.providers.runway');
        $timeout = config('video-generation.timeout');

        $response = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => "Bearer {$config['api_key']}",
                'Content-Type' => 'application/json',
            ])
            ->post($config['endpoint'], [
                'text_prompt' => $prompt,
                'duration' => $config['duration'],
                'aspect_ratio' => $config['aspect_ratio'],
            ])
            ->throw()
            ->json();

        // RunwayML returns: { "id": "...", "status": "...", "output": ["..."] }
        if (! isset($response['id'])) {
            throw new \Exception('Invalid response from RunwayML API: missing job ID');
        }

        // Poll for completion
        return $this->pollRunwayJobStatus($response['id'], $config);
    }

    /**
     * Poll RunwayML job status until completion.
     *
     * @param  string  $jobId  The job ID
     * @param  array  $config  Provider configuration
     * @return array Array with 'url' and 'provider'
     */
    private function pollRunwayJobStatus(string $jobId, array $config): array
    {
        $maxAttempts = config('video-generation.poll_max_attempts', 60);
        $pollInterval = config('video-generation.poll_interval', 5);

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            sleep($pollInterval);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$config['api_key']}",
                ])
                ->get("{$config['endpoint']}/{$jobId}")
                ->throw()
                ->json();

            if (isset($response['status']) && $response['status'] === 'succeeded') {
                if (! isset($response['output']) || empty($response['output'])) {
                    throw new \Exception('Job completed but no video URL in response');
                }

                return [
                    'url' => $response['output'][0],
                    'provider' => 'runway',
                    'job_id' => $jobId,
                ];
            }

            if (isset($response['status']) && in_array($response['status'], ['failed', 'cancelled'])) {
                throw new \Exception('Video generation job failed: '.($response['error'] ?? 'Unknown error'));
            }
        }

        throw new \Exception('Video generation job timed out after '.($maxAttempts * $pollInterval).' seconds');
    }

    /**
     * Generate a video using Pika Labs.
     *
     * @param  string  $prompt  The text prompt
     * @return array Array with 'url' and 'provider'
     */
    private function generateWithPika(string $prompt): array
    {
        $config = config('video-generation.providers.pika');
        $timeout = config('video-generation.timeout');

        $response = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => "Bearer {$config['api_key']}",
                'Content-Type' => 'application/json',
            ])
            ->post($config['endpoint'], [
                'prompt' => $prompt,
                'duration' => $config['duration'],
                'aspect_ratio' => $config['aspect_ratio'],
            ])
            ->throw()
            ->json();

        // Pika Labs returns: { "id": "...", "status": "...", "video_url": "..." }
        if (! isset($response['id'])) {
            throw new \Exception('Invalid response from Pika Labs API: missing job ID');
        }

        // Poll for completion
        return $this->pollPikaJobStatus($response['id'], $config);
    }

    /**
     * Poll Pika Labs job status until completion.
     *
     * @param  string  $jobId  The job ID
     * @param  array  $config  Provider configuration
     * @return array Array with 'url' and 'provider'
     */
    private function pollPikaJobStatus(string $jobId, array $config): array
    {
        $maxAttempts = config('video-generation.poll_max_attempts', 60);
        $pollInterval = config('video-generation.poll_interval', 5);

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            sleep($pollInterval);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$config['api_key']}",
                ])
                ->get("{$config['endpoint']}/{$jobId}")
                ->throw()
                ->json();

            if (isset($response['status']) && $response['status'] === 'completed') {
                if (! isset($response['video_url'])) {
                    throw new \Exception('Job completed but no video URL in response');
                }

                return [
                    'url' => $response['video_url'],
                    'provider' => 'pika',
                    'job_id' => $jobId,
                ];
            }

            if (isset($response['status']) && $response['status'] === 'failed') {
                throw new \Exception('Video generation job failed: '.($response['error'] ?? 'Unknown error'));
            }
        }

        throw new \Exception('Video generation job timed out after '.($maxAttempts * $pollInterval).' seconds');
    }

    /**
     * Check if a provider is properly configured.
     *
     * @param  string  $provider  Provider name
     * @return bool True if provider is configured and has API key
     */
    public function isProviderConfigured(string $provider): bool
    {
        $providers = config('video-generation.providers');

        if (! isset($providers[$provider])) {
            return false;
        }

        $config = $providers[$provider];

        return ! empty($config['api_key']);
    }

    /**
     * Get list of available providers.
     *
     * @return array Array of provider names
     */
    public function getAvailableProviders(): array
    {
        $providers = config('video-generation.providers');
        $available = [];

        foreach ($providers as $name => $config) {
            if ($this->isProviderConfigured($name)) {
                $available[] = $name;
            }
        }

        return $available;
    }

    /**
     * Save video to storage (S3) and return storage URL.
     *
     * @param  array  $result  Result from video generation API
     * @param  string  $provider  Provider name
     * @param  string|null  $subfolder  Optional subfolder within the storage path (e.g., 'planets')
     * @return array Array with 'url' (S3 URL), 'path' (storage path), and 'provider'
     *
     * @throws \Exception If saving fails
     */
    private function saveVideoToStorage(array $result, string $provider, ?string $subfolder = null): array
    {
        $storageConfig = config('video-generation.storage');
        $disk = $storageConfig['disk'];
        $basePath = $storageConfig['path'];
        $visibility = $storageConfig['visibility'];

        // Build storage path with optional subfolder
        $path = $basePath;
        if ($subfolder) {
            $path = rtrim($basePath, '/').'/'.trim($subfolder, '/');
        }

        // Generate unique filename
        $filename = Str::uuid().'.mp4';
        $storagePath = rtrim($path, '/').'/'.ltrim($filename, '/');

        try {
            // Handle different provider response formats
            if (isset($result['content'])) {
                // OpenAI Sora: Direct binary content
                $videoContent = $result['content'];
            } elseif (isset($result['url'])) {
                // Other providers: Download from URL
                $videoContent = Http::timeout(300)->get($result['url'])->body(); // 5 min timeout for video download
            } else {
                throw new \Exception('Invalid video data format from provider: missing URL or content');
            }

            // Save to storage
            Storage::disk($disk)->put($storagePath, $videoContent, $visibility);

            // Get public URL using Laravel Storage's native method
            $url = Storage::disk($disk)->url($storagePath);

            Log::info('Video saved to storage', [
                'provider' => $provider,
                'path' => $storagePath,
                'disk' => $disk,
            ]);

            return [
                'url' => $url,
                'path' => $storagePath, // Store path for future URL reconstruction
                'disk' => $disk,
                'provider' => $provider,
                'job_id' => $result['job_id'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to save video to storage', [
                'provider' => $provider,
                'path' => $storagePath,
                'disk' => $disk,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception("Failed to save video to storage: {$e->getMessage()}", 0, $e);
        }
    }
}
