<?php

namespace Tests\Feature\Livewire;

use App\Models\Planet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
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
        Session::put('sanctum_token', 'test-token');

        // Mock API responses
        Http::fake([
            '*/api/auth/user' => Http::response([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'home_planet_id' => $planet->id,
                    ],
                ],
                'status' => 'success',
            ], 200),
            '*/api/users/'.$user->id.'/home-planet' => Http::response([
                'data' => [
                    'planet' => [
                        'id' => $planet->id,
                        'name' => $planet->name,
                        'type' => $planet->type,
                        'size' => $planet->size,
                        'temperature' => $planet->temperature,
                        'atmosphere' => $planet->atmosphere,
                        'terrain' => $planet->terrain,
                        'resources' => $planet->resources,
                        'description' => $planet->description,
                    ],
                ],
                'status' => 'success',
            ], 200),
        ]);

        Livewire::test(\App\Livewire\Dashboard::class)
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
        Session::put('sanctum_token', 'test-token');

        // Mock API responses
        Http::fake([
            '*/api/auth/user' => Http::response([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'home_planet_id' => $planet->id,
                    ],
                ],
                'status' => 'success',
            ], 200),
            '*/api/users/'.$user->id.'/home-planet' => Http::response([
                'data' => [
                    'planet' => [
                        'id' => $planet->id,
                        'name' => $planet->name,
                        'type' => $planet->type,
                        'size' => $planet->size,
                        'temperature' => $planet->temperature,
                        'atmosphere' => $planet->atmosphere,
                        'terrain' => $planet->terrain,
                        'resources' => $planet->resources,
                        'description' => $planet->description,
                    ],
                ],
                'status' => 'success',
            ], 200),
        ]);

        Livewire::test(\App\Livewire\Dashboard::class)
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
        Session::put('sanctum_token', 'test-token');

        // Mock API response
        Http::fake([
            '*/api/auth/user' => Http::response([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'home_planet_id' => null,
                    ],
                ],
                'status' => 'success',
            ], 200),
        ]);

        Livewire::test(\App\Livewire\Dashboard::class)
            ->assertSet('error', 'No home planet found. Please contact support.')
            ->assertSet('loading', false);
    }

    /**
     * Test that dashboard handles API errors gracefully.
     */
    public function test_dashboard_handles_api_errors(): void
    {
        $user = User::factory()->create();

        Auth::login($user);
        Session::put('sanctum_token', 'test-token');

        // Mock API error response
        Http::fake([
            '*/api/auth/user' => Http::response([
                'message' => 'Server error',
            ], 500),
        ]);

        Livewire::test(\App\Livewire\Dashboard::class)
            ->assertSet('error', 'Failed to load planet data: Server error')
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
        Session::put('sanctum_token', 'test-token');

        // Mock API responses
        Http::fake([
            '*/api/auth/user' => Http::response([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'home_planet_id' => $planet->id,
                    ],
                ],
                'status' => 'success',
            ], 200),
            '*/api/users/'.$user->id.'/home-planet' => Http::response([
                'data' => [
                    'planet' => [
                        'id' => $planet->id,
                        'name' => $planet->name,
                        'type' => $planet->type,
                        'size' => $planet->size,
                        'temperature' => $planet->temperature,
                        'atmosphere' => $planet->atmosphere,
                        'terrain' => $planet->terrain,
                        'resources' => $planet->resources,
                        'description' => $planet->description,
                    ],
                ],
                'status' => 'success',
            ], 200),
        ]);

        Livewire::test(\App\Livewire\Dashboard::class)
            ->call('loadUserAndPlanet')
            ->assertSet('user.id', $user->id)
            ->assertSet('planet.id', $planet->id)
            ->assertSet('loading', false);
    }
}
