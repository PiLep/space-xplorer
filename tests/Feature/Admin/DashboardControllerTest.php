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

    config(['admin.email_whitelist' => '']);
    Auth::guard('admin')->login($this->admin);
});

it('displays the admin dashboard', function () {
    $response = $this->get('/admin/dashboard');

    $response->assertStatus(200)
        ->assertViewIs('admin.dashboard');
});

it('shows total users count', function () {
    // Create some users
    User::factory()->count(5)->create();

    $response = $this->get('/admin/dashboard');

    $response->assertStatus(200)
        ->assertViewHas('totalUsers', 6); // 5 new + 1 admin
});

it('shows recent users', function () {
    // Create some users
    $users = User::factory()->count(10)->create();

    $response = $this->get('/admin/dashboard');

    $response->assertStatus(200)
        ->assertViewHas('recentUsers');

    $recentUsers = $response->viewData('recentUsers');
    expect($recentUsers)->toHaveCount(5); // Should show only 5 most recent
});

it('shows users ordered by latest first', function () {
    $oldUser = User::factory()->create(['created_at' => now()->subDays(5)]);
    $newUser = User::factory()->create(['created_at' => now()]);

    $response = $this->get('/admin/dashboard');

    $recentUsers = $response->viewData('recentUsers');
    // Check that the newest user is before the old user in the list
    $newUserIndex = $recentUsers->search(function ($user) use ($newUser) {
        return $user->id === $newUser->id;
    });
    $oldUserIndex = $recentUsers->search(function ($user) use ($oldUser) {
        return $user->id === $oldUser->id;
    });
    expect($newUserIndex)->toBeLessThan($oldUserIndex);
});

it('requires authentication', function () {
    Auth::guard('admin')->logout();

    $response = $this->get('/admin/dashboard');

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

    $response = $this->get('/admin/dashboard');

    $response->assertRedirect(route('admin.login'));
});
