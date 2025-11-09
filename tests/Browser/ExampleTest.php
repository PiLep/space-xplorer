<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can visit the registration page', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
    $response->assertSee('Create Your Account');
});

it('can complete registration flow with browser', function () {
    // This test uses Playwright via Laravel Dusk-like approach
    // For now, we'll use HTTP tests, but you can extend this with Playwright

    $userData = [
        'name' => 'Browser Test User',
        'email' => 'browsertest@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    // Visit registration page
    $response = $this->get('/register');
    $response->assertStatus(200);

    // Submit registration via API (simulating browser form submission)
    $response = $this->postJson('/api/auth/register', $userData);

    $response->assertStatus(201);

    // Verify user was created
    $this->assertDatabaseHas('users', [
        'email' => $userData['email'],
        'name' => $userData['name'],
    ]);

    $user = User::where('email', $userData['email'])->first();
    expect($user)->not->toBeNull()
        ->and($user->home_planet_id)->not->toBeNull();
});
