<?php

namespace Database\Factories;

use App\Models\StarSystem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StarSystem>
 */
class StarSystemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StarSystem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prefixes = ['Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon', 'Zeta', 'Eta', 'Theta'];
        $suffixes = ['Centauri', 'Orionis', 'Lyrae', 'Draconis', 'Cygni', 'Aquilae', 'Pegasi', 'Andromedae'];
        $starTypes = ['yellow_dwarf', 'red_dwarf', 'orange_dwarf', 'red_giant', 'blue_giant', 'white_dwarf'];

        $prefix = $prefixes[array_rand($prefixes)];
        $suffix = $suffixes[array_rand($suffixes)];
        $number = rand(1, 999);

        return [
            'name' => "{$prefix} {$suffix}-{$number}",
            'x' => (rand(-10000, 10000) / 100),
            'y' => (rand(-10000, 10000) / 100),
            'z' => (rand(-10000, 10000) / 100),
            'star_type' => $starTypes[array_rand($starTypes)],
            'planet_count' => 0,
            'discovered' => false,
        ];
    }
}
