<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the login component renders successfully.
     */
    public function test_login_component_renders(): void
    {
        Livewire::test(\App\Livewire\LoginTerminal::class)
            ->assertStatus(200);
    }

    /**
     * Test that login validates required fields.
     */
    public function test_login_validates_required_fields(): void
    {
        Livewire::test(\App\Livewire\LoginTerminal::class)
            ->set('email', '')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['email', 'password']);
    }

    /**
     * Test that login validates email format.
     */
    public function test_login_validates_email_format(): void
    {
        Livewire::test(\App\Livewire\LoginTerminal::class)
            ->set('email', 'invalid-email')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    /**
     * Test successful login.
     */
    public function test_successful_login(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Mock the API response
        Http::fake([
            '*/api/auth/login' => Http::response([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'home_planet_id' => null,
                    ],
                    'token' => 'test-token',
                ],
                'message' => 'Logged in successfully',
                'status' => 'success',
            ], 200),
        ]);

        Livewire::test(\App\Livewire\LoginTerminal::class)
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        // Verify token was stored in session
        $this->assertEquals('test-token', Session::get('sanctum_token'));
    }

    /**
     * Test that login handles invalid credentials.
     */
    public function test_login_handles_invalid_credentials(): void
    {
        // Mock API error response for invalid credentials
        Http::fake([
            '*/api/auth/login' => Http::response([
                'message' => 'The provided credentials are incorrect.',
                'errors' => [
                    'email' => ['The provided credentials are incorrect.'],
                ],
            ], 422),
        ]);

        Livewire::test(\App\Livewire\LoginTerminal::class)
            ->set('email', 'john@example.com')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    /**
     * Test that login handles API errors gracefully.
     */
    public function test_login_handles_api_errors(): void
    {
        // Mock API error response
        Http::fake([
            '*/api/auth/login' => Http::response([
                'message' => 'Server error',
            ], 500),
        ]);

        Livewire::test(\App\Livewire\LoginTerminal::class)
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['email']);
    }
}
