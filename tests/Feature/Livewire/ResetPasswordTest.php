<?php

use App\Events\PasswordResetCompleted;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

it('renders reset password component', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    Livewire::test(\App\Livewire\ResetPassword::class, [
        'token' => $token,
        'email' => 'test@example.com',
    ])->assertStatus(200);
});

it('initializes with token and email', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    Livewire::test(\App\Livewire\ResetPassword::class, [
        'token' => $token,
        'email' => 'test@example.com',
    ])
        ->assertSet('token', $token)
        ->assertSet('email', 'test@example.com');
});

it('validates password is required', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    Livewire::test(\App\Livewire\ResetPassword::class, [
        'token' => $token,
        'email' => 'test@example.com',
    ])
        ->set('password', '')
        ->call('resetPassword')
        ->assertHasErrors(['password']);
});

it('validates password minimum length', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    Livewire::test(\App\Livewire\ResetPassword::class, [
        'token' => $token,
        'email' => 'test@example.com',
    ])
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('resetPassword')
        ->assertHasErrors(['password']);
});

it('validates password confirmation', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    Livewire::test(\App\Livewire\ResetPassword::class, [
        'token' => $token,
        'email' => 'test@example.com',
    ])
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'different')
        ->call('resetPassword')
        ->assertHasErrors(['password']);
});

it('resets password successfully', function () {
    Event::fake();
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $token = Password::createToken($user);

    Livewire::test(\App\Livewire\ResetPassword::class, [
        'token' => $token,
        'email' => 'test@example.com',
    ])
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertRedirect(route('login'));

    // Verify password was changed
    $user->refresh();
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();

    Event::assertDispatched(PasswordResetCompleted::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

it('calculates password strength', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    $component = Livewire::test(\App\Livewire\ResetPassword::class, [
        'token' => $token,
        'email' => 'test@example.com',
    ]);

    // Test weak password
    $component->set('password', 'short')
        ->assertSet('passwordStrength', function ($strength) {
            return str_contains($strength, '[WEAK]') || str_contains($strength, '[MEDIUM]');
        });

    // Test strong password
    $component->set('password', 'StrongP@ssw0rd123')
        ->assertSet('passwordStrength', function ($strength) {
            return str_contains($strength, '[STRONG]') || str_contains($strength, '[GOOD]');
        });
});

it('shows error for invalid token', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    Livewire::test(\App\Livewire\ResetPassword::class, [
        'token' => 'invalid-token',
        'email' => 'test@example.com',
    ])
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertHasErrors(['password'])
        ->assertSet('status', function ($status) {
            return str_contains($status, '[ERROR]');
        });
});

it('invalidates sessions after password reset', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    // Create a session
    DB::table('sessions')->insert([
        'id' => 'test-session-id',
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test',
        'payload' => 'test',
        'last_activity' => now()->timestamp,
    ]);

    $token = Password::createToken($user);

    Livewire::test(\App\Livewire\ResetPassword::class, [
        'token' => $token,
        'email' => 'test@example.com',
    ])
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('resetPassword');

    // Verify session was deleted
    $this->assertDatabaseMissing('sessions', [
        'user_id' => $user->id,
    ]);
});
