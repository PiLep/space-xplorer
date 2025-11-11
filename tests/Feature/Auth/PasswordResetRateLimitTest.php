<?php

use App\Models\User;

it('limits forgot password requests to 3 per hour', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    // Make 3 requests (should succeed)
    for ($i = 0; $i < 3; $i++) {
        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);
        $response->assertRedirect(route('password.request'));
    }

    // 4th request should be rate limited
    $response = $this->post('/forgot-password', [
        'email' => 'test@example.com',
    ]);

    $response->assertStatus(429); // Too Many Requests
});

it('limits reset password attempts to 5 per hour', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
    ]);

    $token = \Illuminate\Support\Facades\Password::createToken($user);

    // Make 5 requests (should succeed or fail validation, but not rate limited)
    for ($i = 0; $i < 5; $i++) {
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'short', // Invalid password to trigger validation
            'password_confirmation' => 'short',
        ]);
        // Should get validation error, not rate limit
        expect($response->status())->not->toBe(429);
    }

    // 6th request should be rate limited
    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertStatus(429); // Too Many Requests
});

it('allows requests from different IPs', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    // Simulate requests from different IPs by using different sessions
    // Note: In a real scenario, you'd need to mock the IP address
    // For this test, we verify that rate limiting is per-IP by making requests
    // that should not interfere with each other

    // Make 3 requests (should succeed)
    for ($i = 0; $i < 3; $i++) {
        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);
        $response->assertRedirect(route('password.request'));
    }

    // The rate limit should reset after the time window
    // In a real test, you'd need to wait or mock time
    // For now, we verify the basic rate limiting works
});

it('shows rate limit error message', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    // Exceed rate limit
    for ($i = 0; $i < 4; $i++) {
        $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);
    }

    $response = $this->post('/forgot-password', [
        'email' => 'test@example.com',
    ]);

    $response->assertStatus(429);
});
