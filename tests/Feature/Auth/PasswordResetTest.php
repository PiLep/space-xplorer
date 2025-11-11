<?php

use App\Events\PasswordResetCompleted;
use App\Events\PasswordResetRequested;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

it('shows forgot password form', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

it('sends password reset link successfully', function () {
    Event::fake();
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this->post('/forgot-password', [
        'email' => 'test@example.com',
    ]);

    $response->assertRedirect(route('password.request'))
        ->assertSessionHas('status');

    Event::assertDispatched(PasswordResetRequested::class, function ($event) {
        return $event->email === 'test@example.com';
    });

    // Verify token was created
    $this->assertDatabaseHas('password_reset_tokens', [
        'email' => 'test@example.com',
    ]);
});

it('returns success message even if email does not exist', function () {
    Event::fake();

    $response = $this->post('/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertRedirect(route('password.request'))
        ->assertSessionHas('status');

    // Should still dispatch event for tracking
    Event::assertDispatched(PasswordResetRequested::class);
});

it('validates email format', function () {
    $response = $this->post('/forgot-password', [
        'email' => 'invalid-email',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('validates email is required', function () {
    $response = $this->post('/forgot-password', []);

    $response->assertSessionHasErrors(['email']);
});

it('shows reset form with valid token', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    $response = $this->get("/reset-password/{$token}?email=test@example.com");

    $response->assertStatus(200);
});

it('redirects if token is invalid', function () {
    $response = $this->get('/reset-password/invalid-token?email=test@example.com');

    $response->assertRedirect(route('password.request'))
        ->assertSessionHasErrors(['email']);
});

it('redirects if email is missing', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    $response = $this->get("/reset-password/{$token}");

    $response->assertRedirect(route('password.request'))
        ->assertSessionHasErrors(['email']);
});

it('redirects if user does not exist', function () {
    $token = 'some-token';

    $response = $this->get("/reset-password/{$token}?email=nonexistent@example.com");

    $response->assertRedirect(route('password.request'))
        ->assertSessionHasErrors(['email']);
});

it('resets password successfully', function () {
    Event::fake();
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect(route('login'))
        ->assertSessionHas('status');

    // Verify password was changed
    $user->refresh();
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();

    // Verify token was deleted
    $this->assertDatabaseMissing('password_reset_tokens', [
        'email' => 'test@example.com',
    ]);

    Event::assertDispatched(PasswordResetCompleted::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

it('invalidates remember me tokens after password reset', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    // Create a session (simulating remember me)
    DB::table('sessions')->insert([
        'id' => 'test-session-id',
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test',
        'payload' => 'test',
        'last_activity' => now()->timestamp,
    ]);

    $token = Password::createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    // Verify session was deleted
    $this->assertDatabaseMissing('sessions', [
        'user_id' => $user->id,
    ]);
});

it('validates password confirmation', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'different',
    ]);

    $response->assertSessionHasErrors(['password']);
});

it('validates password minimum length', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors(['password']);
});

it('validates token is required', function () {
    $response = $this->post('/reset-password', [
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertSessionHasErrors(['token']);
});

it('validates email is required for reset', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('fails with expired token', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    // Manually expire the token by updating created_at
    DB::table('password_reset_tokens')
        ->where('email', 'test@example.com')
        ->update(['created_at' => now()->subHours(2)]);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertSessionHasErrors();
});

it('sends confirmation email after successful reset', function () {
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('oldpassword'),
    ]);

    $token = Password::createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    Mail::assertSent(\App\Mail\PasswordResetConfirmation::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});
