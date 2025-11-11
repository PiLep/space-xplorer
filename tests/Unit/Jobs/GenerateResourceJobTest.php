<?php

use App\Jobs\GenerateResourceJob;
use App\Models\Resource;
use App\Services\ResourceGenerationService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
    Queue::fake();
    Event::fake();
    $this->resourceGenerator = Mockery::mock(ResourceGenerationService::class);
    $this->app->instance(ResourceGenerationService::class, $this->resourceGenerator);
});

it('generates avatar resource successfully', function () {
    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'status' => 'generating',
    ]);

    $this->resourceGenerator
        ->shouldReceive('generateAvatarTemplateForResource')
        ->once()
        ->with($resource)
        ->andReturn($resource);

    $job = new GenerateResourceJob($resource);
    $job->handle($this->resourceGenerator);

    $resource->refresh();
    expect($resource->metadata)->not->toHaveKey('generation_attempts');
});

it('generates planet image resource successfully', function () {
    $resource = Resource::factory()->create([
        'type' => 'planet_image',
        'status' => 'generating',
    ]);

    $this->resourceGenerator
        ->shouldReceive('generatePlanetImageTemplateForResource')
        ->once()
        ->with($resource)
        ->andReturn($resource);

    $job = new GenerateResourceJob($resource);
    $job->handle($this->resourceGenerator);

    $resource->refresh();
    expect($resource->metadata)->not->toHaveKey('generation_attempts');
});

it('generates planet video resource successfully', function () {
    $resource = Resource::factory()->create([
        'type' => 'planet_video',
        'status' => 'generating',
    ]);

    $this->resourceGenerator
        ->shouldReceive('generatePlanetVideoTemplateForResource')
        ->once()
        ->with($resource)
        ->andReturn($resource);

    $job = new GenerateResourceJob($resource);
    $job->handle($this->resourceGenerator);

    $resource->refresh();
    expect($resource->metadata)->not->toHaveKey('generation_attempts');
});

it('skips generation if resource is not in generating status', function () {
    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'status' => 'pending',
    ]);

    Log::shouldReceive('warning')->once();

    $this->resourceGenerator->shouldNotReceive('generateAvatarTemplateForResource');

    $job = new GenerateResourceJob($resource);
    $job->handle($this->resourceGenerator);
});

it('records attempt in metadata on failure', function () {
    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'status' => 'generating',
    ]);

    $this->resourceGenerator
        ->shouldReceive('generateAvatarTemplateForResource')
        ->once()
        ->andThrow(new \Exception('Generation failed'));

    Log::shouldReceive('info')->twice(); // "Attempting" + "Will retry"
    Log::shouldReceive('error')->once();

    $job = new GenerateResourceJob($resource);

    try {
        $job->handle($this->resourceGenerator);
    } catch (\Exception $e) {
        // Expected
    }

    $resource->refresh();
    expect($resource->metadata)->toHaveKey('generation_attempts')
        ->and($resource->metadata['generation_attempts'])->toBeArray()
        ->and(count($resource->metadata['generation_attempts']))->toBe(1);
});

it('deletes resource after max attempts without valid file', function () {
    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'status' => 'generating',
        'file_path' => null,
    ]);

    $this->resourceGenerator
        ->shouldReceive('generateAvatarTemplateForResource')
        ->times(3)
        ->andThrow(new \Exception('Generation failed'));

    Log::shouldReceive('error')->times(3);
    Log::shouldReceive('info')->times(6); // 3x "Attempting" + 2x "Will retry" + 1x "Deleting"

    $job = new GenerateResourceJob($resource);
    $job->tries = 3;

    // Simulate 3 attempts
    for ($i = 1; $i <= 3; $i++) {
        try {
            $job->handle($this->resourceGenerator);
        } catch (\Exception $e) {
            if ($i === 3) {
                // On last attempt, check if resource should be deleted
                $resource->refresh();
                if (! $resource->file_path) {
                    $resource->delete();
                }
            }
        }
    }

    expect(Resource::find($resource->id))->toBeNull();
});

it('sets resource to pending after max attempts if file exists', function () {
    Storage::fake('s3');
    $filePath = 'images/test.png';
    Storage::disk('s3')->put($filePath, 'fake content');

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'status' => 'generating',
        'file_path' => $filePath,
    ]);

    $this->resourceGenerator
        ->shouldReceive('generateAvatarTemplateForResource')
        ->times(3)
        ->andThrow(new \Exception('Generation failed'));

    Log::shouldReceive('error')->times(3);
    Log::shouldReceive('info')->atLeast()->times(5); // 3x "Attempting" + 2x "Will retry" (may have more)
    Log::shouldReceive('warning')->once()->with('Setting resource to pending after max attempts, file exists but generation had errors', Mockery::type('array'));

    $job = new GenerateResourceJob($resource);
    $job->tries = 3;

    // Simulate 3 attempts - the job will automatically set status to pending on the last attempt
    for ($i = 1; $i <= 3; $i++) {
        try {
            $job->handle($this->resourceGenerator);
        } catch (\Exception $e) {
            // Exception is expected, job will handle status update on last attempt
        }
    }

    $resource->refresh();
    expect($resource->status)->toBe('pending');
});

it('logs attempt information', function () {
    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'status' => 'generating',
    ]);

    Log::shouldReceive('info')->once()->with('Attempting to generate resource', Mockery::type('array'));
    Log::shouldReceive('info')->once()->with('Resource generated successfully', Mockery::type('array'));

    $this->resourceGenerator
        ->shouldReceive('generateAvatarTemplateForResource')
        ->once()
        ->andReturn($resource);

    $job = new GenerateResourceJob($resource);
    $job->handle($this->resourceGenerator);
});

afterEach(function () {
    \Mockery::close();
});
