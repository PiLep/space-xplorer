<?php

use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('has homePlanet relationship', function () {
    $planet = Planet::factory()->create();
    $user = User::factory()->create(['home_planet_id' => $planet->id]);

    expect($user->homePlanet)->not->toBeNull()
        ->and($user->homePlanet->id)->toBe($planet->id);
});

it('returns null for avatar_url when path is empty', function () {
    $user = User::factory()->create(['avatar_url' => null]);

    expect($user->avatar_url)->toBeNull();
});

it('returns full URL when avatar_url is already a URL', function () {
    $fullUrl = 'https://example.com/avatar.jpg';
    $user = User::factory()->create(['avatar_url' => $fullUrl]);

    expect($user->avatar_url)->toBe($fullUrl);
});

it('returns storage URL when avatar_url is a path and file exists', function () {
    Storage::fake('s3');
    $path = 'avatars/avatar.jpg';
    Storage::disk('s3')->put($path, 'fake content');

    $user = User::factory()->create(['avatar_url' => $path]);

    expect($user->avatar_url)->toBe(Storage::disk('s3')->url($path));
});

it('returns null when avatar_url is a path but file does not exist', function () {
    Storage::fake('s3');
    $path = 'avatars/nonexistent.jpg';

    $user = User::factory()->create(['avatar_url' => $path]);

    expect($user->avatar_url)->toBeNull();
});

it('hasAvatar returns true when avatar exists and not generating', function () {
    Storage::fake('s3');
    $path = 'avatars/avatar.jpg';
    Storage::disk('s3')->put($path, 'fake content');

    $user = User::factory()->create([
        'avatar_url' => $path,
        'avatar_generating' => false,
    ]);

    expect($user->hasAvatar())->toBeTrue();
});

it('hasAvatar returns false when avatar is generating', function () {
    Storage::fake('s3');
    $path = 'avatars/avatar.jpg';
    Storage::disk('s3')->put($path, 'fake content');

    $user = User::factory()->create([
        'avatar_url' => $path,
        'avatar_generating' => true,
    ]);

    expect($user->hasAvatar())->toBeFalse();
});

it('hasAvatar returns false when avatar_url is null', function () {
    $user = User::factory()->create([
        'avatar_url' => null,
        'avatar_generating' => false,
    ]);

    expect($user->hasAvatar())->toBeFalse();
});

it('isAvatarGenerating returns true when avatar_generating is true', function () {
    $user = User::factory()->create(['avatar_generating' => true]);

    expect($user->isAvatarGenerating())->toBeTrue();
});

it('isAvatarGenerating returns false when avatar_generating is false', function () {
    $user = User::factory()->create(['avatar_generating' => false]);

    expect($user->isAvatarGenerating())->toBeFalse();
});

it('casts is_super_admin to boolean', function () {
    $user = User::factory()->create(['is_super_admin' => true]);

    expect($user->is_super_admin)->toBeTrue()
        ->and(is_bool($user->is_super_admin))->toBeTrue();
});

it('casts password to hashed', function () {
    $password = 'plain-password';
    $user = User::factory()->create(['password' => $password]);

    expect($user->password)->not->toBe($password)
        ->and(\Hash::check($password, $user->password))->toBeTrue();
});
