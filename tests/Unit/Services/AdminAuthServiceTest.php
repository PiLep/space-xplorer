<?php

use App\Models\User;
use App\Services\AdminAuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->service = new AdminAuthService;
    $this->password = 'password123';
    // Clear whitelist before each test
    config(['admin.email_whitelist' => '']);
});

it('successfully logs in admin user with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => true,
    ]);

    // Set empty whitelist to allow any admin email
    config(['admin.email_whitelist' => '']);

    $result = $this->service->login('admin@example.com', $this->password);

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->id)->toBe($user->id)
        ->and(Auth::guard('admin')->check())->toBeTrue()
        ->and(Auth::guard('admin')->id())->toBe($user->id);
});

it('successfully logs in admin user when email is in whitelist', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => true,
    ]);

    // Set whitelist with user's email
    config(['admin.email_whitelist' => 'admin@example.com,other@example.com']);

    $result = $this->service->login('admin@example.com', $this->password);

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->id)->toBe($user->id)
        ->and(Auth::guard('admin')->check())->toBeTrue();
});

it('throws validation exception when credentials are incorrect', function () {
    User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => true,
    ]);

    expect(fn () => $this->service->login('admin@example.com', 'wrong-password'))
        ->toThrow(ValidationException::class, 'The provided credentials are incorrect.');
});

it('throws validation exception when user does not exist', function () {
    expect(fn () => $this->service->login('nonexistent@example.com', $this->password))
        ->toThrow(ValidationException::class, 'The provided credentials are incorrect.');
});

it('throws validation exception when user is not super admin', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => false,
    ]);

    expect(fn () => $this->service->login('user@example.com', $this->password))
        ->toThrow(ValidationException::class, 'You do not have admin privileges.');
});

it('throws validation exception when email is not in whitelist', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => true,
    ]);

    // Set whitelist without user's email
    config(['admin.email_whitelist' => 'authorized@example.com']);

    expect(fn () => $this->service->login('admin@example.com', $this->password))
        ->toThrow(ValidationException::class, 'Your email is not authorized to access the admin panel.');
});

it('allows login when whitelist is empty', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => true,
    ]);

    // Empty whitelist should allow any admin
    config(['admin.email_whitelist' => '']);

    $result = $this->service->login('admin@example.com', $this->password);

    expect($result)->toBeInstanceOf(User::class)
        ->and(Auth::guard('admin')->check())->toBeTrue();
});

it('handles whitelist with spaces correctly', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => true,
    ]);

    // Whitelist with spaces should be trimmed
    config(['admin.email_whitelist' => ' admin@example.com , other@example.com ']);

    $result = $this->service->login('admin@example.com', $this->password);

    expect($result)->toBeInstanceOf(User::class)
        ->and(Auth::guard('admin')->check())->toBeTrue();
});

it('handles whitelist with empty values correctly', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => true,
    ]);

    // Whitelist with empty values should be filtered
    config(['admin.email_whitelist' => 'admin@example.com,,other@example.com']);

    $result = $this->service->login('admin@example.com', $this->password);

    expect($result)->toBeInstanceOf(User::class)
        ->and(Auth::guard('admin')->check())->toBeTrue();
});

it('successfully logs out admin user', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => true,
    ]);

    Auth::guard('admin')->login($user);
    expect(Auth::guard('admin')->check())->toBeTrue();

    $this->service->logout();

    expect(Auth::guard('admin')->check())->toBeFalse();
});
