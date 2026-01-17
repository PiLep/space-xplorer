<?php

use App\Models\Planet;
use App\Services\AIDescriptionService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->service = new AIDescriptionService;
    $this->planet = Planet::factory()->create();
    // Update existing properties created by factory instead of creating new ones
    $this->planet->properties()->updateOrCreate(
        ['planet_id' => $this->planet->id],
        [
            'type' => 'terrestrial',
            'size' => 'medium',
            'temperature' => 'temperate',
            'atmosphere' => 'breathable',
            'terrain' => 'rocky',
            'resources' => 'moderate',
        ]
    );
    $this->planet->refresh();
});

it('throws exception when provider is not configured', function () {
    config(['text-generation.providers.openai.api_key' => null]);

    expect(fn () => $this->service->generatePlanetDescription($this->planet))
        ->toThrow(\App\Exceptions\ProviderConfigurationException::class);
});

it('checks if provider is configured correctly', function () {
    config(['text-generation.providers.openai.api_key' => 'test-key']);

    expect($this->service->isProviderConfigured('openai'))->toBeTrue();
    expect($this->service->isProviderConfigured('nonexistent'))->toBeFalse();
});

it('builds a prompt from planet characteristics', function () {
    $prompt = $this->service->buildPrompt($this->planet);

    expect($prompt)->toBeString()
        ->and($prompt)->toContain('terrestrial')
        ->and($prompt)->toContain('medium')
        ->and($prompt)->toContain('temperate');
});

it('generates fallback description when AI generation fails', function () {
    config([
        'text-generation.providers.openai.api_key' => 'test-key',
        'text-generation.retry_attempts' => 1, // Reduce retries for faster test
    ]);
    Http::fake([
        '*' => Http::response(['error' => 'API Error'], 500),
    ]);

    // After retries fail, the service should use fallback description
    $description = $this->service->generatePlanetDescription($this->planet);

    expect($description)->toBeString()
        ->and($description)->toContain('terrestrial')
        ->and($description)->toContain('medium');
});

it('caches generated descriptions', function () {
    config([
        'text-generation.providers.openai.api_key' => 'test-key',
        'text-generation.cache.enabled' => true,
    ]);

    Cache::shouldReceive('get')->once()->andReturn(null);
    Cache::shouldReceive('put')->once();

    Http::fake([
        '*' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => 'A beautiful planet with diverse ecosystems.',
                    ],
                ],
            ],
        ], 200),
    ]);

    $this->service->generatePlanetDescription($this->planet);
});

it('uses cached description when available', function () {
    config([
        'text-generation.providers.openai.api_key' => 'test-key',
        'text-generation.cache.enabled' => true,
    ]);

    $cachedDescription = 'Cached description';
    Cache::shouldReceive('get')->once()->andReturn($cachedDescription);

    $description = $this->service->generatePlanetDescription($this->planet);

    expect($description)->toBe($cachedDescription);
});

