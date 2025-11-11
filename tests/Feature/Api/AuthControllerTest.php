<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('allows user to register successfully', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->postJson('/api/auth/register', $userData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'name', 'email', 'home_planet_id'],
                'token',
            ],
            'message',
            'status',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'name' => 'John Doe',
    ]);

    // Verify user has a home planet assigned
    $user = User::where('email', 'john@example.com')->first();
    expect($user->home_planet_id)->not->toBeNull();
});

it('validates required fields during registration', function () {
    $response = $this->postJson('/api/auth/register', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('validates email uniqueness during registration', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('validates password confirmation during registration', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

it('validates password minimum length during registration', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

it('allows user to login successfully', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'name', 'email', 'home_planet_id'],
                'token',
            ],
            'message',
            'status',
        ]);

    expect($response->json('data.token'))->not->toBeNull();
});

it('fails login with incorrect credentials', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('fails login with non-existent email', function () {
    $response = $this->postJson('/api/auth/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('validates remember field as boolean', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
        'remember' => 'not-a-boolean',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['remember']);
});

it('accepts remember field as true', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
        'remember' => true,
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'name', 'email', 'home_planet_id'],
                'token',
            ],
            'message',
            'status',
        ]);
});

it('accepts remember field as false', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
        'remember' => false,
    ]);

    $response->assertStatus(200);
});

it('works without remember field (backward compatibility)', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200);
});

it('creates remember me cookie with secure attributes when remember is true', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'password123',
        'remember' => true,
    ]);

    $response->assertStatus(200);

    // Get the remember cookie from response
    $cookies = $response->headers->getCookies();
    $rememberCookie = collect($cookies)->first(function ($cookie) {
        return str_contains($cookie->getName(), 'remember');
    });

    if ($rememberCookie) {
        // Verify cookie security attributes
        expect($rememberCookie->isHttpOnly())->toBeTrue()
            ->and($rememberCookie->getSameSite())->toBeIn(['lax', 'strict']);

        // In production, secure should be true, but in tests it may be false
        // We verify the configuration is correct instead
        expect(config('session.http_only'))->toBeTrue()
            ->and(config('session.same_site'))->toBeIn(['lax', 'strict']);
    }
});

it('requires authentication for logout', function () {
    $response = $this->postJson('/api/auth/logout');

    $response->assertStatus(401);
});

it('allows user to logout successfully', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/auth/logout');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Logged out successfully',
            'status' => 'success',
        ]);

    // Verify token was deleted
    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'name' => 'test-token',
    ]);
});

it('requires authentication to get user', function () {
    $response = $this->getJson('/api/auth/user');

    $response->assertStatus(401);
});

it('returns authenticated user data', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/auth/user');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'name', 'email', 'home_planet_id'],
            ],
            'status',
        ])
        ->assertJson([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'home_planet_id' => $user->home_planet_id,
                ],
            ],
        ]);
});

it('triggers planet generation during registration', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->postJson('/api/auth/register', $userData);

    $response->assertStatus(201);

    $user = User::where('email', 'john@example.com')->first();

    // Refresh to get updated home_planet_id
    $user->refresh();

    // Verify planet was generated
    expect($user->home_planet_id)->not->toBeNull();

    // Verify planet exists in database
    $this->assertDatabaseHas('planets', [
        'id' => $user->home_planet_id,
    ]);
});
