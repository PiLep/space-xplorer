<?php

use App\Services\ImageGenerationService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

/**
 * Mock ImageGenerationService automatiquement pour tous les tests Feature.
 *
 * Même configuration que pour l'avatar : Queue::fake() fake uniquement les listeners
 * qui implémentent ShouldQueue (GenerateAvatar, GeneratePlanetImage, GeneratePlanetVideo).
 * Les listeners synchrones (GenerateHomePlanet) s'exécutent normalement.
 */
beforeEach(function () {
    // Queue::fake() fake automatiquement les listeners avec ShouldQueue
    // Les listeners synchrones (comme GenerateHomePlanet) s'exécutent normalement
    Queue::fake();
    Storage::fake('s3');

    $mockGenerator = \Mockery::mock(ImageGenerationService::class);
    $mockGenerator->shouldReceive('generate')
        ->zeroOrMoreTimes()
        ->with(Mockery::any(), Mockery::any(), Mockery::any())
        ->andReturn([
            'url' => 'https://s3.example.com/avatar.png',
            'path' => 'images/generated/avatars/avatar.png',
            'disk' => 's3',
            'provider' => 'openai',
        ]);

    $this->app->instance(ImageGenerationService::class, $mockGenerator);
});

afterEach(function () {
    \Mockery::close();
});
