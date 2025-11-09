<?php

namespace Database\Factories;

use App\Models\Planet;
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
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = config('planets.types');
        $type = array_rand($types);
        $typeConfig = $types[$type]['characteristics'];

        return [
            'name' => fake()->unique()->word().'-'.rand(100, 999).fake()->randomElement(['Alpha', 'Beta', 'Gamma', 'Delta']),
            'type' => $type,
            'size' => fake()->randomElement(array_keys($typeConfig['size'])),
            'temperature' => fake()->randomElement(array_keys($typeConfig['temperature'])),
            'atmosphere' => fake()->randomElement(array_keys($typeConfig['atmosphere'])),
            'terrain' => fake()->randomElement(array_keys($typeConfig['terrain'])),
            'resources' => fake()->randomElement(array_keys($typeConfig['resources'])),
            'description' => fake()->sentence(20),
        ];
    }
}
