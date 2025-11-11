<?php

namespace Tests;

use App\Services\ImageGenerationService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

abstract class TestCase extends BaseTestCase
{
    /**
     * Mock ImageGenerationService to avoid real API calls in tests.
     * This should be called in tests that create users, as user creation
     * triggers the GenerateAvatar listener which calls this service.
     */
    protected function mockImageGenerationService(): void
    {
        Queue::fake();
        Storage::fake('s3');

        $mockGenerator = \Mockery::mock(ImageGenerationService::class);
        $mockGenerator->shouldReceive('generate')
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any())
            ->andReturn([
                'url' => 'https://s3.example.com/avatar.png',
                'path' => 'images/generated/avatars/avatar.png',
                'disk' => 's3',
                'provider' => 'openai',
            ]);

        $this->app->instance(ImageGenerationService::class, $mockGenerator);
    }
}
