<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
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
        Session::put('sanctum_token', 'test-token');

        // Mock API response
        Http::fake([
            '*/api/auth/user' => Http::response([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                        'home_planet_id' => null,
                    ],
                ],
                'status' => 'success',
            ], 200),
        ]);

        Livewire::test(\App\Livewire\Profile::class)
            ->assertSet('user.id', $user->id)
            ->assertSet('name', 'John Doe')
            ->assertSet('email', 'john@example.com')
            ->assertSet('loading', false);
    }

    /**
     * Test that profile validates email format.
     */
    public function test_profile_validates_email_format(): void
    {
        $user = User::factory()->create();

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

        Livewire::test(\App\Livewire\Profile::class)
            ->set('email', 'invalid-email')
            ->call('updateProfile')
            ->assertHasErrors(['email']);
    }

    /**
     * Test successful profile update.
     */
    public function test_successful_profile_update(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        Auth::login($user);
        Session::put('sanctum_token', 'test-token');

        // Mock API responses
        Http::fake([
            '*/api/auth/user' => Http::response([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                        'home_planet_id' => null,
                    ],
                ],
                'status' => 'success',
            ], 200),
            '*/api/users/'.$user->id => Http::response([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => 'Updated Name',
                        'email' => 'updated@example.com',
                        'home_planet_id' => null,
                    ],
                ],
                'message' => 'Profile updated successfully',
                'status' => 'success',
            ], 200),
        ]);

        Livewire::test(\App\Livewire\Profile::class)
            ->set('name', 'Updated Name')
            ->set('email', 'updated@example.com')
            ->call('updateProfile')
            ->assertSet('success', 'Profile updated successfully!')
            ->assertSet('saving', false);
    }

    /**
     * Test that profile update handles no changes.
     */
    public function test_profile_update_handles_no_changes(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        Auth::login($user);
        Session::put('sanctum_token', 'test-token');

        // Mock API response
        Http::fake([
            '*/api/auth/user' => Http::response([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                        'home_planet_id' => null,
                    ],
                ],
                'status' => 'success',
            ], 200),
        ]);

        Livewire::test(\App\Livewire\Profile::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->call('updateProfile')
            ->assertSet('success', 'No changes to save.')
            ->assertSet('saving', false);
    }

    /**
     * Test that profile update handles API validation errors.
     */
    public function test_profile_update_handles_validation_errors(): void
    {
        $user = User::factory()->create();

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
                        'home_planet_id' => null,
                    ],
                ],
                'status' => 'success',
            ], 200),
            '*/api/users/'.$user->id => Http::response([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['The email has already been taken.'],
                ],
            ], 422),
        ]);

        Livewire::test(\App\Livewire\Profile::class)
            ->set('email', 'existing@example.com')
            ->call('updateProfile')
            ->assertHasErrors(['email'])
            ->assertSet('saving', false);
    }

    /**
     * Test that profile update handles API errors gracefully.
     */
    public function test_profile_update_handles_api_errors(): void
    {
        $user = User::factory()->create();

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
                        'home_planet_id' => null,
                    ],
                ],
                'status' => 'success',
            ], 200),
            '*/api/users/'.$user->id => Http::response([
                'message' => 'Server error',
            ], 500),
        ]);

        Livewire::test(\App\Livewire\Profile::class)
            ->set('name', 'Updated Name')
            ->call('updateProfile')
            ->assertSet('error', 'Server error')
            ->assertSet('saving', false);
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
        Session::put('sanctum_token', 'test-token');

        // Mock API response
        Http::fake([
            '*/api/auth/user' => Http::response([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                        'home_planet_id' => null,
                    ],
                ],
                'status' => 'success',
            ], 200),
        ]);

        Livewire::test(\App\Livewire\Profile::class)
            ->call('loadUser')
            ->assertSet('user.id', $user->id)
            ->assertSet('name', 'John Doe')
            ->assertSet('email', 'john@example.com')
            ->assertSet('loading', false);
    }
}
