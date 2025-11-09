<?php

namespace Tests\Feature\Livewire;

use App\Models\Planet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
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
        Livewire::test(\App\Livewire\Register::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        // Verify token was stored in session
        $this->assertNotNull(Session::get('sanctum_token'));
        $this->assertTrue(Auth::check());

        // Verify user was created
        $user = Auth::user();
        $this->assertNotNull($user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
    }

    /**
     * Test that registration handles duplicate email.
     */
    public function test_registration_handles_duplicate_email(): void
    {
        // Create existing user
        \App\Models\User::factory()->create(['email' => 'existing@example.com']);

        Livewire::test(\App\Livewire\Register::class)
            ->set('name', 'John Doe')
            ->set('email', 'existing@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    /**
     * Test that registration creates user with home planet.
     */
    public function test_registration_creates_user_with_home_planet(): void
    {
        Livewire::test(\App\Livewire\Register::class)
            ->set('name', 'John Doe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        // Verify user was created with home planet
        $user = Auth::user();
        $this->assertNotNull($user);
        $this->assertNotNull($user->home_planet_id);
        $this->assertNotNull($user->homePlanet);
    }
}
