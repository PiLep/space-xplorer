<?php

namespace Database\Factories;

use App\Models\CodexContribution;
use App\Models\CodexEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CodexContribution>
 */
class CodexContributionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CodexContribution::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codex_entry_id' => CodexEntry::factory(),
            'contributor_user_id' => User::factory(),
            'content_type' => fake()->randomElement(['description', 'name_suggestion', 'additional_info']),
            'content' => fake()->paragraphs(3, true),
            'status' => 'pending',
        ];
    }

    /**
     * Indicate that the contribution is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the contribution is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }
}

