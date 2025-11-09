<?php

use App\Models\Planet;
use App\Models\User;

it('returns user details', function () {
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
});

it('returns 404 for non-existent user', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/users/99999');

    $response->assertStatus(404);
});

it('updates user profile successfully', function () {
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
});

it('requires authentication to update profile', function () {
    $user = User::factory()->create();

    $response = $this->putJson("/api/users/{$user->id}", [
        'name' => 'Updated Name',
    ]);

    $response->assertStatus(401);
});

it('requires authorization to update profile', function () {
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
    expect($user2->name)->not->toBe('Updated Name');
});

it('validates email format during update', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/users/{$user->id}", [
            'email' => 'invalid-email',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('validates email uniqueness during update', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);
    $token = $user1->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/users/{$user1->id}", [
            'email' => 'user2@example.com',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('can update only name', function () {
    $user = User::factory()->create(['email' => 'original@example.com']);
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/users/{$user->id}", [
            'name' => 'Updated Name Only',
        ]);

    $response->assertStatus(200);

    $user->refresh();
    expect($user->name)->toBe('Updated Name Only')
        ->and($user->email)->toBe('original@example.com');
});

it('can update only email', function () {
    $user = User::factory()->create(['name' => 'Original Name']);
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/users/{$user->id}", [
            'email' => 'newemail@example.com',
        ]);

    $response->assertStatus(200);

    $user->refresh();
    expect($user->name)->toBe('Original Name')
        ->and($user->email)->toBe('newemail@example.com');
});

it('returns user home planet', function () {
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
});

it('returns 404 when user has no home planet', function () {
    $user = User::factory()->create(['home_planet_id' => null]);
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson("/api/users/{$user->id}/home-planet");

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'User does not have a home planet assigned.',
            'status' => 'error',
        ]);
});

it('returns 404 for non-existent user when getting home planet', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/users/99999/home-planet');

    $response->assertStatus(404);
});
