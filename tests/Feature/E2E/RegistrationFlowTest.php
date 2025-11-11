<?php

use App\Models\Planet;
use App\Models\User;
use Livewire\Livewire;

it('completes registration flow end-to-end', function () {
    $userData = [
        'name' => 'E2E Test User',
        'email' => 'e2etest@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    // Visit registration page
    $response = $this->get('/register');
    $response->assertStatus(200);
    // The page should contain either the booting message or the registration form
    $response->assertSee('register', false); // Case insensitive search

    // Submit registration form via Livewire
    $livewire = Livewire::test(\App\Livewire\Register::class)
        ->set('name', $userData['name'])
        ->set('email', $userData['email'])
        ->set('password', $userData['password'])
        ->set('password_confirmation', $userData['password_confirmation'])
        ->set('terms_accepted', true)
        ->call('register');

    // Verify redirect happened (indicates success)
    $livewire->assertRedirect(route('email.verify'));

    // Verify user was created
    $this->assertDatabaseHas('users', [
        'email' => $userData['email'],
        'name' => $userData['name'],
    ]);

    $user = User::where('email', $userData['email'])->first();
    expect($user)->not->toBeNull();

    // Verify home planet was assigned
    expect($user->home_planet_id)->not->toBeNull();

    // Verify planet exists
    $planet = Planet::find($user->home_planet_id);
    expect($planet)->not->toBeNull()
        ->and($planet->name)->not->toBeNull()
        ->and($planet->type)->not->toBeNull();
});

it('shows validation errors for invalid registration data', function () {
    $livewire = Livewire::test(\App\Livewire\Register::class)
        ->set('name', '')
        ->set('email', 'invalid-email')
        ->set('password', 'short')
        ->set('password_confirmation', 'different')
        ->call('register');

    // Should have validation errors
    $livewire->assertHasErrors(['name', 'email', 'password']);

    // User should not be created
    $this->assertDatabaseMissing('users', [
        'email' => 'invalid-email',
    ]);
});

it('shows error for duplicate email during registration', function () {
    // Create existing user
    User::factory()->create(['email' => 'existing@example.com']);

    $livewire = Livewire::test(\App\Livewire\Register::class)
        ->set('name', 'Test User')
        ->set('email', 'existing@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register');

    // Should have email error
    $livewire->assertHasErrors(['email']);

    // Should not create duplicate user
    expect(User::where('email', 'existing@example.com')->count())->toBe(1);
});

it('creates user and planet correctly during registration', function () {
    $userData = [
        'name' => 'API Test User',
        'email' => 'apitest@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    // Count users before
    $usersBefore = User::count();
    $planetsBefore = Planet::count();

    // Submit registration
    $livewire = Livewire::test(\App\Livewire\Register::class)
        ->set('name', $userData['name'])
        ->set('email', $userData['email'])
        ->set('password', $userData['password'])
        ->set('password_confirmation', $userData['password_confirmation'])
        ->set('terms_accepted', true)
        ->call('register');

    // Verify redirect happened (indicates success)
    $livewire->assertRedirect(route('email.verify'));

    // Verify user was created
    expect(User::count())->toBe($usersBefore + 1);

    // Verify planet was created
    expect(Planet::count())->toBeGreaterThan($planetsBefore);

    $user = User::where('email', $userData['email'])->first();
    expect($user)->not->toBeNull()
        ->and($user->home_planet_id)->not->toBeNull();
});
