<?php

namespace Tests\Feature\Livewire;

use App\Models\Planet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the dashboard component renders successfully.
     */
    public function test_dashboard_component_renders(): void
    {
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
    }

    /**
     * Test that dashboard loads user and planet data on mount.
     */
    public function test_dashboard_loads_user_and_planet_data(): void
    {
        $user = User::factory()->create();
        $planet = Planet::factory()->create();
        $user->update(['home_planet_id' => $planet->id]);

        Auth::login($user);

        Livewire::test(\App\Livewire\Dashboard::class)
            ->call('loadUserAndPlanet')
            ->assertSet('user.id', $user->id)
            ->assertSet('planet.id', $planet->id)
            ->assertSet('loading', false);
    }

    /**
     * Test that dashboard handles missing home planet.
     */
    public function test_dashboard_handles_missing_home_planet(): void
    {
        $user = User::factory()->create(['home_planet_id' => null]);

        Auth::login($user);

        Livewire::test(\App\Livewire\Dashboard::class)
            ->call('loadUserAndPlanet')
            ->assertSet('error', '[ERROR] No home planet found. Please contact support.')
            ->assertSet('loading', false);
    }

    /**
     * Test that dashboard handles unauthenticated user.
     */
    public function test_dashboard_handles_unauthenticated_user(): void
    {
        Livewire::test(\App\Livewire\Dashboard::class)
            ->call('loadUserAndPlanet')
            ->assertSet('error', '[ERROR] You must be logged in to view your dashboard.')
            ->assertSet('loading', false);
    }

    /**
     * Test that dashboard can reload user and planet data.
     */
    public function test_dashboard_can_reload_data(): void
    {
        $user = User::factory()->create();
        $planet = Planet::factory()->create();
        $user->update(['home_planet_id' => $planet->id]);

        Auth::login($user);

        Livewire::test(\App\Livewire\Dashboard::class)
            ->call('loadUserAndPlanet')
            ->assertSet('user.id', $user->id)
            ->assertSet('planet.id', $planet->id)
            ->assertSet('loading', false);
    }
}
