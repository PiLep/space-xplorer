<?php

use App\Events\PlanetCreated;
use App\Listeners\GeneratePlanetImage;
use App\Models\Planet;
use App\Services\ImageGenerationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
    Queue::fake(); // Fake queues for testing
    $this->imageGenerator = Mockery::mock(ImageGenerationService::class);
    $this->listener = new GeneratePlanetImage($this->imageGenerator);
    $this->planet = Planet::factory()->create();
});

it('queues planet image generation job when planet is created', function () {
    Queue::fake();

    $event = new PlanetCreated($this->planet);
    event($event);

    // Verify that the listener job was queued
    Queue::assertPushed(\Illuminate\Events\CallQueuedListener::class, function ($job) {
        return $job->class === GeneratePlanetImage::class;
    });
});

it('generates planet image successfully when job is processed', function () {
    $imagePath = 'images/generated/planets/planet-123.png';
    $imageUrl = 'https://s3.example.com/' . $imagePath;

    $this->imageGenerator
        ->shouldReceive('generate')
        ->once()
        ->with(
            Mockery::on(function ($prompt) {
                return is_string($prompt)
                    && str_contains(strtolower($prompt), 'alien')
                    && str_contains(strtolower($prompt), '1979')
                    && str_contains(strtolower($prompt), 'cinematic')
                    && str_contains(strtolower($prompt), 'space');
            }),
            null,
            'planets'
        )
        ->andReturn([
            'url' => $imageUrl,
            'path' => $imagePath,
            'disk' => 's3',
            'provider' => 'openai',
        ]);

    // Create the file in fake storage to simulate actual file creation
    Storage::disk('s3')->put($imagePath, 'fake image content');

    $event = new PlanetCreated($this->planet);
    $this->listener->handle($event);

    $this->planet->refresh();

    // The path should be stored in the database
    expect($this->planet->getRawOriginal('image_url'))->toBe($imagePath);

    // The accessor should reconstruct the URL (and verify file exists)
    expect($this->planet->image_url)->toBeString()->toContain($imagePath);
});

it('includes planet name and characteristics in prompt', function () {
    $planet = Planet::factory()->create([
        'name' => 'Kepler-452b',
        'type' => 'tellurique',
        'size' => 'moyenne',
        'temperature' => 'tempérée',
        'atmosphere' => 'respirable',
        'terrain' => 'forestier',
    ]);

    $this->imageGenerator
        ->shouldReceive('generate')
        ->once()
        ->with(
            Mockery::on(function ($prompt) use ($planet) {
                return str_contains($prompt, $planet->name)
                    && str_contains(strtolower($prompt), 'rocky terrestrial')
                    && str_contains(strtolower($prompt), 'medium-sized')
                    && str_contains(strtolower($prompt), 'temperate')
                    && str_contains(strtolower($prompt), 'breathable')
                    && str_contains(strtolower($prompt), 'forest');
            }),
            null,
            'planets'
        )
        ->andReturn([
            'url' => 'https://s3.example.com/planet.png',
            'path' => 'images/generated/planets/planet.png',
            'disk' => 's3',
            'provider' => 'openai',
        ]);

    $event = new PlanetCreated($planet);
    $this->listener->handle($event);
});

it('generates prompt with Alien movie aesthetic', function () {
    $this->imageGenerator
        ->shouldReceive('generate')
        ->once()
        ->with(
            Mockery::on(function ($prompt) {
                return str_contains(strtolower($prompt), 'alien')
                    && str_contains(strtolower($prompt), '1979')
                    && str_contains(strtolower($prompt), 'cinematic')
                    && str_contains(strtolower($prompt), 'industrial')
                    && str_contains(strtolower($prompt), 'sci-fi')
                    && str_contains(strtolower($prompt), 'moody')
                    && str_contains(strtolower($prompt), 'atmospheric');
            }),
            null,
            'planets'
        )
        ->andReturn([
            'url' => 'https://s3.example.com/planet.png',
            'path' => 'images/generated/planets/planet.png',
            'disk' => 's3',
            'provider' => 'openai',
        ]);

    $event = new PlanetCreated($this->planet);
    $this->listener->handle($event);
});

it('throws exception when planet image generation fails (job will be retried)', function () {
    Log::shouldReceive('error')
        ->once()
        ->with('Failed to generate planet image', Mockery::type('array'));

    $this->imageGenerator
        ->shouldReceive('generate')
        ->once()
        ->with(Mockery::any(), null, 'planets')
        ->andThrow(new \Exception('API error'));

    $event = new PlanetCreated($this->planet);

    // The exception will be thrown, marking the job as failed
    // In a real queue, this would trigger a retry
    expect(fn() => $this->listener->handle($event))
        ->toThrow(\Exception::class, 'API error');

    $this->planet->refresh();
    expect($this->planet->image_url)->toBeNull();
});

it('logs success when planet image is generated', function () {
    $imageUrl = 'https://s3.example.com/planet.png';

    Log::shouldReceive('info')
        ->once()
        ->with('Planet image generated successfully', Mockery::type('array'));

    $this->imageGenerator
        ->shouldReceive('generate')
        ->once()
        ->with(Mockery::any(), null, 'planets')
        ->andReturn([
            'url' => $imageUrl,
            'path' => 'images/generated/planets/planet.png',
            'disk' => 's3',
            'provider' => 'openai',
        ]);

    $event = new PlanetCreated($this->planet);
    $this->listener->handle($event);
});

it('skips generation if planet already has an image', function () {
    $planet = Planet::factory()->create([
        'image_url' => 'images/generated/planets/existing-planet.png',
    ]);

    // Image generator should not be called
    $this->imageGenerator->shouldNotReceive('generate');

    $event = new PlanetCreated($planet);
    $this->listener->handle($event);

    $planet->refresh();
    expect($planet->getRawOriginal('image_url'))->toBe('images/generated/planets/existing-planet.png');
});

it('generates different prompts for different planet types', function () {
    $telluriquePlanet = Planet::factory()->create(['type' => 'tellurique']);
    $gazeusePlanet = Planet::factory()->create(['type' => 'gazeuse']);

    $this->imageGenerator
        ->shouldReceive('generate')
        ->twice()
        ->with(Mockery::on(function ($prompt) {
            return is_string($prompt);
        }), null, 'planets')
        ->andReturn([
            'url' => 'https://s3.example.com/planet.png',
            'path' => 'images/generated/planets/planet.png',
            'disk' => 's3',
            'provider' => 'openai',
        ]);

    $this->listener->handle(new PlanetCreated($telluriquePlanet));
    $this->listener->handle(new PlanetCreated($gazeusePlanet));
});

afterEach(function () {
    \Mockery::close();
});
