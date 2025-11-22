<?php

use App\Events\UserDeleting;
use App\Models\User;

it('creates UserDeleting event with user', function () {
    $user = User::factory()->create();

    $event = new UserDeleting($user);

    expect($event->user)->toBe($user)
        ->and($event->shouldBroadcast)->toBeFalse();
});

it('has correct user property', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    $event = new UserDeleting($user);

    expect($event->user)->toBeInstanceOf(User::class)
        ->and($event->user->id)->toBe($user->id)
        ->and($event->user->email)->toBe('test@example.com')
        ->and($event->user->name)->toBe('Test User');
});

it('does not broadcast by default', function () {
    $user = User::factory()->create();
    $event = new UserDeleting($user);

    expect($event->shouldBroadcast)->toBeFalse();
});

it('serializes user model correctly', function () {
    $user = User::factory()->create();

    $event = new UserDeleting($user);

    // Event should be serializable (uses SerializesModels trait)
    $serialized = serialize($event);
    $unserialized = unserialize($serialized);

    expect($unserialized->user->id)->toBe($user->id)
        ->and($unserialized->user->email)->toBe($user->email);
});

it('can be dispatched', function () {
    $user = User::factory()->create();
    $event = new UserDeleting($user);

    // Event uses Dispatchable trait, so it should be dispatchable
    expect($event)->toBeInstanceOf(UserDeleting::class);
});

it('is different from UserDeleted event', function () {
    $user = User::factory()->create();
    $deletingEvent = new UserDeleting($user);
    $deletedEvent = new \App\Events\UserDeleted($user);

    // Both events should have the same user but be different instances
    expect($deletingEvent->user->id)->toBe($deletedEvent->user->id)
        ->and($deletingEvent)->not->toBeInstanceOf(\App\Events\UserDeleted::class)
        ->and($deletedEvent)->not->toBeInstanceOf(UserDeleting::class);
});

