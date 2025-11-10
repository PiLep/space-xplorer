<?php

use App\Services\ImageGenerationService;
use App\Services\VideoGenerationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Mock ImageGenerationService et VideoGenerationService automatiquement pour tous les tests Feature.
 *
 * IMPORTANT: On n'utilise PAS Queue::fake() car cela empêche l'exécution des listeners ShouldQueue
 * même avec QUEUE_CONNECTION=sync. À la place, on mock directement les services pour éviter
 * les appels API réels, tout en permettant l'exécution synchrone des listeners.
 *
 * PROTECTION: Http::preventStrayRequests() bloque tous les appels HTTP non mockés pour éviter
 * les timeouts et les appels externes accidentels dans les tests.
 *
 * Les listeners synchrones (GenerateHomePlanet) s'exécutent normalement.
 * Les listeners en queue (GenerateAvatar, GeneratePlanetImage, GeneratePlanetVideo) s'exécutent
 * de manière synchrone grâce à QUEUE_CONNECTION=sync, mais avec les services mockés.
 */
beforeEach(function () {
    // Bloquer tous les appels HTTP non mockés pour éviter les timeouts et appels externes
    Http::preventStrayRequests();

    Storage::fake('s3');

    // Mock ImageGenerationService pour éviter les appels API réels
    $mockImageGenerator = \Mockery::mock(ImageGenerationService::class);
    $mockImageGenerator->shouldReceive('generate')
        ->zeroOrMoreTimes()
        ->with(\Mockery::any(), \Mockery::any(), \Mockery::any())
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

    $this->app->instance(ImageGenerationService::class, $mockImageGenerator);

    // Mock VideoGenerationService pour éviter les appels API réels
    $mockVideoGenerator = \Mockery::mock(VideoGenerationService::class);
    $mockVideoGenerator->shouldReceive('generate')
        ->zeroOrMoreTimes()
        ->with(\Mockery::any(), \Mockery::any(), \Mockery::any())
        ->andReturnUsing(function ($prompt, $provider, $subfolder) {
            // Determine path based on subfolder
            $folder = $subfolder ?? 'generated';
            $filename = 'test-'.uniqid().'.mp4';
            $path = "videos/generated/{$folder}/{$filename}";

            return [
                'url' => "https://s3.example.com/{$path}",
                'path' => $path,
                'disk' => 's3',
                'provider' => $provider ?? 'openai',
            ];
        });

    $this->app->instance(VideoGenerationService::class, $mockVideoGenerator);
});

afterEach(function () {
    \Mockery::close();
});
