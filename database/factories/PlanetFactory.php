<?php

namespace Database\Factories;

use App\Models\Planet;
use App\Models\PlanetProperty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Planet>
 */
class PlanetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Planet::class;

    /**
     * Mapping of French to English translations.
     */
    private const TYPE_TRANSLATIONS = [
        'tellurique' => 'terrestrial',
        'gazeuse' => 'gaseous',
        'glacée' => 'icy',
        'désertique' => 'desert',
        'océanique' => 'oceanic',
    ];

    private const SIZE_TRANSLATIONS = [
        'petite' => 'small',
        'moyenne' => 'medium',
        'grande' => 'large',
    ];

    private const TEMPERATURE_TRANSLATIONS = [
        'froide' => 'cold',
        'tempérée' => 'temperate',
        'chaude' => 'hot',
    ];

    private const ATMOSPHERE_TRANSLATIONS = [
        'respirable' => 'breathable',
        'toxique' => 'toxic',
        'inexistante' => 'nonexistent',
    ];

    private const TERRAIN_TRANSLATIONS = [
        'rocheux' => 'rocky',
        'océanique' => 'oceanic',
        'désertique' => 'desert',
        'forestier' => 'forested',
        'urbain' => 'urban',
        'mixte' => 'mixed',
        'glacé' => 'icy',
    ];

    private const RESOURCES_TRANSLATIONS = [
        'abondantes' => 'abundant',
        'modérées' => 'moderate',
        'rares' => 'rare',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = config('planets.types');
        $type = array_rand($types);
        $typeConfig = $types[$type]['characteristics'];

        $sizeFr = fake()->randomElement(array_keys($typeConfig['size']));
        $temperatureFr = fake()->randomElement(array_keys($typeConfig['temperature']));
        $atmosphereFr = fake()->randomElement(array_keys($typeConfig['atmosphere']));
        $terrainFr = fake()->randomElement(array_keys($typeConfig['terrain']));
        $resourcesFr = fake()->randomElement(array_keys($typeConfig['resources']));

        return [
            'name' => fake()->unique()->word().'-'.rand(100, 999).fake()->randomElement(['Alpha', 'Beta', 'Gamma', 'Delta']),
            'image_generating' => false,
            'video_generating' => false,
            // Old columns (to be removed after data migration)
            'type' => $type,
            'size' => $sizeFr,
            'temperature' => $temperatureFr,
            'atmosphere' => $atmosphereFr,
            'terrain' => $terrainFr,
            'resources' => $resourcesFr,
            'description' => fake()->sentence(20),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Planet $planet) {
            $types = config('planets.types');
            $type = array_rand($types);
            $typeConfig = $types[$type]['characteristics'];

            $sizeFr = fake()->randomElement(array_keys($typeConfig['size']));
            $temperatureFr = fake()->randomElement(array_keys($typeConfig['temperature']));
            $atmosphereFr = fake()->randomElement(array_keys($typeConfig['atmosphere']));
            $terrainFr = fake()->randomElement(array_keys($typeConfig['terrain']));
            $resourcesFr = fake()->randomElement(array_keys($typeConfig['resources']));

            PlanetProperty::create([
                'planet_id' => $planet->id,
                'type' => self::TYPE_TRANSLATIONS[$type] ?? $type,
                'size' => self::SIZE_TRANSLATIONS[$sizeFr] ?? $sizeFr,
                'temperature' => self::TEMPERATURE_TRANSLATIONS[$temperatureFr] ?? $temperatureFr,
                'atmosphere' => self::ATMOSPHERE_TRANSLATIONS[$atmosphereFr] ?? $atmosphereFr,
                'terrain' => self::TERRAIN_TRANSLATIONS[$terrainFr] ?? $terrainFr,
                'resources' => self::RESOURCES_TRANSLATIONS[$resourcesFr] ?? $resourcesFr,
                'description' => fake()->sentence(20),
            ]);
        });
    }
}
