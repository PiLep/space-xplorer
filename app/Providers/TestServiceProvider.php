<?php

namespace App\Providers;

use App\Services\ImageGenerationService;
use Illuminate\Support\ServiceProvider;
use Mockery;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Mock ImageGenerationService if API key is not configured (E2E tests, CI)
        // or if we're in testing environment
        $isTesting = $this->app->environment('testing');
        $hasApiKey = ! empty(config('image-generation.providers.openai.api_key'));

        if (! $isTesting && $hasApiKey) {
            return;
        }

        // Mock ImageGenerationService to avoid real API calls in E2E tests
        $this->app->singleton(ImageGenerationService::class, function ($app) {
            $mock = Mockery::mock(ImageGenerationService::class);
            $mock->shouldReceive('generate')
                ->zeroOrMoreTimes()
                ->with(Mockery::any(), Mockery::any(), Mockery::any())
                ->andReturnUsing(function ($prompt, $provider, $subfolder) {
                    // Determine path based on subfolder
                    $folder = $subfolder ?? 'generated';
                    $filename = 'test-'.uniqid().'.png';
                    $path = "images/generated/{$folder}/{$filename}";

                    return [
                        'url' => "https://s3.example.com/{$path}",
                        'path' => $path,
                        'disk' => 's3',
                        'provider' => $provider ?? 'openai',
                    ];
                });

            return $mock;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
