<?php

use App\Console\Commands\GenerateResources;
use App\Models\Resource;
use App\Services\ResourceGenerationService;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->resourceGenerator = Mockery::mock(ResourceGenerationService::class);
    $this->app->instance(ResourceGenerationService::class, $this->resourceGenerator);
});

it('generates avatar resources successfully', function () {
    $this->resourceGenerator
        ->shouldReceive('generateAvatarTemplate')
        ->times(3)
        ->andReturn(Resource::factory()->create(['type' => 'avatar_image']));

    $exitCode = Artisan::call('resources:generate', [
        '--type' => 'avatar_image',
        '--count' => 3,
    ]);

    $output = Artisan::output();
    expect($output)->toContain('Generating 3 resource(s)')
        ->and($output)->toContain('Generation complete! Generated: 3')
        ->and($exitCode)->toBe(0);
});

it('generates planet image resources successfully', function () {
    $this->resourceGenerator
        ->shouldReceive('generatePlanetImageTemplate')
        ->times(2)
        ->andReturn(Resource::factory()->create(['type' => 'planet_image']));

    $exitCode = Artisan::call('resources:generate', [
        '--type' => 'planet_image',
        '--count' => 2,
    ]);

    $output = Artisan::output();
    expect($output)->toContain('Generating 2 resource(s)')
        ->and($output)->toContain('Generation complete! Generated: 2')
        ->and($exitCode)->toBe(0);
});

it('generates planet video resources successfully', function () {
    $this->resourceGenerator
        ->shouldReceive('generatePlanetVideoTemplate')
        ->times(2)
        ->andReturn(Resource::factory()->create(['type' => 'planet_video']));

    $exitCode = Artisan::call('resources:generate', [
        '--type' => 'planet_video',
        '--count' => 2,
    ]);

    $output = Artisan::output();
    expect($output)->toContain('Generating 2 resource(s)')
        ->and($output)->toContain('Generation complete! Generated: 2')
        ->and($exitCode)->toBe(0);
});

it('generates all resource types when no type specified', function () {
    $this->resourceGenerator
        ->shouldReceive('generateAvatarTemplate')
        ->times(2)
        ->andReturn(Resource::factory()->create(['type' => 'avatar_image']));
    $this->resourceGenerator
        ->shouldReceive('generatePlanetImageTemplate')
        ->times(2)
        ->andReturn(Resource::factory()->create(['type' => 'planet_image']));
    $this->resourceGenerator
        ->shouldReceive('generatePlanetVideoTemplate')
        ->times(2)
        ->andReturn(Resource::factory()->create(['type' => 'planet_video']));

    $exitCode = Artisan::call('resources:generate', ['--count' => 2]);

    $output = Artisan::output();
    expect($output)->toContain('avatar_image')
        ->and($output)->toContain('planet_image')
        ->and($output)->toContain('planet_video')
        ->and($exitCode)->toBe(0);
});

it('validates count option minimum value', function () {
    $exitCode = Artisan::call('resources:generate', ['--count' => 0]);

    expect(Artisan::output())->toContain('Count must be between 1 and 50')
        ->and($exitCode)->toBe(1);
});

it('validates count option maximum value', function () {
    $exitCode = Artisan::call('resources:generate', ['--count' => 51]);

    expect(Artisan::output())->toContain('Count must be between 1 and 50')
        ->and($exitCode)->toBe(1);
});

it('validates resource type', function () {
    $exitCode = Artisan::call('resources:generate', [
        '--type' => 'invalid_type',
        '--count' => 1,
    ]);

    expect(Artisan::output())->toContain('Invalid type')
        ->and($exitCode)->toBe(1);
});

it('handles errors during generation', function () {
    $this->resourceGenerator
        ->shouldReceive('generateAvatarTemplate')
        ->once()
        ->andThrow(new \Exception('Generation error'));

    $exitCode = Artisan::call('resources:generate', [
        '--type' => 'avatar_image',
        '--count' => 1,
    ]);

    $output = Artisan::output();
    expect($output)->toContain('Failed to generate avatar_image')
        ->and($output)->toContain('Generation complete! Generated: 0, Failed: 1')
        ->and($exitCode)->toBe(0);
});

it('generates prompts with variety', function () {
    $command = new GenerateResources;
    $reflection = new ReflectionClass($command);

    $avatarMethod = $reflection->getMethod('generateAvatarPrompt');
    $avatarMethod->setAccessible(true);
    $avatarPrompt = $avatarMethod->invoke($command, 0);

    $planetMethod = $reflection->getMethod('generatePlanetImagePrompt');
    $planetMethod->setAccessible(true);
    $planetPrompt = $planetMethod->invoke($command, 0);

    $videoMethod = $reflection->getMethod('generatePlanetVideoPrompt');
    $videoMethod->setAccessible(true);
    $videoPrompt = $videoMethod->invoke($command, 0);

    expect($avatarPrompt)->toBeString()->toContain('space')
        ->and($planetPrompt)->toBeString()->toContain('planet')
        ->and($videoPrompt)->toBeString()->toContain('planet');
});

afterEach(function () {
    \Mockery::close();
});
