<?php

use App\Events\DashboardAccessed;
use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

it('renders dashboard component successfully', function () {
    Event::fake([DashboardAccessed::class]);

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

    Event::assertDispatched(DashboardAccessed::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
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

it('initializes terminal boot on mount', function () {
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $user->update(['home_planet_id' => $planet->id]);

    Auth::login($user);

    Livewire::test(\App\Livewire\Dashboard::class)
        ->assertSet('loading', true)
        ->assertSet('terminalBooted', false)
        ->assertSet('bootStep', 0)
        ->assertSet('bootMessages', []);
});

it('starts terminal boot sequence', function () {
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $user->update(['home_planet_id' => $planet->id]);

    Auth::login($user);

    Livewire::test(\App\Livewire\Dashboard::class)
        ->call('startTerminalBoot')
        ->assertSet('loading', true)
        ->assertSet('terminalBooted', false)
        ->assertSet('bootStep', 0)
        ->assertSet('bootMessages', []);
});

it('progresses through boot steps', function () {
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $user->update(['home_planet_id' => $planet->id]);

    Auth::login($user);

    $component = Livewire::test(\App\Livewire\Dashboard::class);

    // Progress through boot steps
    for ($i = 0; $i < 9; $i++) {
        $component->call('nextBootStep');
    }

    $component->assertSet('bootStep', 9)
        ->assertSet('bootMessages', function ($messages) {
            return count($messages) === 9;
        })
        ->assertSet('terminalBooted', true)
        ->assertSet('loading', false);
});

it('loads user and planet after boot sequence completes', function () {
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $user->update(['home_planet_id' => $planet->id]);

    Auth::login($user);

    $component = Livewire::test(\App\Livewire\Dashboard::class);

    // Complete boot sequence
    for ($i = 0; $i < 9; $i++) {
        $component->call('nextBootStep');
    }

    $component->assertSet('user.id', $user->id)
        ->assertSet('planet.id', $planet->id)
        ->assertSet('terminalBooted', true)
        ->assertSet('loading', false);
});

it('handles exception during load user and planet', function () {
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $user->update(['home_planet_id' => $planet->id]);

    Auth::login($user);

    // Delete planet to cause an error
    $planet->delete();

    Livewire::test(\App\Livewire\Dashboard::class)
        ->call('loadUserAndPlanet')
        ->assertSet('error', function ($error) {
            return str_contains($error, '[ERROR]') || str_contains($error, 'Failed to load planet data');
        })
        ->assertSet('loading', false)
        ->assertSet('terminalBooted', true);
});
