<?php

use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

it('renders register component', function () {
    Livewire::test(\App\Livewire\Register::class)
        ->assertStatus(200);
});

it('validates required fields during registration', function () {
    Livewire::test(\App\Livewire\Register::class)
        ->set('name', '')
        ->set('email', '')
        ->set('password', '')
        ->call('register')
        ->assertHasErrors(['name', 'email', 'password']);
});

it('validates email format during registration', function () {
    Livewire::test(\App\Livewire\Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'invalid-email')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors(['email']);
});

it('validates password confirmation during registration', function () {
    Livewire::test(\App\Livewire\Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'different')
        ->call('register')
        ->assertHasErrors(['password']);
});

it('validates password minimum length during registration', function () {
    Livewire::test(\App\Livewire\Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('register')
        ->assertHasErrors(['password']);
});

it('allows successful registration', function () {
    // Ensure mock is set up (should be done by tests/Feature/Pest.php, but explicit call ensures it)
    // The beforeEach in tests/Feature/Pest.php should handle this, but calling it explicitly
    // ensures the mock is ready before Livewire creates the user

    Livewire::test(\App\Livewire\Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'john1@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertRedirect(route('dashboard'));

    // Verify user is authenticated
    expect(Auth::check())->toBeTrue();

    // Verify user was created
    $user = Auth::user();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john1@example.com');
});

it('handles duplicate email during registration', function () {
    // Create existing user
    User::factory()->create(['email' => 'existing@example.com']);

    Livewire::test(\App\Livewire\Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'existing@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors(['email']);
});

it('creates user with home planet during registration', function () {
    Livewire::test(\App\Livewire\Register::class)
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertRedirect(route('dashboard'));

    // Verify user was created with home planet
    $user = Auth::user();
    expect($user)->not->toBeNull()
        ->and($user->home_planet_id)->not->toBeNull()
        ->and($user->homePlanet)->not->toBeNull();
});
