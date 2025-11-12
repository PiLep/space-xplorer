<?php

use App\Events\UserDeleted;
use App\Models\User;

it('creates UserDeleted event with user', function () {
    $user = User::factory()->create();

    $event = new UserDeleted($user);

    expect($event->user)->toBe($user)
        ->and($event->shouldBroadcast)->toBeFalse();
});

it('serializes user model correctly', function () {
    $user = User::factory()->create();

    $event = new UserDeleted($user);

    // Event should be serializable
    $serialized = serialize($event);
    $unserialized = unserialize($serialized);

    expect($unserialized->user->id)->toBe($user->id);
});

