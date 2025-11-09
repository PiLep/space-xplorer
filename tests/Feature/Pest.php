<?php

use App\Services\ImageGenerationService;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

/**
 * Mock ImageGenerationService automatiquement pour tous les tests Feature.
 *
 * Avec QUEUE_CONNECTION=null dans phpunit.xml, les listeners en queue ne s'exécutent pas,
 * mais on mock quand même le service au cas où.
 */
beforeEach(function () {
    Queue::fake();
    Storage::fake('s3');

    $mockGenerator = \Mockery::mock(ImageGenerationService::class);
    $mockGenerator->shouldReceive('generate')
        ->zeroOrMoreTimes()
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
