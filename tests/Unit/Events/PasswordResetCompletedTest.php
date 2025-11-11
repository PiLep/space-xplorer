<?php

use App\Events\PasswordResetCompleted;
use App\Models\User;

it('can be instantiated with user', function () {
    $user = User::factory()->create();
    $event = new PasswordResetCompleted($user);

    expect($event->user)->toBe($user)
        ->and($event->shouldBroadcast)->toBeFalse();
});

it('stores user correctly', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    $event = new PasswordResetCompleted($user);

    expect($event->user->id)->toBe($user->id)
        ->and($event->user->email)->toBe('test@example.com')
        ->and($event->user->name)->toBe('Test User');
});
