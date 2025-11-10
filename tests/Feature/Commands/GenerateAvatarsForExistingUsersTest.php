<?php

use App\Exceptions\ImageGenerationException;
use App\Models\User;
use App\Services\ImageGenerationService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

it('generates avatars for users without avatars', function () {
    // Create users without avatars
    $user1 = User::factory()->create(['avatar_url' => null]);
    $user2 = User::factory()->create(['avatar_url' => null]);
    $userWithAvatar = User::factory()->create(['avatar_url' => 'https://example.com/avatar.png']);

    $avatarPath = 'images/generated/avatars/avatar.png';

    // Mock the image generator
    $mockGenerator = Mockery::mock(ImageGenerationService::class);
    $mockGenerator->shouldReceive('generate')
        ->twice()
        ->with(Mockery::any(), null, 'avatars')
        ->andReturn([
            'url' => 'https://s3.example.com/avatar.png',
            'path' => $avatarPath,
            'disk' => 's3',
            'provider' => 'openai',
        ]);

    $this->app->instance(ImageGenerationService::class, $mockGenerator);

    // Create the file in fake storage to simulate actual file creation
    Storage::disk('s3')->put($avatarPath, 'fake image content');

    // Run command with --force to skip confirmation
    Artisan::call('users:generate-avatars', ['--force' => true]);

    // Verify avatars were generated
    $user1->refresh();
    $user2->refresh();
    $userWithAvatar->refresh();

    expect($user1->avatar_url)->not->toBeNull()
        ->and($user2->avatar_url)->not->toBeNull()
        ->and($userWithAvatar->avatar_url)->toBe('https://example.com/avatar.png'); // Should remain unchanged
});

it('skips users who already have avatars', function () {
    $userWithAvatar = User::factory()->create(['avatar_url' => 'https://example.com/avatar.png']);

    $mockGenerator = Mockery::mock(ImageGenerationService::class);
    $mockGenerator->shouldNotReceive('generate');

    $this->app->instance(ImageGenerationService::class, $mockGenerator);

    Artisan::call('users:generate-avatars', ['--force' => true]);

    $output = Artisan::output();
    expect($output)->toContain('All users already have avatars');
});

it('respects limit option', function () {
    User::factory()->count(5)->create(['avatar_url' => null]);

    $avatarPath = 'images/generated/avatars/avatar.png';

    $mockGenerator = Mockery::mock(ImageGenerationService::class);
    $mockGenerator->shouldReceive('generate')
        ->times(2) // Only 2 times because of limit
        ->with(Mockery::any(), null, 'avatars')
        ->andReturn([
            'url' => 'https://s3.example.com/avatar.png',
            'path' => $avatarPath,
            'disk' => 's3',
            'provider' => 'openai',
        ]);

    $this->app->instance(ImageGenerationService::class, $mockGenerator);

    // Create the file in fake storage to simulate actual file creation
    Storage::disk('s3')->put($avatarPath, 'fake image content');

    Artisan::call('users:generate-avatars', [
        '--force' => true,
        '--limit' => 2,
    ]);

    $processedCount = User::whereNotNull('avatar_url')->count();
    expect($processedCount)->toBe(2);
});

it('handles errors gracefully and continues processing', function () {
    $user1 = User::factory()->create(['avatar_url' => null]);
    $user2 = User::factory()->create(['avatar_url' => null]);

    $avatarPath = 'images/generated/avatars/avatar.png';

    $mockGenerator = Mockery::mock(ImageGenerationService::class);
    $mockGenerator->shouldReceive('generate')
        ->once()
        ->with(Mockery::any(), null, 'avatars')
        ->andThrow(new ImageGenerationException('API error'));
    $mockGenerator->shouldReceive('generate')
        ->once()
        ->with(Mockery::any(), null, 'avatars')
        ->andReturn([
            'url' => 'https://s3.example.com/avatar.png',
            'path' => $avatarPath,
            'disk' => 's3',
            'provider' => 'openai',
        ]);

    $this->app->instance(ImageGenerationService::class, $mockGenerator);

    // Create the file in fake storage to simulate actual file creation
    Storage::disk('s3')->put($avatarPath, 'fake image content');

    $exitCode = Artisan::call('users:generate-avatars', ['--force' => true]);

    // Should return failure exit code because of errors
    expect($exitCode)->toBe(1);

    // One user should have avatar, one should not
    $user1->refresh();
    $user2->refresh();

    expect($user1->avatar_url)->toBeNull()
        ->and($user2->avatar_url)->not->toBeNull();
});

afterEach(function () {
    Mockery::close();
});
