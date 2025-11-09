<?php

use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

it('renders dashboard component successfully', function () {
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $user->update(['home_planet_id' => $planet->id]);

    Auth::login($user);

    Livewire::test(\App\Livewire\Dashboard::class)
        ->call('loadUserAndPlanet')
        ->assertStatus(200)
        ->assertSet('user.id', $user->id)
        ->assertSet('planet.id', $planet->id)
        ->assertSet('loading', false)
        ->assertSet('error', null);
});

it('loads user and planet data on mount', function () {
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $user->update(['home_planet_id' => $planet->id]);

    Auth::login($user);

    Livewire::test(\App\Livewire\Dashboard::class)
        ->call('loadUserAndPlanet')
        ->assertSet('user.id', $user->id)
        ->assertSet('planet.id', $planet->id)
        ->assertSet('loading', false);
});

it('handles missing home planet', function () {
    $user = User::factory()->create(['home_planet_id' => null]);

    Auth::login($user);

    Livewire::test(\App\Livewire\Dashboard::class)
        ->call('loadUserAndPlanet')
        ->assertSet('error', '[ERROR] No home planet found. Please contact support.')
        ->assertSet('loading', false);
});

it('handles unauthenticated user', function () {
    Livewire::test(\App\Livewire\Dashboard::class)
        ->call('loadUserAndPlanet')
        ->assertSet('error', '[ERROR] You must be logged in to view your dashboard.')
        ->assertSet('loading', false);
});

it('can reload user and planet data', function () {
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $user->update(['home_planet_id' => $planet->id]);

    Auth::login($user);

    Livewire::test(\App\Livewire\Dashboard::class)
        ->call('loadUserAndPlanet')
        ->assertSet('user.id', $user->id)
        ->assertSet('planet.id', $planet->id)
        ->assertSet('loading', false);
});
