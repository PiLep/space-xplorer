<?php

namespace Tests\Feature\Api;

use App\Models\Planet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanetControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that GET /api/planets/{id} returns planet details.
     */
    public function test_show_returns_planet_details(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $planet = Planet::factory()->create();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson("/api/planets/{$planet->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'planet' => [
                        'id',
                        'name',
                        'type',
                        'size',
                        'temperature',
                        'atmosphere',
                        'terrain',
                        'resources',
                        'description',
                    ],
                ],
                'status',
            ])
            ->assertJson([
                'data' => [
                    'planet' => [
                        'id' => $planet->id,
                        'name' => $planet->name,
                        'type' => $planet->type,
                        'size' => $planet->size,
                        'temperature' => $planet->temperature,
                        'atmosphere' => $planet->atmosphere,
                        'terrain' => $planet->terrain,
                        'resources' => $planet->resources,
                        'description' => $planet->description,
                    ],
                ],
            ]);
    }

    /**
     * Test that GET /api/planets/{id} returns 404 for non-existent planet.
     */
    public function test_show_returns_404_for_non_existent_planet(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/planets/99999');

        $response->assertStatus(404);
    }

    /**
     * Test that GET /api/planets/{id} returns all required planet fields.
     */
    public function test_show_returns_all_required_fields(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $planet = Planet::factory()->create([
            'name' => 'Test Planet',
            'type' => 'tellurique',
            'size' => 'moyenne',
            'temperature' => 'tempérée',
            'atmosphere' => 'respirable',
            'terrain' => 'rocheux',
            'resources' => 'abondantes',
            'description' => 'A test planet description',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson("/api/planets/{$planet->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'planet' => [
                        'id' => $planet->id,
                        'name' => 'Test Planet',
                        'type' => 'tellurique',
                        'size' => 'moyenne',
                        'temperature' => 'tempérée',
                        'atmosphere' => 'respirable',
                        'terrain' => 'rocheux',
                        'resources' => 'abondantes',
                        'description' => 'A test planet description',
                    ],
                ],
            ]);
    }
}
