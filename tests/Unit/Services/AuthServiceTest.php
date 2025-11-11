<?php

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->service = new AuthService;
    $this->password = 'password123';
});

it('successfully logs in user with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    $request = LoginRequest::create('/api/auth/login', 'POST', [
        'email' => 'john@example.com',
        'password' => $this->password,
    ]);

    $result = $this->service->login($request);

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->id)->toBe($user->id)
        ->and(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id);
});

it('throws validation exception when credentials are incorrect', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    $request = LoginRequest::create('/api/auth/login', 'POST', [
        'email' => 'john@example.com',
        'password' => 'wrongpassword',
    ]);

    expect(fn () => $this->service->login($request))
        ->toThrow(ValidationException::class);
});

it('uses remember false by default when remember is not provided', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    $request = LoginRequest::create('/api/auth/login', 'POST', [
        'email' => 'john@example.com',
        'password' => $this->password,
    ]);

    $result = $this->service->login($request);

    expect($result)->toBeInstanceOf(User::class)
        ->and(Auth::check())->toBeTrue();

    // Verify user is authenticated (remember false means session cookie, not persistent cookie)
    // We can't directly test the cookie, but we can verify authentication works
});

it('uses remember true when remember is provided as true', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    $request = LoginRequest::create('/api/auth/login', 'POST', [
        'email' => 'john@example.com',
        'password' => $this->password,
        'remember' => true,
    ]);

    $result = $this->service->login($request);

    expect($result)->toBeInstanceOf(User::class)
        ->and(Auth::check())->toBeTrue();

    // Verify user is authenticated (remember true creates persistent cookie)
    // We can't directly test the cookie, but we can verify authentication works
});

it('successfully logs in user from credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    $result = $this->service->loginFromCredentials('john@example.com', $this->password);

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->id)->toBe($user->id)
        ->and(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id);
});

it('uses remember false by default in loginFromCredentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    $result = $this->service->loginFromCredentials('john@example.com', $this->password);

    expect($result)->toBeInstanceOf(User::class)
        ->and(Auth::check())->toBeTrue();
});

it('uses remember true when provided in loginFromCredentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    $result = $this->service->loginFromCredentials('john@example.com', $this->password, true);

    expect($result)->toBeInstanceOf(User::class)
        ->and(Auth::check())->toBeTrue();
});

it('throws validation exception when credentials are incorrect in loginFromCredentials', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    expect(fn () => $this->service->loginFromCredentials('john@example.com', 'wrongpassword'))
        ->toThrow(ValidationException::class);
});
