<?php

use App\Models\Planet;
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

    config(['admin.email_whitelist' => '']);
    Auth::guard('admin')->login($this->admin);
});

it('displays a listing of users', function () {
    User::factory()->count(15)->create();

    $response = $this->get('/admin/users');

    $response->assertStatus(200)
        ->assertViewIs('admin.users.index')
        ->assertViewHas('users');
});

it('paginates users with 20 per page', function () {
    User::factory()->count(25)->create();

    $response = $this->get('/admin/users');

    $users = $response->viewData('users');
    expect($users)->toHaveCount(20);
});

it('shows second page of users', function () {
    User::factory()->count(25)->create();

    $response = $this->get('/admin/users?page=2');

    $users = $response->viewData('users');
    expect($users)->toHaveCount(6); // 25 + 1 admin - 20 from first page
});

it('orders users by latest first', function () {
    $oldUser = User::factory()->create(['created_at' => now()->subDays(5)]);
    $newUser = User::factory()->create(['created_at' => now()]);

    $response = $this->get('/admin/users');

    $users = $response->viewData('users');
    // Check that the newest user is before the old user in the list
    $newUserIndex = $users->search(function ($user) use ($newUser) {
        return $user->id === $newUser->id;
    });
    $oldUserIndex = $users->search(function ($user) use ($oldUser) {
        return $user->id === $oldUser->id;
    });
    expect($newUserIndex)->toBeLessThan($oldUserIndex);
});

it('eager loads home planet relationship', function () {
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $user->update(['home_planet_id' => $planet->id]);

    $response = $this->get('/admin/users');

    $users = $response->viewData('users');
    $foundUser = $users->firstWhere('id', $user->id);
    expect($foundUser->relationLoaded('homePlanet'))->toBeTrue();
});

it('displays a specific user', function () {
    $user = User::factory()->create();

    $response = $this->get("/admin/users/{$user->id}");

    $response->assertStatus(200)
        ->assertViewIs('admin.users.show')
        ->assertViewHas('user', $user);
});

it('eager loads home planet when showing user', function () {
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $user->update(['home_planet_id' => $planet->id]);

    $response = $this->get("/admin/users/{$user->id}");

    $viewUser = $response->viewData('user');
    expect($viewUser->relationLoaded('homePlanet'))->toBeTrue();
});

it('requires authentication', function () {
    Auth::guard('admin')->logout();

    $response = $this->get('/admin/users');

    // Middleware may redirect to default login route
    $response->assertRedirect();
});

it('requires super admin privileges', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => false,
    ]);

    Auth::guard('admin')->login($user);

    $response = $this->get('/admin/users');

    $response->assertRedirect(route('admin.login'));
});

it('returns 404 for non-existent user', function () {
    $response = $this->get('/admin/users/non-existent-id');

    $response->assertStatus(404);
});
