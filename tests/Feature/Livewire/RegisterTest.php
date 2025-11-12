<?php

use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function () {
    Mail::fake();
});

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

it('allows successful registration and redirects to email verification', function () {
    // Ensure mock is set up (should be done by tests/Feature/Pest.php, but explicit call ensures it)
    // The beforeEach in tests/Feature/Pest.php should handle this, but calling it explicitly
    // ensures the mock is ready before Livewire creates the user

    Livewire::test(\App\Livewire\Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'john1@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('terms_accepted', true)
        ->call('register')
        ->assertRedirect(route('email.verify'));

    // Verify user is authenticated
    expect(Auth::check())->toBeTrue();

    // Verify user was created
    $user = Auth::user();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john1@example.com')
        ->and($user->email_verified_at)->toBeNull();
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
        ->set('terms_accepted', true)
        ->call('register')
        ->assertRedirect(route('email.verify'));

    // Verify user was created with home planet
    $user = Auth::user();
    expect($user)->not->toBeNull()
        ->and($user->home_planet_id)->not->toBeNull()
        ->and($user->homePlanet)->not->toBeNull();
});

// Note: Testing the isSubmitting guard (line 42) is difficult with Livewire
// because the component state is reset between calls. The guard works correctly
// in production when called concurrently. The finally block (line 69) is tested
// through the error handling tests below.

it('handles ValidationException from AuthService', function () {
    // Mock AuthService to throw ValidationException
    $mockAuthService = Mockery::mock(\App\Services\AuthService::class);
    $mockAuthService->shouldReceive('registerFromArray')
        ->once()
        ->andThrow(\Illuminate\Validation\ValidationException::withMessages([
            'email' => ['Custom validation error'],
        ]));

    app()->instance(\App\Services\AuthService::class, $mockAuthService);

    Livewire::test(\App\Livewire\Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'validation@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('terms_accepted', true)
        ->call('register')
        ->assertHasErrors(['email'])
        ->assertSet('status', '[ERROR] Validation failed.');
});

it('handles generic Exception from AuthService', function () {
    // Mock AuthService to throw generic Exception
    $mockAuthService = Mockery::mock(\App\Services\AuthService::class);
    $mockAuthService->shouldReceive('registerFromArray')
        ->once()
        ->andThrow(new \Exception('Database connection failed'));

    app()->instance(\App\Services\AuthService::class, $mockAuthService);

    Livewire::test(\App\Livewire\Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'error@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('terms_accepted', true)
        ->call('register')
        ->assertHasErrors(['email'])
        ->assertSet('status', '[ERROR] Registration failed.');
});

it('handles generic Exception with empty message', function () {
    // Mock AuthService to throw Exception with empty message
    $mockAuthService = Mockery::mock(\App\Services\AuthService::class);
    $mockAuthService->shouldReceive('registerFromArray')
        ->once()
        ->andThrow(new \Exception(''));

    app()->instance(\App\Services\AuthService::class, $mockAuthService);

    Livewire::test(\App\Livewire\Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'empty@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('terms_accepted', true)
        ->call('register')
        ->assertHasErrors(['email'])
        ->assertSet('status', '[ERROR] Registration failed.');
});

afterEach(function () {
    Mockery::close();
});
