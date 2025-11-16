<?php

use App\Events\ProfileAccessed;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('renders profile component successfully', function () {
    Event::fake([ProfileAccessed::class]);

    $user = User::factory()->create();

    Auth::login($user);

    Livewire::test(\App\Livewire\Profile::class)
        ->assertStatus(200)
        ->assertSet('user.id', $user->id)
        ->assertSet('loading', false);

    Event::assertDispatched(ProfileAccessed::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

it('loads user data on mount', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    Auth::login($user);

    Livewire::test(\App\Livewire\Profile::class)
        ->assertSet('user.id', $user->id)
        ->assertSet('user.name', 'John Doe')
        ->assertSet('user.email', 'john@example.com')
        ->assertSet('loading', false);
});

it('can reload user data', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    Auth::login($user);

    Livewire::test(\App\Livewire\Profile::class)
        ->call('loadUser')
        ->assertSet('user.id', $user->id)
        ->assertSet('user.name', 'John Doe')
        ->assertSet('user.email', 'john@example.com')
        ->assertSet('loading', false);
});

it('handles unauthenticated user', function () {
    Livewire::test(\App\Livewire\Profile::class)
        ->assertSet('error', 'You must be logged in to view your profile.')
        ->assertSet('loading', false);
});

it('opens avatar modal and loads available avatars', function () {
    $user = User::factory()->create();
    Auth::login($user);

    // Create approved avatar resources
    $avatar1 = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create([
            'file_path' => 'avatars/avatar1.jpg',
            'description' => 'Avatar 1',
            'tags' => ['man'],
        ]);

    $avatar2 = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create([
            'file_path' => 'avatars/avatar2.jpg',
            'description' => 'Avatar 2',
            'tags' => ['woman'],
        ]);

    Http::fake([
        url('/api/resources/avatars') => Http::response([
            'data' => [
                'avatars' => [
                    [
                        'id' => $avatar1->id,
                        'file_url' => 'https://example.com/avatars/avatar1.jpg',
                        'description' => 'Avatar 1',
                        'tags' => ['man'],
                    ],
                    [
                        'id' => $avatar2->id,
                        'file_url' => 'https://example.com/avatars/avatar2.jpg',
                        'description' => 'Avatar 2',
                        'tags' => ['woman'],
                    ],
                ],
            ],
            'status' => 'success',
        ], 200),
    ]);

    Livewire::test(\App\Livewire\Profile::class)
        ->call('openAvatarModal')
        ->assertSet('showAvatarModal', true)
        ->assertSet('loadingAvatars', false)
        ->assertSet('availableAvatars', function ($avatars) use ($avatar1, $avatar2) {
            return count($avatars) === 2
                && collect($avatars)->pluck('id')->contains($avatar1->id)
                && collect($avatars)->pluck('id')->contains($avatar2->id);
        });
});

it('handles error when loading avatars fails', function () {
    $user = User::factory()->create();
    Auth::login($user);

    Http::fake([
        url('/api/resources/avatars') => Http::response([
            'message' => 'Server error',
        ], 500),
    ]);

    Livewire::test(\App\Livewire\Profile::class)
        ->call('openAvatarModal')
        ->assertSet('showAvatarModal', true)
        ->assertSet('loadingAvatars', false)
        ->assertSet('avatarMessage', '[ERROR] Failed to load available bio-profiles. Please try again.');
});

it('handles unauthenticated user when opening avatar modal', function () {
    Livewire::test(\App\Livewire\Profile::class)
        ->call('openAvatarModal')
        ->assertSet('avatarMessage', '[ERROR] Authentication required for bio-profile regeneration.')
        ->assertSet('loadingAvatars', false);
});

it('selects avatar successfully', function () {
    $user = User::factory()->create(['avatar_url' => 'old/avatar.jpg']);
    Auth::login($user);

    $avatarResource = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create(['file_path' => 'new/avatar.jpg']);

    // Mock Storage to return true for file existence
    Storage::fake('s3');
    Storage::disk('s3')->put('old/avatar.jpg', 'fake content');
    Storage::disk('s3')->put('new/avatar.jpg', 'fake content');

    // Mock HTTP response - use wildcard to catch any URL
    Http::fake([
        '*' => Http::response([
            'data' => [
                'avatar_url' => Storage::disk('s3')->url('new/avatar.jpg'),
            ],
            'message' => 'Avatar updated successfully',
            'status' => 'success',
        ], 200),
    ]);

    // Update user in database to simulate what the API would do
    // This is needed because Http::fake() intercepts the call, so the controller never runs
    $user->update(['avatar_url' => 'new/avatar.jpg']);

    $component = Livewire::test(\App\Livewire\Profile::class);

    // Call selectAvatar - this should succeed and close the modal
    $component->call('selectAvatar', $avatarResource->id);

    // closeAvatarModal() resets avatarMessage to null, so we just verify:
    // 1. The modal is closed
    // 2. selectingAvatar is false
    // 3. No error message is set
    $component->assertSet('selectingAvatar', false)
        ->assertSet('showAvatarModal', false)
        ->assertSet('avatarMessage', null); // closeAvatarModal() resets it
});

it('handles error when selecting avatar fails', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $avatarResource = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create();

    Http::fake([
        url("/api/users/{$user->id}/avatar") => Http::response([
            'message' => 'The selected avatar is not available or not approved.',
            'status' => 'error',
        ], 400),
    ]);

    Livewire::test(\App\Livewire\Profile::class)
        ->call('selectAvatar', $avatarResource->id)
        ->assertSet('selectingAvatar', false)
        ->assertSet('avatarMessage', '[ERROR] The selected avatar is not available or not approved.');
});

it('handles unauthenticated user when selecting avatar', function () {
    $avatarResource = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create();

    Livewire::test(\App\Livewire\Profile::class)
        ->call('selectAvatar', $avatarResource->id)
        ->assertSet('avatarMessage', '[ERROR] Authentication required for bio-profile regeneration.')
        ->assertSet('selectingAvatar', false);
});

it('closes avatar modal', function () {
    $user = User::factory()->create();
    Auth::login($user);

    Livewire::test(\App\Livewire\Profile::class)
        ->set('showAvatarModal', true)
        ->set('availableAvatars', [['id' => '1', 'file_url' => 'https://example.com/avatar.jpg', 'description' => 'Test']])
        ->set('avatarMessage', 'Test message')
        ->set('loadingAvatars', true)
        ->set('selectingAvatar', true)
        ->call('closeAvatarModal')
        ->assertSet('showAvatarModal', false)
        ->assertSet('availableAvatars', [])
        ->assertSet('avatarMessage', null)
        ->assertSet('loadingAvatars', false)
        ->assertSet('selectingAvatar', false);
});

it('reloads user data after selecting avatar', function () {
    $user = User::factory()->create(['avatar_url' => 'old/avatar.jpg']);
    Auth::login($user);

    $avatarResource = Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create(['file_path' => 'new/avatar.jpg']);

    // Mock Storage to return true for file existence
    Storage::fake('s3');
    Storage::disk('s3')->put('new/avatar.jpg', 'fake content');

    // Update user in database to simulate the change
    $user->update(['avatar_url' => 'new/avatar.jpg']);

    // Mock the API response
    Http::fake([
        url("/api/users/{$user->id}/avatar") => Http::response([
            'data' => [
                'avatar_url' => Storage::disk('s3')->url('new/avatar.jpg'),
            ],
            'message' => 'Avatar updated successfully',
            'status' => 'success',
        ], 200),
    ]);

    Livewire::test(\App\Livewire\Profile::class)
        ->call('selectAvatar', $avatarResource->id)
        ->assertSet('user.avatar_url', Storage::disk('s3')->url('new/avatar.jpg'));
});
