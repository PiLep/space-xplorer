<?php

namespace Tests\Feature\Api;

use App\Models\Planet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that GET /api/users/{id} returns user details.
     */
    public function test_show_returns_user_details(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email', 'home_planet_id'],
                ],
                'status',
            ])
            ->assertJson([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'home_planet_id' => $user->home_planet_id,
                    ],
                ],
            ]);
    }

    /**
     * Test that GET /api/users/{id} returns 404 for non-existent user.
     */
    public function test_show_returns_404_for_non_existent_user(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/users/99999');

        $response->assertStatus(404);
    }

    /**
     * Test that PUT /api/users/{id} updates user profile successfully.
     */
    public function test_update_updates_user_profile_successfully(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => ['id', 'name', 'email', 'home_planet_id'],
                ],
                'message',
                'status',
            ])
            ->assertJson([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => 'Updated Name',
                        'email' => 'updated@example.com',
                    ],
                ],
                'message' => 'Profile updated successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /**
     * Test that PUT /api/users/{id} requires authentication.
     */
    public function test_update_requires_authentication(): void
    {
        $user = User::factory()->create();

        $response = $this->putJson("/api/users/{$user->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test that PUT /api/users/{id} requires authorization (user can only update own profile).
     */
    public function test_update_requires_authorization(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $token = $user1->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson("/api/users/{$user2->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Unauthorized. You can only update your own profile.',
                'status' => 'error',
            ]);

        // Verify user2's data was not changed
        $user2->refresh();
        $this->assertNotEquals('Updated Name', $user2->name);
    }

    /**
     * Test that PUT /api/users/{id} validates email format.
     */
    public function test_update_validates_email_format(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson("/api/users/{$user->id}", [
                'email' => 'invalid-email',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that PUT /api/users/{id} validates email uniqueness.
     */
    public function test_update_validates_email_uniqueness(): void
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);
        $token = $user1->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson("/api/users/{$user1->id}", [
                'email' => 'user2@example.com',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that PUT /api/users/{id} can update only name.
     */
    public function test_update_can_update_only_name(): void
    {
        $user = User::factory()->create(['email' => 'original@example.com']);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson("/api/users/{$user->id}", [
                'name' => 'Updated Name Only',
            ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertEquals('Updated Name Only', $user->name);
        $this->assertEquals('original@example.com', $user->email);
    }

    /**
     * Test that PUT /api/users/{id} can update only email.
     */
    public function test_update_can_update_only_email(): void
    {
        $user = User::factory()->create(['name' => 'Original Name']);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson("/api/users/{$user->id}", [
                'email' => 'newemail@example.com',
            ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertEquals('Original Name', $user->name);
        $this->assertEquals('newemail@example.com', $user->email);
    }

    /**
     * Test that GET /api/users/{id}/home-planet returns user's home planet.
     */
    public function test_get_home_planet_returns_user_home_planet(): void
    {
        $planet = Planet::factory()->create();
        $user = User::factory()->create(['home_planet_id' => $planet->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson("/api/users/{$user->id}/home-planet");

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
     * Test that GET /api/users/{id}/home-planet returns 404 when user has no home planet.
     */
    public function test_get_home_planet_returns_404_when_no_home_planet(): void
    {
        $user = User::factory()->create(['home_planet_id' => null]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson("/api/users/{$user->id}/home-planet");

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'User does not have a home planet assigned.',
                'status' => 'error',
            ]);
    }

    /**
     * Test that GET /api/users/{id}/home-planet returns 404 for non-existent user.
     */
    public function test_get_home_planet_returns_404_for_non_existent_user(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/users/99999/home-planet');

        $response->assertStatus(404);
    }
}
