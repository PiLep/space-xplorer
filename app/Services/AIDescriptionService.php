<?php

namespace App\Services;

use App\Exceptions\ApiRequestException;
use App\Exceptions\ProviderConfigurationException;
use App\Exceptions\UnsupportedProviderException;
use App\Models\Planet;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for AI-powered text generation (descriptions).
 *
 * This service generates text descriptions from prompts using various AI providers
 * (OpenAI GPT, etc.) via their REST APIs.
 */
class AIDescriptionService
{
    /**
     * Generate a planet description using AI based on planet characteristics.
     *
     * @param  Planet  $planet  The planet to generate a description for
     * @param  string|null  $provider  Optional provider name (defaults to config default)
     * @return string The generated description
     *
     * @throws ApiRequestException If API request fails
     * @throws ProviderConfigurationException If provider is not configured
     * @throws UnsupportedProviderException If provider is not supported
     */
    public function generatePlanetDescription(Planet $planet, ?string $provider = null): string
    {
        $provider = $provider ?? config('text-generation.default_provider');

        // Check cache first (as per architectural recommendation)
        if (config('text-generation.cache.enabled')) {
            $cacheKey = $this->getCacheKey($planet, $provider);
            $cached = Cache::get($cacheKey);

            if ($cached !== null) {
                Log::info('Using cached description for planet', [
                    'planet_id' => $planet->id,
                    'provider' => $provider,
                ]);

                return $cached;
            }
        }

        if (! $this->isProviderConfigured($provider)) {
            throw new ProviderConfigurationException($provider);
        }

        try {
            $prompt = $this->buildPrompt($planet);
            $description = $this->generateWithRetry($prompt, $provider);

            // Cache the result (as per architectural recommendation)
            if (config('text-generation.cache.enabled')) {
                $cacheKey = $this->getCacheKey($planet, $provider);
                $ttl = config('text-generation.cache.ttl', 86400);
                Cache::put($cacheKey, $description, $ttl);
            }

            return $description;
        } catch (RequestException $e) {
            Log::error('Text generation API request failed', [
                'provider' => $provider,
                'planet_id' => $planet->id,
                'error' => $e->getMessage(),
                'response' => $e->response?->json(),
            ]);

            throw new ApiRequestException("Failed to generate description: {$e->getMessage()}", 0, $e);
        } catch (ConnectionException $e) {
            Log::error('Text generation API connection failed', [
                'provider' => $provider,
                'planet_id' => $planet->id,
                'error' => $e->getMessage(),
            ]);

            throw new ApiRequestException("Failed to connect to text generation service: {$e->getMessage()}", 0, $e);
        } catch (ProviderConfigurationException|UnsupportedProviderException|ApiRequestException $e) {
            // Re-throw custom exceptions as-is
            throw $e;
        } catch (\Exception $e) {
            Log::error('Text generation failed', [
                'provider' => $provider,
                'planet_id' => $planet->id,
                'error' => $e->getMessage(),
            ]);

            // Fallback to template-based description if generation fails
            return $this->generateFallbackDescription($planet);
        }
    }

    /**
     * Build a prompt for AI text generation based on planet characteristics.
     *
     * @param  Planet  $planet  The planet to build a prompt for
     * @return string The constructed prompt
     */
    public function buildPrompt(Planet $planet): string
    {
        $properties = $planet->properties;

        if (! $properties) {
            return $this->generateFallbackDescription($planet);
        }

        $characteristics = [
            'Type' => $properties->type ?? 'Unknown',
            'Size' => $properties->size ?? 'Unknown',
            'Temperature' => $properties->temperature ?? 'Unknown',
            'Atmosphere' => $properties->atmosphere ?? 'Unknown',
            'Terrain' => $properties->terrain ?? 'Unknown',
            'Resources' => $properties->resources ?? 'Unknown',
        ];

        $prompt = 'Write a scientific and engaging description for a planet in a space exploration game. ';
        $prompt .= "The planet has the following characteristics:\n\n";
        foreach ($characteristics as $key => $value) {
            $prompt .= "- {$key}: {$value}\n";
        }
        $prompt .= "\n";
        $prompt .= 'Write a 3-5 sentence description that combines scientific accuracy with engaging narrative. ';
        $prompt .= "Describe the planet's appearance, atmosphere, terrain, and potential for exploration. ";
        $prompt .= 'Use a tone that is both informative and inspiring, suitable for a space encyclopedia entry.';

        return $prompt;
    }

    /**
     * Generate text using OpenAI GPT with retry logic.
     *
     * @param  string  $prompt  The text prompt
     * @param  string  $provider  Provider name
     * @return string The generated text
     */
    private function generateWithRetry(string $prompt, string $provider): string
    {
        $maxAttempts = config('text-generation.retry_attempts', 3);
        $retryDelay = config('text-generation.retry_delay', 2);

        $lastException = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                return match ($provider) {
                    'openai' => $this->generateWithOpenAI($prompt),
                    default => throw new UnsupportedProviderException($provider, 'text generation'),
                };
            } catch (RequestException|ConnectionException $e) {
                $lastException = $e;

                if ($attempt < $maxAttempts) {
                    Log::warning('Text generation attempt failed, retrying', [
                        'provider' => $provider,
                        'attempt' => $attempt,
                        'max_attempts' => $maxAttempts,
                        'error' => $e->getMessage(),
                    ]);

                    sleep($retryDelay * $attempt); // Exponential backoff
                }
            }
        }

        // All retries failed
        throw new ApiRequestException(
            "Text generation failed after {$maxAttempts} attempts: {$lastException->getMessage()}",
            0,
            $lastException
        );
    }

    /**
     * Generate text using OpenAI GPT.
     *
     * @param  string  $prompt  The text prompt
     * @return string The generated text
     */
    private function generateWithOpenAI(string $prompt): string
    {
        $config = config('text-generation.providers.openai');
        $timeout = config('text-generation.timeout');

        $response = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => "Bearer {$config['api_key']}",
                'Content-Type' => 'application/json',
            ])
            ->post($config['endpoint'], [
                'model' => $config['model'],
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a scientific writer specializing in space exploration and planetary descriptions.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => $config['temperature'],
                'max_tokens' => $config['max_tokens'],
            ])
            ->throw()
            ->json();

        // OpenAI Chat API returns: { "choices": [{ "message": { "content": "..." } }] }
        if (! isset($response['choices'][0]['message']['content'])) {
            throw new ApiRequestException('Invalid response from OpenAI API: missing content');
        }

        return trim($response['choices'][0]['message']['content']);
    }

    /**
     * Generate a fallback description using a template (when AI generation fails).
     *
     * @param  Planet  $planet  The planet to generate a description for
     * @return string The fallback description
     */
    private function generateFallbackDescription(Planet $planet): string
    {
        $properties = $planet->properties;

        if (! $properties) {
            return 'A mysterious planet discovered in the depths of space. Its characteristics remain largely unknown, awaiting further exploration and study.';
        }

        $type = $properties->type ?? 'Unknown';
        $size = $properties->size ?? 'Unknown';
        $temperature = $properties->temperature ?? 'Unknown';
        $atmosphere = $properties->atmosphere ?? 'Unknown';
        $terrain = $properties->terrain ?? 'Unknown';

        return "This {$type} planet is classified as {$size} in size with a {$temperature} climate. "
            ."The atmosphere is {$atmosphere}, and the surface terrain consists primarily of {$terrain}. "
            ."Further exploration is needed to fully understand this celestial body's unique characteristics and potential for scientific discovery.";
    }

    /**
     * Get cache key for planet description.
     *
     * @param  Planet  $planet  The planet
     * @param  string  $provider  Provider name
     * @return string Cache key
     */
    private function getCacheKey(Planet $planet, string $provider): string
    {
        $prefix = config('text-generation.cache.prefix', 'text_generation:');
        $planetHash = md5($planet->id.(string) $planet->updated_at);

        return "{$prefix}planet:{$planetHash}:provider:{$provider}";
    }

    /**
     * Check if a provider is properly configured.
     *
     * @param  string  $provider  Provider name
     * @return bool True if provider is configured and has API key
     */
    public function isProviderConfigured(string $provider): bool
    {
        $providers = config('text-generation.providers');

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
        $providers = config('text-generation.providers');
        $available = [];

        foreach ($providers as $name => $config) {
            if ($this->isProviderConfigured($name)) {
                $available[] = $name;
            }
        }

        return $available;
    }
}

