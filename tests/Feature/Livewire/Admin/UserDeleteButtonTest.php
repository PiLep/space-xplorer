<?php

use App\Events\UserDeleted;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'email' => 'admin@example.com',
        'is_super_admin' => true,
    ]);

    config(['admin.email_whitelist' => '']);
    Auth::guard('admin')->login($this->admin);
});

it('renders delete button component', function () {
    $user = User::factory()->create();

    Livewire::test(\App\Livewire\Admin\UserDeleteButton::class, ['user' => $user])
        ->assertSee('Delete User');
});

it('opens confirmation modal when delete button is clicked', function () {
    $user = User::factory()->create();

    Livewire::test(\App\Livewire\Admin\UserDeleteButton::class, ['user' => $user])
        ->assertSet('showConfirmModal', false)
        ->call('openConfirmModal')
        ->assertSet('showConfirmModal', true);
});

it('closes confirmation modal when cancel is clicked', function () {
    $user = User::factory()->create();

    Livewire::test(\App\Livewire\Admin\UserDeleteButton::class, ['user' => $user])
        ->set('showConfirmModal', true)
        ->call('closeConfirmModal')
        ->assertSet('showConfirmModal', false);
});

it('deletes user when confirmed', function () {
    Event::fake([UserDeleted::class]);

    $user = User::factory()->create();
    $userId = $user->id;

    Livewire::test(\App\Livewire\Admin\UserDeleteButton::class, ['user' => $user])
        ->call('delete')
        ->assertRedirect(route('admin.users.index'));

    // Verify user is deleted
    expect(User::find($userId))->toBeNull();

    // Verify event was dispatched
    Event::assertDispatched(UserDeleted::class, function ($event) use ($userId) {
        return $event->user->id === $userId;
    });
});

it('cleans up user sessions when user is deleted', function () {
    $user = User::factory()->create();

    // Create sessions for the user
    DB::table('sessions')->insert([
        'id' => 'session-1',
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test',
        'payload' => 'test',
        'last_activity' => now()->timestamp,
    ]);

    Livewire::test(\App\Livewire\Admin\UserDeleteButton::class, ['user' => $user])
        ->call('delete');

    // Verify sessions are deleted
    $remainingSessions = DB::table('sessions')
        ->where('user_id', $user->id)
        ->count();

    expect($remainingSessions)->toBe(0);
});

it('cleans up sanctum tokens when user is deleted', function () {
    $user = User::factory()->create();

    // Create Sanctum tokens
    DB::table('personal_access_tokens')->insert([
        'name' => 'Test Token',
        'token' => hash('sha256', 'test-token'),
        'abilities' => json_encode(['*']),
        'tokenable_type' => User::class,
        'tokenable_id' => $user->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Livewire::test(\App\Livewire\Admin\UserDeleteButton::class, ['user' => $user])
        ->call('delete');

    // Verify tokens are deleted
    $remainingTokens = DB::table('personal_access_tokens')
        ->where('tokenable_type', User::class)
        ->where('tokenable_id', $user->id)
        ->count();

    expect($remainingTokens)->toBe(0);
});

it('displays user name in confirmation modal', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
    ]);

    Livewire::test(\App\Livewire\Admin\UserDeleteButton::class, ['user' => $user])
        ->set('showConfirmModal', true)
        ->assertSee('John Doe');
});

it('displays warning message in confirmation modal', function () {
    $user = User::factory()->create();

    Livewire::test(\App\Livewire\Admin\UserDeleteButton::class, ['user' => $user])
        ->set('showConfirmModal', true)
        ->assertSee('This action cannot be undone');
});

it('prevents admin from deleting their own account', function () {
    // Use the admin created in beforeEach
    $admin = $this->admin;

    Livewire::test(\App\Livewire\Admin\UserDeleteButton::class, ['user' => $admin])
        ->call('delete')
        ->assertSet('error', 'You cannot delete your own account.')
        ->assertSet('showConfirmModal', false);

    // Verify admin is still in database
    expect(User::find($admin->id))->not->toBeNull();
});

it('allows admin to delete other users', function () {
    $otherUser = User::factory()->create();

    Livewire::test(\App\Livewire\Admin\UserDeleteButton::class, ['user' => $otherUser])
        ->call('delete')
        ->assertRedirect(route('admin.users.index'));

    // Verify other user is deleted
    expect(User::find($otherUser->id))->toBeNull();
});

it('displays error message when trying to delete own account', function () {
    // Use the admin created in beforeEach
    $admin = $this->admin;

    Livewire::test(\App\Livewire\Admin\UserDeleteButton::class, ['user' => $admin])
        ->call('delete')
        ->assertSee('You cannot delete your own account.');
});

