<?php

use App\Events\AvatarChanged;
use App\Events\EmailChanged;
use App\Events\UserProfileUpdated;
use App\Models\Planet;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

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
    Event::fake([EmailChanged::class, UserProfileUpdated::class]);

    $user = User::factory()->create(['name' => 'Original Name', 'email' => 'old@example.com']);
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/users/{$user->id}", [
            'email' => 'newemail@example.com',
        ]);

    $response->assertStatus(200);

    $user->refresh();
    expect($user->name)->toBe('Original Name')
        ->and($user->email)->toBe('newemail@example.com');

    Event::assertDispatched(EmailChanged::class, function ($event) use ($user) {
        return $event->user->id === $user->id
            && $event->oldEmail === 'old@example.com'
            && $event->newEmail === 'newemail@example.com';
    });
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

it('updates user avatar successfully', function () {
    $user = User::factory()->create(['avatar_url' => 'old/avatar/path.jpg']);
    $avatarResource = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create(['file_path' => 'new/avatar/path.jpg']);
    $token = $user->createToken('test-token')->plainTextToken;

    // Mock Storage to return true for file existence
    Storage::fake('s3');
    Storage::disk('s3')->put('old/avatar/path.jpg', 'fake content');
    Storage::disk('s3')->put('new/avatar/path.jpg', 'fake content');

    Event::fake();

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/users/{$user->id}/avatar", [
            'resource_id' => $avatarResource->id,
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'avatar_url',
            ],
            'message',
            'status',
        ])
        ->assertJson([
            'message' => 'Avatar updated successfully',
            'status' => 'success',
        ]);

    $user->refresh();
    // Check raw attribute value since accessor checks file existence
    expect($user->getAttributes()['avatar_url'])->toBe('new/avatar/path.jpg')
        ->and((bool) $user->avatar_generating)->toBeFalse();

    Event::assertDispatched(AvatarChanged::class, function ($event) use ($user) {
        return $event->user->id === $user->id
            && $event->oldAvatarPath === Storage::disk('s3')->url('old/avatar/path.jpg')
            && $event->newAvatarPath === 'new/avatar/path.jpg';
    });

    Event::assertDispatched(UserProfileUpdated::class, function ($event) use ($user) {
        return $event->user->id === $user->id
            && isset($event->changedAttributes['avatar_url'])
            && $event->changedAttributes['avatar_url']['old'] === Storage::disk('s3')->url('old/avatar/path.jpg')
            && $event->changedAttributes['avatar_url']['new'] === 'new/avatar/path.jpg';
    });
});

it('requires authentication to update avatar', function () {
    $user = User::factory()->create();
    $avatarResource = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create();

    $response = $this->putJson("/api/users/{$user->id}/avatar", [
        'resource_id' => $avatarResource->id,
    ]);

    $response->assertStatus(401);
});

it('requires authorization to update avatar', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $avatarResource = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create();
    $token = $user1->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/users/{$user2->id}/avatar", [
            'resource_id' => $avatarResource->id,
        ]);

    $response->assertStatus(403)
        ->assertJson([
            'message' => 'Unauthorized. You can only update your own avatar.',
            'status' => 'error',
        ]);

    // Verify user2's avatar was not changed
    $user2->refresh();
    expect($user2->avatar_url)->not->toBe($avatarResource->file_path);
});

it('validates resource_id is required', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/users/{$user->id}/avatar", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['resource_id']);
});

it('rejects non-existent resource', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/users/{$user->id}/avatar", [
            'resource_id' => 'non-existent-id',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['resource_id']);
});

it('rejects non-approved avatar resource', function () {
    $user = User::factory()->create();
    $avatarResource = Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/users/{$user->id}/avatar", [
            'resource_id' => $avatarResource->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['resource_id']);
});

it('rejects rejected avatar resource', function () {
    $user = User::factory()->create();
    $avatarResource = Resource::factory()
        ->rejected()
        ->ofType('avatar_image')
        ->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/users/{$user->id}/avatar", [
            'resource_id' => $avatarResource->id,
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['resource_id']);
});

it('rejects non-avatar resource type', function () {
    $user = User::factory()->create();
    $planetResource = Resource::factory()
        ->approved()
        ->ofType('planet_image')
        ->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/users/{$user->id}/avatar", [
            'resource_id' => $planetResource->id,
        ]);

    // Validation in UpdateAvatarRequest rejects it before reaching controller
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['resource_id']);
});

it('sets avatar_generating to false when updating avatar', function () {
    $user = User::factory()->create(['avatar_generating' => true]);
    $avatarResource = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create(['file_path' => 'new/avatar/path.jpg']);
    $token = $user->createToken('test-token')->plainTextToken;

    // Mock Storage to return true for file existence
    Storage::fake('s3');
    Storage::disk('s3')->put('new/avatar/path.jpg', 'fake content');

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->putJson("/api/users/{$user->id}/avatar", [
            'resource_id' => $avatarResource->id,
        ]);

    $response->assertStatus(200);

    $user->refresh();
    // avatar_generating is stored as 0/1 in DB, not cast to boolean
    expect((bool) $user->avatar_generating)->toBeFalse();
});
