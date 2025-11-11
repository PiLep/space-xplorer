<?php

use App\Events\PasswordResetRequested;

it('can be instantiated with email', function () {
    $email = 'test@example.com';
    $event = new PasswordResetRequested($email);

    expect($event->email)->toBe($email)
        ->and($event->shouldBroadcast)->toBeFalse();
});

it('stores email correctly', function () {
    $email = 'user@example.com';
    $event = new PasswordResetRequested($email);

    expect($event->email)->toBe('user@example.com');
});
