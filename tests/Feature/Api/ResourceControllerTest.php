<?php

use App\Models\Resource;
use App\Models\User;

it('returns approved avatar resources', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    // Create approved avatar resources
    $avatar1 = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create([
            'file_path' => 'avatars/avatar1.jpg',
            'description' => 'Avatar 1',
            'tags' => ['man', 'casual'],
        ]);

    $avatar2 = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create([
            'file_path' => 'avatars/avatar2.jpg',
            'description' => 'Avatar 2',
            'tags' => ['woman', 'formal'],
        ]);

    // Create non-approved avatar (should not appear)
    Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create(['file_path' => 'avatars/pending.jpg']);

    // Create non-avatar resource (should not appear)
    Resource::factory()
        ->approved()
        ->ofType('planet_image')
        ->create(['file_path' => 'planets/planet1.jpg']);

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/resources/avatars');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'avatars' => [
                    '*' => ['id', 'file_url', 'description', 'tags'],
                ],
            ],
            'status',
        ])
        ->assertJson([
            'status' => 'success',
        ]);

    $avatars = $response->json('data.avatars');
    expect($avatars)->toHaveCount(2)
        ->and(collect($avatars)->pluck('id'))->toContain($avatar1->id, $avatar2->id);
});

it('requires authentication to get avatars', function () {
    $response = $this->getJson('/api/resources/avatars');

    $response->assertStatus(401);
});

it('returns empty array when no approved avatars exist', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    // Create only pending avatars
    Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create();

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/resources/avatars');

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'avatars' => [],
            ],
            'status' => 'success',
        ]);
});

it('returns avatars ordered by latest first', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $oldAvatar = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create(['created_at' => now()->subDays(2)]);

    $newAvatar = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create(['created_at' => now()]);

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/resources/avatars');

    $response->assertStatus(200);

    $avatars = $response->json('data.avatars');
    expect($avatars)->toHaveCount(2)
        ->and($avatars[0]['id'])->toBe($newAvatar->id)
        ->and($avatars[1]['id'])->toBe($oldAvatar->id);
});

it('includes correct avatar data structure', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $avatar = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create([
            'file_path' => 'avatars/test.jpg',
            'description' => 'Test Avatar',
            'tags' => ['man', 'casual', 'test'],
        ]);

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/resources/avatars');

    $response->assertStatus(200);

    $avatars = $response->json('data.avatars');
    $returnedAvatar = collect($avatars)->firstWhere('id', $avatar->id);

    expect($returnedAvatar)->not->toBeNull()
        ->and($returnedAvatar)->toHaveKeys(['id', 'file_url', 'description', 'tags'])
        ->and($returnedAvatar['id'])->toBe($avatar->id)
        ->and($returnedAvatar['description'])->toBe('Test Avatar')
        ->and($returnedAvatar['tags'])->toBe(['man', 'casual', 'test']);
});
