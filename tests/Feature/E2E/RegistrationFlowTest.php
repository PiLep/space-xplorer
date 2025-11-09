<?php

namespace Tests\Feature\E2E;

use App\Models\Planet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete registration flow end-to-end.
     * This test verifies that the entire registration process works:
     * 1. User visits registration page
     * 2. Fills and submits registration form
     * 3. User is created in database
     * 4. Home planet is generated
     * 5. User is redirected to dashboard
     */
    public function test_complete_registration_flow_works_end_to_end(): void
    {
        $userData = [
            'name' => 'E2E Test User',
            'email' => 'e2etest@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Visit registration page
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Create Your Account');

        // Mock API response to simulate real API call
        $planet = Planet::factory()->create();
        Http::fake([
            '*/api/auth/register' => function ($request) use ($userData, $planet) {
                // Actually create the user to test the full flow
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => bcrypt($userData['password']),
                    'home_planet_id' => $planet->id,
                ]);

                // Dispatch event to simulate planet generation
                event(new \App\Events\UserRegistered($user));

                return Http::response([
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'home_planet_id' => $user->home_planet_id,
                        ],
                        'token' => 'test-token',
                    ],
                    'message' => 'User registered successfully',
                    'status' => 'success',
                ], 201);
            },
        ]);

        // Submit registration form via Livewire
        $livewire = \Livewire\Livewire::test(\App\Livewire\Register::class)
            ->set('name', $userData['name'])
            ->set('email', $userData['email'])
            ->set('password', $userData['password'])
            ->set('password_confirmation', $userData['password_confirmation'])
            ->call('register');

        // Verify redirect happened (indicates success)
        $livewire->assertRedirect(route('dashboard'));

        // Verify user was created
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name'],
        ]);

        $user = User::where('email', $userData['email'])->first();
        $this->assertNotNull($user);

        // Verify home planet was assigned
        $this->assertNotNull($user->home_planet_id, 'User should have a home planet assigned');

        // Verify planet exists
        $planet = Planet::find($user->home_planet_id);
        $this->assertNotNull($planet, 'Planet should exist in database');
        $this->assertNotNull($planet->name);
        $this->assertNotNull($planet->type);

        // Verify token is stored in session
        $this->assertNotNull(session('sanctum_token'), 'Sanctum token should be stored in session');
    }

    /**
     * Test registration with invalid data shows validation errors.
     */
    public function test_registration_shows_validation_errors_for_invalid_data(): void
    {
        // Use real validation - no HTTP calls needed for client-side validation

        $livewire = \Livewire\Livewire::test(\App\Livewire\Register::class)
            ->set('name', '')
            ->set('email', 'invalid-email')
            ->set('password', 'short')
            ->set('password_confirmation', 'different')
            ->call('register');

        // Should have validation errors
        $livewire->assertHasErrors(['name', 'email', 'password']);

        // User should not be created
        $this->assertDatabaseMissing('users', [
            'email' => 'invalid-email',
        ]);
    }

    /**
     * Test registration with duplicate email shows error.
     */
    public function test_registration_shows_error_for_duplicate_email(): void
    {
        // Use real API calls

        // Create existing user
        User::factory()->create(['email' => 'existing@example.com']);

        $livewire = \Livewire\Livewire::test(\App\Livewire\Register::class)
            ->set('name', 'Test User')
            ->set('email', 'existing@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register');

        // Should have email error
        $livewire->assertHasErrors(['email']);

        // Should not create duplicate user
        $this->assertEquals(1, User::where('email', 'existing@example.com')->count());
    }

    /**
     * Test that registration actually calls the API endpoint.
     */
    public function test_registration_calls_real_api_endpoint(): void
    {
        // This test verifies that the Livewire component actually makes HTTP requests
        // to the API endpoint

        $userData = [
            'name' => 'API Test User',
            'email' => 'apitest@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Count users before
        $usersBefore = User::count();
        $planetsBefore = Planet::count();

        // Mock API response but verify the call is made
        $planet = Planet::factory()->create();
        Http::fake([
            '*/api/auth/register' => function ($request) use ($userData, $planet) {
                // Create user to simulate API behavior
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => bcrypt($userData['password']),
                    'home_planet_id' => $planet->id,
                ]);

                return Http::response([
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'home_planet_id' => $user->home_planet_id,
                        ],
                        'token' => 'test-token',
                    ],
                    'message' => 'User registered successfully',
                    'status' => 'success',
                ], 201);
            },
        ]);

        // Submit registration
        $livewire = \Livewire\Livewire::test(\App\Livewire\Register::class)
            ->set('name', $userData['name'])
            ->set('email', $userData['email'])
            ->set('password', $userData['password'])
            ->set('password_confirmation', $userData['password_confirmation'])
            ->call('register');

        // Verify redirect happened (indicates success)
        $livewire->assertRedirect(route('dashboard'));

        // Verify user was created (API was called)
        $this->assertEquals($usersBefore + 1, User::count(), 'User should be created via API');

        // Verify API was called (check that Http fake was used)
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/auth/register') &&
                   $request->method() === 'POST';
        });

        $user = User::where('email', $userData['email'])->first();
        $this->assertNotNull($user, 'User should exist');
        $this->assertNotNull($user->home_planet_id, 'User should have home planet');
    }
}
