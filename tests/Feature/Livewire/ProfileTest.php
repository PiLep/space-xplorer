<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

it('renders profile component successfully', function () {
    $user = User::factory()->create();

    Auth::login($user);

    Livewire::test(\App\Livewire\Profile::class)
        ->assertStatus(200)
        ->assertSet('user.id', $user->id)
        ->assertSet('loading', false);
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
