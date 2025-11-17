<?php

use App\Events\SessionExpired;
use App\Models\User;

it('creates SessionExpired event with user', function () {
    $user = User::factory()->create();

    $event = new SessionExpired($user);

    expect($event->user)->toBe($user)
        ->and($event->shouldBroadcast)->toBeFalse();
});

it('has correct user property', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    $event = new SessionExpired($user);

    expect($event->user)->toBeInstanceOf(User::class)
        ->and($event->user->id)->toBe($user->id)
        ->and($event->user->email)->toBe('test@example.com')
        ->and($event->user->name)->toBe('Test User');
});

it('does not broadcast by default', function () {
    $user = User::factory()->create();
    $event = new SessionExpired($user);

    expect($event->shouldBroadcast)->toBeFalse();
});

it('serializes user model correctly', function () {
    $user = User::factory()->create();

    $event = new SessionExpired($user);

    // Event should be serializable (uses SerializesModels trait)
    $serialized = serialize($event);
    $unserialized = unserialize($serialized);

    expect($unserialized->user->id)->toBe($user->id)
        ->and($unserialized->user->email)->toBe($user->email);
});

it('can be dispatched', function () {
    $user = User::factory()->create();
    $event = new SessionExpired($user);

    // Event uses Dispatchable trait, so it should be dispatchable
    expect($event)->toBeInstanceOf(SessionExpired::class);
});

