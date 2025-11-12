<?php

use App\Models\Planet;
use App\Services\PlanetGeneratorService;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake(); // Fake events to prevent actual listener execution
    $this->service = new PlanetGeneratorService;
});

it('generates a valid planet with all required fields', function () {
    $planet = $this->service->generate();
    $planet->load('properties');

    expect($planet)
        ->toBeInstanceOf(Planet::class)
        ->and($planet->id)->not->toBeNull()
        ->and($planet->name)->not->toBeNull()
        ->and($planet->type)->not->toBeNull()
        ->and($planet->size)->not->toBeNull()
        ->and($planet->temperature)->not->toBeNull()
        ->and($planet->atmosphere)->not->toBeNull()
        ->and($planet->terrain)->not->toBeNull()
        ->and($planet->resources)->not->toBeNull()
        ->and($planet->description)->not->toBeNull();
});

it('generates a planet with valid type from configuration', function () {
    $validTypes = ['terrestrial', 'gaseous', 'icy', 'desert', 'oceanic']; // English types
    $planet = $this->service->generate();
    $planet->load('properties');

    expect($planet->type)->toBeIn($validTypes);
});

it('generates a planet with valid characteristics for its type', function () {
    $planet = $this->service->generate();
    $planet->load('properties');

    // Valid English values
    $validSizes = ['small', 'medium', 'large'];
    $validTemperatures = ['cold', 'temperate', 'hot'];
    $validAtmospheres = ['breathable', 'toxic', 'nonexistent'];
    $validTerrains = ['rocky', 'oceanic', 'desert', 'forested', 'urban', 'mixed', 'icy'];
    $validResources = ['abundant', 'moderate', 'rare'];

    expect($planet->size)->toBeIn($validSizes)
        ->and($planet->temperature)->toBeIn($validTemperatures)
        ->and($planet->atmosphere)->toBeIn($validAtmospheres)
        ->and($planet->terrain)->toBeIn($validTerrains)
        ->and($planet->resources)->toBeIn($validResources);
});

it('generates unique planet names', function () {
    $names = [];

    // Generate multiple planets
    for ($i = 0; $i < 10; $i++) {
        $planet = $this->service->generate();
        $names[] = $planet->name;
    }

    // Check that all names are unique
    expect($names)->toHaveCount(count(array_unique($names)));
});

it('generates planet names following the expected format', function () {
    $planet = $this->service->generate();
    $prefixes = config('planets.name_prefixes');
    $suffixes = config('planets.name_suffixes');

    // Name should contain a prefix, a number, and a suffix
    // Format: Prefix-NumberSuffix or Prefix-NumberSuffix-UniqueId
    expect($planet->name)
        ->toMatch('/^('.implode('|', $prefixes).')-\d{3}['.implode('', $suffixes).']/');
});

it('generates coherent planet descriptions', function () {
    $planet = $this->service->generate();
    $planet->load('properties');

    expect($planet->description)
        ->not->toBeEmpty()
        ->toBeString();

    // Description should be in English and contain planet-related terms
    expect(strtolower($planet->description))
        ->toContain('planet');
});

it('selects planet types respecting weighted probability', function () {
    $typeCounts = [];
    $iterations = 1000;
    $tolerance = 0.1; // 10% tolerance

    // Generate many planets and count types
    for ($i = 0; $i < $iterations; $i++) {
        $type = $this->service->selectPlanetType();
        $typeCounts[$type] = ($typeCounts[$type] ?? 0) + 1;
    }

    // Check that distribution is close to expected weights
    $types = config('planets.types');
    $totalWeight = array_sum(array_column($types, 'weight'));

    foreach ($types as $typeName => $typeConfig) {
        $expectedPercentage = ($typeConfig['weight'] / $totalWeight) * 100;
        $actualPercentage = ($typeCounts[$typeName] / $iterations) * 100;
        $difference = abs($actualPercentage - $expectedPercentage);

        expect($difference)
            ->toBeLessThan($expectedPercentage * $tolerance + 5);
    }
});

it('generates valid characteristics for a given type', function () {
    $types = config('planets.types');

    foreach (array_keys($types) as $type) {
        $characteristics = $this->service->generateCharacteristics($type);

        expect($characteristics)
            ->toBeArray()
            ->toHaveKeys(['size', 'temperature', 'atmosphere', 'terrain', 'resources']);

        // Verify each characteristic is valid for the type
        $typeConfig = $types[$type]['characteristics'];
        expect($characteristics['size'])->toBeIn(array_keys($typeConfig['size']));
        expect($characteristics['temperature'])->toBeIn(array_keys($typeConfig['temperature']));
        expect($characteristics['atmosphere'])->toBeIn(array_keys($typeConfig['atmosphere']));
        expect($characteristics['terrain'])->toBeIn(array_keys($typeConfig['terrain']));
        expect($characteristics['resources'])->toBeIn(array_keys($typeConfig['resources']));
    }
});

it('generates coherent descriptions', function () {
    $types = config('planets.types');

    foreach (array_keys($types) as $type) {
        $characteristics = $this->service->generateCharacteristics($type);
        $description = $this->service->generateDescription($type, $characteristics);

        expect($description)
            ->toBeString()
            ->not->toBeEmpty()
            ->and(strlen($description))->toBeGreaterThan(50);
    }
});

it('can generate multiple planets without conflicts', function () {
    $planets = [];

    // Generate 50 planets
    for ($i = 0; $i < 50; $i++) {
        $planet = $this->service->generate();
        $planets[] = $planet;

        // Verify each planet is valid
        expect($planet)
            ->toBeInstanceOf(Planet::class)
            ->and($planet->id)->not->toBeNull()
            ->and($planet->properties)->not->toBeNull();
    }

    // Verify all planets are unique
    $ids = array_map(fn ($p) => $p->id, $planets);
    expect($ids)->toHaveCount(count(array_unique($ids)));
});
