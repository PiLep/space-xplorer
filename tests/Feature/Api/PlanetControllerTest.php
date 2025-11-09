<?php

use App\Models\Planet;
use App\Models\User;

it('returns planet details', function () {
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
});

it('returns 404 for non-existent planet', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/planets/99999');

    $response->assertStatus(404);
});

it('returns all required planet fields', function () {
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
});
