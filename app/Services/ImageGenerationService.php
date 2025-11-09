<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service for AI-powered image generation.
 *
 * This service generates images from text prompts using various AI providers
 * (OpenAI DALL-E, Stability AI, etc.) via their REST APIs.
 */
class ImageGenerationService
{
    /**
     * Generate an image from a text prompt using the default provider.
     *
     * @param  string  $prompt  The text prompt describing the image to generate
     * @param  string|null  $provider  Optional provider name (defaults to config default)
     * @return array Array containing 'url' (S3 URL), 'path' (storage path), and 'provider'
     *
     * @throws \Exception If image generation fails
     */
    public function generate(string $prompt, ?string $provider = null): array
    {
        $provider = $provider ?? config('image-generation.default_provider');

        if (! $this->isProviderConfigured($provider)) {
            throw new \Exception("Image generation provider '{$provider}' is not configured or missing API key.");
        }

        try {
            $result = match ($provider) {
                'openai' => $this->generateWithOpenAI($prompt),
                'stability' => $this->generateWithStability($prompt),
                default => throw new \Exception("Unsupported image generation provider: {$provider}"),
            };

            // Save image to storage (S3) and return S3 URL
            return $this->saveImageToStorage($result, $provider);
        } catch (RequestException $e) {
            Log::error('Image generation API request failed', [
                'provider' => $provider,
                'prompt' => $prompt,
                'error' => $e->getMessage(),
                'response' => $e->response?->json(),
            ]);

            throw new \Exception("Failed to generate image: {$e->getMessage()}", 0, $e);
        } catch (ConnectionException $e) {
            Log::error('Image generation API connection failed', [
                'provider' => $provider,
                'prompt' => $prompt,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception("Failed to connect to image generation service: {$e->getMessage()}", 0, $e);
        } catch (\Exception $e) {
            Log::error('Image generation failed', [
                'provider' => $provider,
                'prompt' => $prompt,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate an image using OpenAI DALL-E.
     *
     * @param  string  $prompt  The text prompt
     * @return array Array with 'url' and 'provider'
     */
    private function generateWithOpenAI(string $prompt): array
    {
        $config = config('image-generation.providers.openai');
        $timeout = config('image-generation.timeout');

        $response = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => "Bearer {$config['api_key']}",
                'Content-Type' => 'application/json',
            ])
            ->post($config['endpoint'], [
                'model' => $config['model'],
                'prompt' => $prompt,
                'size' => $config['size'],
                'quality' => $config['quality'],
                'style' => $config['style'],
                'n' => $config['n'],
            ])
            ->throw()
            ->json();

        // OpenAI DALL-E 3 returns: { "data": [{ "url": "...", "revised_prompt": "..." }] }
        if (! isset($response['data'][0]['url'])) {
            throw new \Exception('Invalid response from OpenAI API: missing image URL');
        }

        return [
            'url' => $response['data'][0]['url'],
            'provider' => 'openai',
            'revised_prompt' => $response['data'][0]['revised_prompt'] ?? null,
        ];
    }

    /**
     * Generate an image using Stability AI.
     *
     * @param  string  $prompt  The text prompt
     * @return array Array with 'url' and 'provider'
     */
    private function generateWithStability(string $prompt): array
    {
        $config = config('image-generation.providers.stability');
        $timeout = config('image-generation.timeout');

        $response = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => "Bearer {$config['api_key']}",
                'Accept' => 'application/json',
            ])
            ->asMultipart()
            ->post($config['endpoint'], [
                [
                    'name' => 'text_prompts[0][text]',
                    'contents' => $prompt,
                ],
                [
                    'name' => 'cfg_scale',
                    'contents' => (string) $config['cfg_scale'],
                ],
                [
                    'name' => 'width',
                    'contents' => (string) $config['width'],
                ],
                [
                    'name' => 'height',
                    'contents' => (string) $config['height'],
                ],
                [
                    'name' => 'steps',
                    'contents' => (string) $config['steps'],
                ],
            ])
            ->throw()
            ->json();

        // Stability AI returns: { "artifacts": [{ "base64": "...", "finishReason": "SUCCESS" }] }
        if (! isset($response['artifacts'][0]['base64'])) {
            throw new \Exception('Invalid response from Stability AI: missing image data');
        }

        // Stability AI returns base64, we need to decode and potentially save it
        // For now, we'll return the base64 data - you may want to save it to storage
        $base64Data = $response['artifacts'][0]['base64'];
        $finishReason = $response['artifacts'][0]['finishReason'] ?? 'UNKNOWN';

        if ($finishReason !== 'SUCCESS') {
            throw new \Exception("Stability AI generation finished with reason: {$finishReason}");
        }

        return [
            'base64' => $base64Data,
            'provider' => 'stability',
            'finish_reason' => $finishReason,
        ];
    }

    /**
     * Check if a provider is properly configured.
     *
     * @param  string  $provider  Provider name
     * @return bool True if provider is configured and has API key
     */
    public function isProviderConfigured(string $provider): bool
    {
        $providers = config('image-generation.providers');

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
        $providers = config('image-generation.providers');
        $available = [];

        foreach ($providers as $name => $config) {
            if ($this->isProviderConfigured($name)) {
                $available[] = $name;
            }
        }

        return $available;
    }

    /**
     * Save image to storage (S3) and return storage URL.
     *
     * @param  array  $result  Result from image generation API
     * @param  string  $provider  Provider name
     * @return array Array with 'url' (S3 URL), 'path' (storage path), and 'provider'
     *
     * @throws \Exception If saving fails
     */
    private function saveImageToStorage(array $result, string $provider): array
    {
        $storageConfig = config('image-generation.storage');
        $disk = $storageConfig['disk'];
        $path = $storageConfig['path'];
        $visibility = $storageConfig['visibility'];

        // Generate unique filename
        $filename = Str::uuid() . '.png';
        $storagePath = rtrim($path, '/') . '/' . ltrim($filename, '/');

        try {
            // Handle different provider response formats
            if (isset($result['url'])) {
                // OpenAI: Download from URL
                $imageContent = Http::timeout(30)->get($result['url'])->body();
            } elseif (isset($result['base64'])) {
                // Stability AI: Decode base64
                $imageContent = base64_decode($result['base64']);
                if ($imageContent === false) {
                    throw new \Exception('Failed to decode base64 image data');
                }
            } else {
                throw new \Exception('Invalid image data format from provider');
            }

            // Save to storage
            Storage::disk($disk)->put($storagePath, $imageContent, $visibility);

            // Get public URL
            $url = Storage::disk($disk)->url($storagePath);

            // For S3, if AWS_URL is not set, construct URL manually
            if ($disk === 's3' && ! config('filesystems.disks.s3.url')) {
                $region = config('filesystems.disks.s3.region');
                $bucket = config('filesystems.disks.s3.bucket');
                $url = "https://{$bucket}.s3.{$region}.amazonaws.com/{$storagePath}";
            }

            Log::info('Image saved to storage', [
                'provider' => $provider,
                'path' => $storagePath,
                'disk' => $disk,
            ]);

            return [
                'url' => $url,
                'path' => $storagePath,
                'disk' => $disk,
                'provider' => $provider,
                'revised_prompt' => $result['revised_prompt'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to save image to storage', [
                'provider' => $provider,
                'path' => $storagePath,
                'disk' => $disk,
                'error' => $e->getMessage(),
            ]);

            throw new \Exception("Failed to save image to storage: {$e->getMessage()}", 0, $e);
        }
    }
}
