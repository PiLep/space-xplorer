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
