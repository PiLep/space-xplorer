<?php

namespace Database\Factories;

use App\Models\CodexEntry;
use App\Models\Planet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CodexEntry>
 */
class CodexEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CodexEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'planet_id' => Planet::factory(),
            'name' => null,
            'fallback_name' => 'Planète '.fake()->randomElement(['Tellurique', 'Gazeuse', 'Glacée', 'Océanique', 'Désertique', 'Volcanique']).' #'.rand(1000, 9999),
            'description' => fake()->paragraphs(2, true),
            'discovered_by_user_id' => User::factory(),
            'is_named' => false,
            'is_public' => true,
        ];
    }

    /**
     * Indicate that the entry has a user-provided name.
     */
    public function named(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->unique()->words(2, true),
            'is_named' => true,
        ]);
    }

    /**
     * Indicate that the entry is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }

    /**
     * Indicate that the entry is private.
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => false,
        ]);
    }
}

