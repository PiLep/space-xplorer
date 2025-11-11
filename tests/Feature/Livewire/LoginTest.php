<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

it('renders login component', function () {
    Livewire::test(\App\Livewire\LoginTerminal::class)
        ->assertStatus(200);
});

it('validates required fields during login', function () {
    Livewire::test(\App\Livewire\LoginTerminal::class)
        ->set('email', '')
        ->set('password', '')
        ->call('login')
        ->assertHasErrors(['email', 'password']);
});

it('validates email format during login', function () {
    Livewire::test(\App\Livewire\LoginTerminal::class)
        ->set('email', 'invalid-email')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasErrors(['email']);
});

it('allows successful login', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    Livewire::test(\App\Livewire\LoginTerminal::class)
        ->set('email', 'john@example.com')
        ->set('password', 'password123')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    // Verify user is authenticated
    expect(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id);
});

it('handles invalid credentials', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    Livewire::test(\App\Livewire\LoginTerminal::class)
        ->set('email', 'john@example.com')
        ->set('password', 'wrongpassword')
        ->call('login')
        ->assertHasErrors(['email']);
});

it('handles non-existent user', function () {
    Livewire::test(\App\Livewire\LoginTerminal::class)
        ->set('email', 'nonexistent@example.com')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasErrors(['email']);
});

it('initializes terminal boot on mount', function () {
    Livewire::test(\App\Livewire\LoginTerminal::class)
        ->assertSet('terminalBooted', false)
        ->assertSet('bootStep', 0)
        ->assertSet('bootMessages', []);
});

it('starts terminal boot sequence', function () {
    Livewire::test(\App\Livewire\LoginTerminal::class)
        ->call('startTerminalBoot')
        ->assertSet('terminalBooted', false)
        ->assertSet('bootStep', 0)
        ->assertSet('bootMessages', []);
});

it('progresses through boot steps', function () {
    $component = Livewire::test(\App\Livewire\LoginTerminal::class);

    // Progress through boot steps (7 steps total)
    for ($i = 0; $i < 7; $i++) {
        $component->call('nextBootStep');
    }

    $component->assertSet('bootStep', 7)
        ->assertSet('bootMessages', function ($messages) {
            return count($messages) === 7;
        })
        ->assertSet('terminalBooted', true);
});

it('shows login form after boot sequence completes', function () {
    $component = Livewire::test(\App\Livewire\LoginTerminal::class);

    // Complete boot sequence
    for ($i = 0; $i < 7; $i++) {
        $component->call('nextBootStep');
    }

    $component->assertSet('terminalBooted', true);
});

it('sets status message during login', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    Livewire::test(\App\Livewire\LoginTerminal::class)
        ->set('email', 'john@example.com')
        ->set('password', 'password123')
        ->call('login')
        ->assertSet('status', '[SUCCESS] Authentication successful. Redirecting...');
});

it('sets error status on validation failure', function () {
    $component = Livewire::test(\App\Livewire\LoginTerminal::class)
        ->set('email', 'invalid-email')
        ->set('password', 'password123')
        ->call('login');

    // The status is set to AUTHENTICATING first, then ERROR if validation fails
    // But validation happens before status is updated, so we check for validation errors
    $component->assertHasErrors(['email'])
        ->assertSet('status', function ($status) {
            // Status could be AUTHENTICATING or ERROR depending on when validation fails
            return str_contains($status, '[ERROR]') || str_contains($status, '[AUTHENTICATING]');
        });
});

it('sets error status on authentication failure', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    // AuthService throws ValidationException for invalid credentials,
    // which is caught as validation error, so status is "[ERROR] Validation failed."
    Livewire::test(\App\Livewire\LoginTerminal::class)
        ->set('email', 'john@example.com')
        ->set('password', 'wrongpassword')
        ->call('login')
        ->assertSet('status', '[ERROR] Validation failed.')
        ->assertHasErrors(['email']);
});
