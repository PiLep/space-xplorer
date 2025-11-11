<?php

use App\Console\Commands\GenerateDailyAvatarResources;
use App\Jobs\GenerateResourceJob;
use App\Models\Resource;
use App\Services\ResourceGenerationService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();
    $this->resourceGenerator = Mockery::mock(ResourceGenerationService::class);
    $this->app->instance(ResourceGenerationService::class, $this->resourceGenerator);
});

it('generates daily avatar resources successfully', function () {
    Log::shouldReceive('info')->zeroOrMoreTimes();

    $this->resourceGenerator
        ->shouldReceive('extractAvatarTagsFromPrompt')
        ->times(5)
        ->andReturn(['space', 'technician']);

    Artisan::call('resources:generate-daily-avatars', ['--count' => 5]);

    $output = Artisan::output();
    expect($output)->toContain('Generating 5 avatar image resources')
        ->and($output)->toContain('Daily generation complete! Created: 5')
        ->and(Resource::where('type', 'avatar_image')->count())->toBe(5)
        ->and(Queue::pushed(GenerateResourceJob::class))->toHaveCount(5);
});

it('validates count option minimum value', function () {
    $exitCode = Artisan::call('resources:generate-daily-avatars', ['--count' => 0]);

    expect(Artisan::output())->toContain('Count must be between 1 and 50')
        ->and($exitCode)->toBe(1);
});

it('validates count option maximum value', function () {
    $exitCode = Artisan::call('resources:generate-daily-avatars', ['--count' => 51]);

    expect(Artisan::output())->toContain('Count must be between 1 and 50')
        ->and($exitCode)->toBe(1);
});

it('handles errors during resource creation', function () {
    $this->resourceGenerator
        ->shouldReceive('extractAvatarTagsFromPrompt')
        ->once()
        ->andThrow(new \Exception('Service error'));

    Log::shouldReceive('error')->once();
    Log::shouldReceive('info')->zeroOrMoreTimes();

    Artisan::call('resources:generate-daily-avatars', ['--count' => 1]);

    $output = Artisan::output();
    expect($output)->toContain('Failed to create resource')
        ->and($output)->toContain('Daily generation complete! Created: 0, Failed: 1');
});

it('creates resources with correct metadata', function () {
    $this->resourceGenerator
        ->shouldReceive('extractAvatarTagsFromPrompt')
        ->once()
        ->andReturn(['space', 'captain']);

    Artisan::call('resources:generate-daily-avatars', ['--count' => 1]);

    $resource = Resource::where('type', 'avatar_image')->first();

    expect($resource)->not->toBeNull()
        ->and($resource->status)->toBe('generating')
        ->and($resource->metadata['auto_generated'])->toBeTrue()
        ->and($resource->metadata['scheduled_generation'])->toBeTrue()
        ->and($resource->created_by)->toBeNull();
});

it('generates varied avatar prompts', function () {
    $command = new GenerateDailyAvatarResources;
    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('generateVariedAvatarPrompts');
    $method->setAccessible(true);

    $prompts = $method->invoke($command, 10);

    expect($prompts)->toHaveCount(10)
        ->and($prompts[0])->toBeString()
        ->and($prompts[0])->toContain('Single person only');

    // Verify that at least some prompts contain "space" (not all prompts need it, but many should)
    $promptsWithSpace = array_filter($prompts, fn ($prompt) => str_contains(strtolower($prompt), 'space'));
    expect($promptsWithSpace)->not->toBeEmpty('At least some prompts should contain "space"');
});

afterEach(function () {
    \Mockery::close();
});
