<?php

namespace Tests\Feature\Livewire;

use App\Models\Planet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the register component renders successfully.
     */
    public function test_register_component_renders(): void
    {
        Livewire::test(\App\Livewire\Register::class)
            ->assertStatus(200);
    }

    /**
     * Test that registration validates required fields.
     */
    public function test_registration_validates_required_fields(): void
    {
        Livewire::test(\App\Livewire\Register::class)
            ->set('name', '')
            ->set('email', '')
            ->set('password', '')
            ->call('register')
            ->assertHasErrors(['name', 'email', 'password']);
    }

    /**
     * Test that registration validates email format.
     */
    public function test_registration_validates_email_format(): void
    {
        Livewire::test(\App\Livewire\Register::class)
            ->set('name', 'John Doe')
            ->set('email', 'invalid-email')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    /**
     * Test that registration validates password confirmation.
     */
    public function test_registration_validates_password_confirmation(): void
    {
        Livewire::test(\App\Livewire\Register::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'different')
            ->call('register')
            ->assertHasErrors(['password']);
    }

    /**
     * Test that registration validates password minimum length.
     */
    public function test_registration_validates_password_minimum_length(): void
    {
        Livewire::test(\App\Livewire\Register::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('register')
            ->assertHasErrors(['password']);
    }

    /**
     * Test successful registration.
     */
    public function test_successful_registration(): void
    {
        $planet = Planet::factory()->create();

        // Mock the API response
        Http::fake([
            '*/api/auth/register' => Http::response([
                'data' => [
                    'user' => [
                        'id' => 1,
                        'name' => 'John Doe',
                        'email' => 'john@example.com',
                        'home_planet_id' => $planet->id,
                    ],
                    'token' => 'test-token',
                ],
                'message' => 'User registered successfully',
                'status' => 'success',
            ], 201),
        ]);

        Livewire::test(\App\Livewire\Register::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        // Verify token was stored in session
        $this->assertEquals('test-token', Session::get('sanctum_token'));
    }

    /**
     * Test that registration handles API validation errors.
     */
    public function test_registration_handles_api_validation_errors(): void
    {
        // Mock API validation error response
        Http::fake([
            '*/api/auth/register' => Http::response([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['The email has already been taken.'],
                ],
            ], 422),
        ]);

        Livewire::test(\App\Livewire\Register::class)
            ->set('name', 'John Doe')
            ->set('email', 'existing@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    /**
     * Test that registration handles API errors gracefully.
     */
    public function test_registration_handles_api_errors(): void
    {
        // Mock API error response
        Http::fake([
            '*/api/auth/register' => Http::response([
                'message' => 'Server error',
            ], 500),
        ]);

        Livewire::test(\App\Livewire\Register::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);
    }
}
