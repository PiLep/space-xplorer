<?php

namespace Tests\Unit\Services;

use App\Models\Planet;
use App\Services\PlanetGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanetGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    private PlanetGeneratorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PlanetGeneratorService;
    }

    /**
     * Test that the service generates a valid planet with all required fields.
     */
    public function test_generates_valid_planet_with_all_characteristics(): void
    {
        $planet = $this->service->generate();

        $this->assertInstanceOf(Planet::class, $planet);
        $this->assertNotNull($planet->id);
        $this->assertNotNull($planet->name);
        $this->assertNotNull($planet->type);
        $this->assertNotNull($planet->size);
        $this->assertNotNull($planet->temperature);
        $this->assertNotNull($planet->atmosphere);
        $this->assertNotNull($planet->terrain);
        $this->assertNotNull($planet->resources);
        $this->assertNotNull($planet->description);
    }

    /**
     * Test that generated planet has valid type from configuration.
     */
    public function test_generated_planet_has_valid_type(): void
    {
        $validTypes = array_keys(config('planets.types'));

        $planet = $this->service->generate();

        $this->assertContains($planet->type, $validTypes);
    }

    /**
     * Test that generated planet has valid characteristics for its type.
     */
    public function test_generated_planet_has_valid_characteristics_for_type(): void
    {
        $planet = $this->service->generate();
        $typeConfig = config("planets.types.{$planet->type}.characteristics");

        // Check size
        $validSizes = array_keys($typeConfig['size']);
        $this->assertContains($planet->size, $validSizes);

        // Check temperature
        $validTemperatures = array_keys($typeConfig['temperature']);
        $this->assertContains($planet->temperature, $validTemperatures);

        // Check atmosphere
        $validAtmospheres = array_keys($typeConfig['atmosphere']);
        $this->assertContains($planet->atmosphere, $validAtmospheres);

        // Check terrain
        $validTerrains = array_keys($typeConfig['terrain']);
        $this->assertContains($planet->terrain, $validTerrains);

        // Check resources
        $validResources = array_keys($typeConfig['resources']);
        $this->assertContains($planet->resources, $validResources);
    }

    /**
     * Test that planet names are unique.
     */
    public function test_generated_planet_names_are_unique(): void
    {
        $planets = [];
        $names = [];

        // Generate multiple planets
        for ($i = 0; $i < 10; $i++) {
            $planet = $this->service->generate();
            $planets[] = $planet;
            $names[] = $planet->name;
        }

        // Check that all names are unique
        $uniqueNames = array_unique($names);
        $this->assertCount(count($names), $uniqueNames, 'All planet names should be unique');
    }

    /**
     * Test that planet name follows the expected format.
     */
    public function test_planet_name_follows_expected_format(): void
    {
        $planet = $this->service->generate();
        $prefixes = config('planets.name_prefixes');
        $suffixes = config('planets.name_suffixes');

        // Name should contain a prefix, a number, and a suffix
        // Format: Prefix-NumberSuffix or Prefix-NumberSuffix-UniqueId
        $this->assertMatchesRegularExpression(
            '/^('.implode('|', $prefixes).')-\d{3}['.implode('', $suffixes).']/',
            $planet->name,
            'Planet name should follow the format: Prefix-NumberSuffix'
        );
    }

    /**
     * Test that planet description is coherent and contains expected elements.
     */
    public function test_planet_description_is_coherent(): void
    {
        $planet = $this->service->generate();

        $this->assertNotEmpty($planet->description);
        $this->assertIsString($planet->description);

        // Description should contain information about the planet type
        $typeDescriptions = [
            'tellurique' => 'tellurique',
            'gazeuse' => 'gazeuse',
            'glacée' => 'glacée',
            'désertique' => 'désertique',
            'océanique' => 'océanique',
        ];

        $this->assertStringContainsString(
            $typeDescriptions[$planet->type],
            strtolower($planet->description),
            'Description should mention the planet type'
        );
    }

    /**
     * Test that planet type selection respects weighted probability (statistical test).
     */
    public function test_planet_type_selection_respects_weighted_probability(): void
    {
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

            $this->assertLessThan(
                $expectedPercentage * $tolerance + 5, // Allow 10% tolerance + 5% margin
                $difference,
                "Type '{$typeName}' distribution should be close to expected {$expectedPercentage}% (got {$actualPercentage}%)"
            );
        }
    }

    /**
     * Test that generateCharacteristics returns valid characteristics for a given type.
     */
    public function test_generate_characteristics_returns_valid_data(): void
    {
        $types = config('planets.types');

        foreach (array_keys($types) as $type) {
            $characteristics = $this->service->generateCharacteristics($type);

            $this->assertIsArray($characteristics);
            $this->assertArrayHasKey('size', $characteristics);
            $this->assertArrayHasKey('temperature', $characteristics);
            $this->assertArrayHasKey('atmosphere', $characteristics);
            $this->assertArrayHasKey('terrain', $characteristics);
            $this->assertArrayHasKey('resources', $characteristics);

            // Verify each characteristic is valid for the type
            $typeConfig = $types[$type]['characteristics'];
            $this->assertContains($characteristics['size'], array_keys($typeConfig['size']));
            $this->assertContains($characteristics['temperature'], array_keys($typeConfig['temperature']));
            $this->assertContains($characteristics['atmosphere'], array_keys($typeConfig['atmosphere']));
            $this->assertContains($characteristics['terrain'], array_keys($typeConfig['terrain']));
            $this->assertContains($characteristics['resources'], array_keys($typeConfig['resources']));
        }
    }

    /**
     * Test that generateDescription creates a coherent description.
     */
    public function test_generate_description_creates_coherent_text(): void
    {
        $types = config('planets.types');

        foreach (array_keys($types) as $type) {
            $characteristics = $this->service->generateCharacteristics($type);
            $description = $this->service->generateDescription($type, $characteristics);

            $this->assertIsString($description);
            $this->assertNotEmpty($description);
            $this->assertGreaterThan(50, strlen($description), 'Description should be substantial');
        }
    }

    /**
     * Test that multiple planets can be generated without conflicts.
     */
    public function test_can_generate_multiple_planets_without_conflicts(): void
    {
        $planets = [];

        // Generate 50 planets
        for ($i = 0; $i < 50; $i++) {
            $planet = $this->service->generate();
            $planets[] = $planet;

            // Verify each planet is valid
            $this->assertInstanceOf(Planet::class, $planet);
            $this->assertNotNull($planet->id);
        }

        // Verify all planets are unique
        $ids = array_map(fn ($p) => $p->id, $planets);
        $this->assertCount(count($planets), array_unique($ids), 'All planets should have unique IDs');
    }
}
