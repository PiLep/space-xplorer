<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        Livewire::test(\App\Livewire\LoginTerminal::class)
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        // Verify token was stored in session
        $this->assertNotNull(Session::get('sanctum_token'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    /**
     * Test that login handles invalid credentials.
     */
    public function test_login_handles_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(\App\Livewire\LoginTerminal::class)
            ->set('email', 'john@example.com')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    /**
     * Test that login handles non-existent user.
     */
    public function test_login_handles_non_existent_user(): void
    {
        Livewire::test(\App\Livewire\LoginTerminal::class)
            ->set('email', 'nonexistent@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['email']);
    }
}
