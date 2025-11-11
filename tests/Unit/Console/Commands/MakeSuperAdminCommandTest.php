<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

it('makes a user super admin successfully', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'is_super_admin' => false,
    ]);

    $exitCode = Artisan::call('admin:make', ['email' => 'admin@example.com']);

    expect(Artisan::output())->toContain("User 'admin@example.com' has been made a super admin")
        ->and($exitCode)->toBe(0);

    $user->refresh();
    expect($user->is_super_admin)->toBeTrue();
});

it('handles non-existent user', function () {
    $exitCode = Artisan::call('admin:make', ['email' => 'nonexistent@example.com']);

    expect(Artisan::output())->toContain("User with email 'nonexistent@example.com' not found")
        ->and($exitCode)->toBe(1);
});

it('handles user already super admin', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'is_super_admin' => true,
    ]);

    $exitCode = Artisan::call('admin:make', ['email' => 'admin@example.com']);

    expect(Artisan::output())->toContain("User 'admin@example.com' is already a super admin")
        ->and($exitCode)->toBe(0);
});

it('displays reminder about admin email whitelist', function () {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'is_super_admin' => false,
    ]);

    Artisan::call('admin:make', ['email' => 'admin@example.com']);

    expect(Artisan::output())->toContain("Don't forget to add this email to ADMIN_EMAIL_WHITELIST");
});
