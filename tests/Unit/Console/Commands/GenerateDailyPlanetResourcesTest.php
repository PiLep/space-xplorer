<?php

use App\Console\Commands\GenerateDailyPlanetResources;
use App\Jobs\GenerateResourceJob;
use App\Models\Resource;
use App\Services\ResourceGenerationService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();

    // Mock planet config
    Config::set('planets.types', [
        'tellurique' => [
            'characteristics' => [
                'size' => ['petite' => 1, 'moyenne' => 1, 'grande' => 1],
                'temperature' => ['froide' => 1, 'tempérée' => 1, 'chaude' => 1],
                'atmosphere' => ['respirable' => 1, 'toxique' => 1, 'inexistante' => 1],
                'terrain' => ['rocheux' => 1, 'océanique' => 1, 'désertique' => 1],
            ],
        ],
        'gazeuse' => [
            'characteristics' => [
                'size' => ['petite' => 1, 'moyenne' => 1, 'grande' => 1],
                'temperature' => ['froide' => 1, 'tempérée' => 1, 'chaude' => 1],
                'atmosphere' => ['respirable' => 1, 'toxique' => 1, 'inexistante' => 1],
                'terrain' => ['rocheux' => 1, 'océanique' => 1, 'désertique' => 1],
            ],
        ],
        'glacée' => [
            'characteristics' => [
                'size' => ['petite' => 1, 'moyenne' => 1, 'grande' => 1],
                'temperature' => ['froide' => 1, 'tempérée' => 1, 'chaude' => 1],
                'atmosphere' => ['respirable' => 1, 'toxique' => 1, 'inexistante' => 1],
                'terrain' => ['rocheux' => 1, 'océanique' => 1, 'désertique' => 1],
            ],
        ],
        'désertique' => [
            'characteristics' => [
                'size' => ['petite' => 1, 'moyenne' => 1, 'grande' => 1],
                'temperature' => ['froide' => 1, 'tempérée' => 1, 'chaude' => 1],
                'atmosphere' => ['respirable' => 1, 'toxique' => 1, 'inexistante' => 1],
                'terrain' => ['rocheux' => 1, 'océanique' => 1, 'désertique' => 1],
            ],
        ],
        'océanique' => [
            'characteristics' => [
                'size' => ['petite' => 1, 'moyenne' => 1, 'grande' => 1],
                'temperature' => ['froide' => 1, 'tempérée' => 1, 'chaude' => 1],
                'atmosphere' => ['respirable' => 1, 'toxique' => 1, 'inexistante' => 1],
                'terrain' => ['rocheux' => 1, 'océanique' => 1, 'désertique' => 1],
            ],
        ],
    ]);

    $this->resourceGenerator = Mockery::mock(ResourceGenerationService::class);
    $this->app->instance(ResourceGenerationService::class, $this->resourceGenerator);
});

it('generates daily planet resources successfully', function () {
    Log::shouldReceive('info')->zeroOrMoreTimes();

    $this->resourceGenerator
        ->shouldReceive('extractPlanetTagsFromPrompt')
        ->times(5)
        ->andReturn(['planet', 'space']);

    Artisan::call('resources:generate-daily-planets', ['--count' => 5]);

    $output = Artisan::output();
    expect($output)->toContain('Generating 5 planet image resources')
        ->and($output)->toContain('Daily generation complete! Created: 5')
        ->and(Resource::where('type', 'planet_image')->count())->toBe(5)
        ->and(Queue::pushed(GenerateResourceJob::class))->toHaveCount(5);
});

it('validates count option minimum value', function () {
    $exitCode = Artisan::call('resources:generate-daily-planets', ['--count' => 0]);

    expect(Artisan::output())->toContain('Count must be between 1 and 50')
        ->and($exitCode)->toBe(1);
});

it('validates count option maximum value', function () {
    $exitCode = Artisan::call('resources:generate-daily-planets', ['--count' => 51]);

    expect(Artisan::output())->toContain('Count must be between 1 and 50')
        ->and($exitCode)->toBe(1);
});

it('handles errors during resource creation', function () {
    $this->resourceGenerator
        ->shouldReceive('extractPlanetTagsFromPrompt')
        ->once()
        ->andThrow(new \Exception('Service error'));

    Log::shouldReceive('error')->once();
    Log::shouldReceive('info')->zeroOrMoreTimes();

    Artisan::call('resources:generate-daily-planets', ['--count' => 1]);

    $output = Artisan::output();
    expect($output)->toContain('Failed to create resource')
        ->and($output)->toContain('Daily generation complete! Created: 0, Failed: 1');
});

it('creates resources with correct metadata', function () {
    $this->resourceGenerator
        ->shouldReceive('extractPlanetTagsFromPrompt')
        ->once()
        ->andReturn(['planet', 'gas']);

    Artisan::call('resources:generate-daily-planets', ['--count' => 1]);

    $resource = Resource::where('type', 'planet_image')->first();

    expect($resource)->not->toBeNull()
        ->and($resource->status)->toBe('generating')
        ->and($resource->metadata['auto_generated'])->toBeTrue()
        ->and($resource->metadata['scheduled_generation'])->toBeTrue()
        ->and($resource->created_by)->toBeNull();
});

it('generates varied planet prompts', function () {
    $command = new GenerateDailyPlanetResources;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('generateVariedPlanetPrompts');
    $method->setAccessible(true);

    $prompts = $method->invoke($command, 5);

    expect($prompts)->toHaveCount(5)
        ->and($prompts[0])->toBeString()
        ->and($prompts[0])->toContain('planet')
        ->and($prompts[0])->toContain('space');
});

it('builds planet prompt with correct characteristics', function () {
    $command = new GenerateDailyPlanetResources;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('buildPlanetPrompt');
    $method->setAccessible(true);

    $prompt = $method->invoke($command, 'tellurique', 'moyenne', 'tempérée', 'respirable', 'rocheux');

    expect($prompt)->toBeString()
        ->and($prompt)->toContain('rocky terrestrial planet')
        ->and($prompt)->toContain('medium-sized')
        ->and($prompt)->toContain('temperate')
        ->and($prompt)->toContain('breathable')
        ->and($prompt)->toContain('rocky');
});

it('performs weighted random selection', function () {
    $command = new GenerateDailyPlanetResources;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('weightedRandomSelect');
    $method->setAccessible(true);

    $weights = ['option1' => 1, 'option2' => 1, 'option3' => 1];
    $result = $method->invoke($command, $weights);

    expect($result)->toBeIn(['option1', 'option2', 'option3']);
});

afterEach(function () {
    \Mockery::close();
});
