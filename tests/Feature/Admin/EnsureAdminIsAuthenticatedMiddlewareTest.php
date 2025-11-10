<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->password = 'password123';
    $this->admin = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => true,
    ]);

    // Set empty whitelist for tests
    config(['admin.email_whitelist' => '']);
});

it('redirects unauthenticated users to login', function () {
    $response = $this->get('/admin/users');

    // Middleware redirects to login (may be default login route)
    $response->assertRedirect();
});

it('allows authenticated super admin to access protected routes', function () {
    Auth::guard('admin')->login($this->admin);

    $response = $this->get('/admin/users');

    $response->assertStatus(200);
});

it('redirects and logs out user without super admin flag', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => false,
    ]);

    Auth::guard('admin')->login($user);

    $response = $this->get('/admin/users');

    $response->assertRedirect(route('admin.login'))
        ->assertSessionHasErrors(['email']);

    expect(Auth::guard('admin')->check())->toBeFalse();
});

it('invalidates session when user loses super admin privileges', function () {
    Auth::guard('admin')->login($this->admin);

    // Remove super admin flag
    $this->admin->update(['is_super_admin' => false]);

    $response = $this->get('/admin/users');

    $response->assertRedirect(route('admin.login'))
        ->assertSessionHasErrors(['email']);

    expect(Auth::guard('admin')->check())->toBeFalse();
});

it('redirects and logs out user when email is not in whitelist', function () {
    config(['admin.email_whitelist' => 'authorized@example.com']);

    Auth::guard('admin')->login($this->admin);

    $response = $this->get('/admin/users');

    $response->assertRedirect(route('admin.login'))
        ->assertSessionHasErrors(['email']);

    expect(Auth::guard('admin')->check())->toBeFalse();
});

it('allows access when whitelist is empty', function () {
    config(['admin.email_whitelist' => '']);

    Auth::guard('admin')->login($this->admin);

    $response = $this->get('/admin/users');

    $response->assertStatus(200);
});

it('allows access when email is in whitelist', function () {
    config(['admin.email_whitelist' => 'admin@example.com,other@example.com']);

    Auth::guard('admin')->login($this->admin);

    $response = $this->get('/admin/users');

    $response->assertStatus(200);
});

it('handles whitelist with spaces correctly', function () {
    config(['admin.email_whitelist' => ' admin@example.com , other@example.com ']);

    Auth::guard('admin')->login($this->admin);

    $response = $this->get('/admin/users');

    $response->assertStatus(200);
});

it('regenerates session token when user is rejected', function () {
    Auth::guard('admin')->login($this->admin);
    $token = session()->token();

    // Remove super admin flag
    $this->admin->update(['is_super_admin' => false]);

    $response = $this->get('/admin/users');

    expect(session()->token())->not->toBe($token);
});
