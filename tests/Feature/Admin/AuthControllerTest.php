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

it('shows the admin login form', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200)
        ->assertViewIs('admin.login');
});

it('redirects authenticated admin away from login form', function () {
    Auth::guard('admin')->login($this->admin);

    $response = $this->get('/admin/login');

    // Guest middleware redirects authenticated users away from login
    $response->assertRedirect();
});

it('successfully logs in admin user with valid credentials', function () {
    $response = $this->post('/admin/login', [
        'email' => 'admin@example.com',
        'password' => $this->password,
    ]);

    $response->assertRedirect(route('admin.users.index'))
        ->assertSessionHasNoErrors();

    expect(Auth::guard('admin')->check())->toBeTrue()
        ->and(Auth::guard('admin')->id())->toBe($this->admin->id);
});

it('regenerates session on successful login', function () {
    $sessionId = session()->getId();

    $response = $this->post('/admin/login', [
        'email' => 'admin@example.com',
        'password' => $this->password,
    ]);

    $response->assertRedirect(route('admin.users.index'));

    expect(session()->getId())->not->toBe($sessionId);
});

it('redirects back with errors when credentials are incorrect', function () {
    $response = $this->post('/admin/login', [
        'email' => 'admin@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['email'])
        ->assertSessionHasInput('email', 'admin@example.com');

    expect(Auth::guard('admin')->check())->toBeFalse();
});

it('redirects back with errors when user is not super admin', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => false,
    ]);

    $response = $this->post('/admin/login', [
        'email' => 'user@example.com',
        'password' => $this->password,
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['email']);

    expect(Auth::guard('admin')->check())->toBeFalse();
});

it('redirects back with errors when email is not in whitelist', function () {
    config(['admin.email_whitelist' => 'authorized@example.com']);

    $response = $this->post('/admin/login', [
        'email' => 'admin@example.com',
        'password' => $this->password,
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['email']);

    expect(Auth::guard('admin')->check())->toBeFalse();
});

it('validates required fields', function () {
    $response = $this->post('/admin/login', []);

    $response->assertSessionHasErrors(['email', 'password']);
});

it('validates email format', function () {
    $response = $this->post('/admin/login', [
        'email' => 'invalid-email',
        'password' => $this->password,
    ]);

    $response->assertSessionHasErrors(['email']);
});

it('successfully logs out admin user', function () {
    Auth::guard('admin')->login($this->admin);

    $response = $this->post('/admin/logout');

    $response->assertRedirect(route('admin.login'));

    expect(Auth::guard('admin')->check())->toBeFalse();
});

it('invalidates session on logout', function () {
    Auth::guard('admin')->login($this->admin);
    $sessionId = session()->getId();

    $response = $this->post('/admin/logout');

    $response->assertRedirect(route('admin.login'));

    // Session should be regenerated
    expect(session()->getId())->not->toBe($sessionId);
});

it('redirects to intended URL after login', function () {
    $response = $this->get('/admin/users');

    // Should be redirected to login (guest middleware may redirect to default login)
    $response->assertRedirect();

    // Now login
    $response = $this->post('/admin/login', [
        'email' => 'admin@example.com',
        'password' => $this->password,
    ]);

    // Should redirect to intended URL
    $response->assertRedirect(route('admin.users.index'));
});
