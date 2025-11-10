<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resource>
 */
class ResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Resource::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['avatar_image', 'planet_image', 'planet_video']),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected', 'generating']),
            'file_path' => 'images/generated/'.fake()->word().'.'.fake()->randomElement(['png', 'jpg', 'mp4']),
            'prompt' => fake()->sentence(10),
            'tags' => [fake()->word(), fake()->word(), fake()->word()],
            'description' => fake()->optional()->sentence(15),
            'metadata' => null,
            'created_by' => User::factory(),
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ];
    }

    /**
     * Indicate that the resource is approved.
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'approved_by' => User::factory(),
                'approved_at' => now(),
                'rejection_reason' => null,
            ];
        });
    }

    /**
     * Indicate that the resource is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Indicate that the resource is rejected.
     */
    public function rejected(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'approved_by' => User::factory(),
                'approved_at' => now(),
                'rejection_reason' => fake()->sentence(),
            ];
        });
    }

    /**
     * Indicate that the resource is generating.
     */
    public function generating(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'generating',
            'file_path' => null,
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Set the resource type.
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }
}
