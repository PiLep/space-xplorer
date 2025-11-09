<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the profile component renders successfully.
     */
    public function test_profile_component_renders(): void
    {
        $user = User::factory()->create();

        Auth::login($user);

        Livewire::test(\App\Livewire\Profile::class)
            ->assertStatus(200)
            ->assertSet('user.id', $user->id)
            ->assertSet('loading', false);
    }

    /**
     * Test that profile loads user data on mount.
     */
    public function test_profile_loads_user_data(): void
    {
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
    }

    /**
     * Test that profile can reload user data.
     */
    public function test_profile_can_reload_user_data(): void
    {
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
    }

    /**
     * Test that profile handles unauthenticated user.
     */
    public function test_profile_handles_unauthenticated_user(): void
    {
        Livewire::test(\App\Livewire\Profile::class)
            ->assertSet('error', 'You must be logged in to view your profile.')
            ->assertSet('loading', false);
    }
}
