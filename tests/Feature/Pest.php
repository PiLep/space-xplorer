<?php

use App\Services\ImageGenerationService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

/**
 * Mock ImageGenerationService automatiquement pour tous les tests Feature.
 *
 * On utilise Queue::fake() pour éviter que les jobs soient réellement mis en queue,
 * mais les listeners synchrones (comme GenerateHomePlanet) doivent toujours s'exécuter.
 * Les listeners qui implémentent ShouldQueue seront fake, mais les autres s'exécuteront normalement.
 */
beforeEach(function () {
    // Fake queues pour les listeners qui implémentent ShouldQueue
    // Mais les listeners synchrones (sans ShouldQueue) continueront de s'exécuter
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
