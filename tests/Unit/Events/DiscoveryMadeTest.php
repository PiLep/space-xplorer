<?php

use App\Events\DiscoveryMade;
use App\Models\User;

it('can be instantiated with user and discovery type', function () {
    $user = User::factory()->create();
    $event = new DiscoveryMade($user, 'planet');

    expect($event->user)->toBe($user)
        ->and($event->discoveryType)->toBe('planet')
        ->and($event->discoveryData)->toBe([])
        ->and($event->shouldBroadcast)->toBeFalse();
});

it('can be instantiated with discovery data', function () {
    $user = User::factory()->create();
    $data = ['planet_id' => '123', 'name' => 'Test Planet'];
    $event = new DiscoveryMade($user, 'planet', $data);

    expect($event->user)->toBe($user)
        ->and($event->discoveryType)->toBe('planet')
        ->and($event->discoveryData)->toBe($data);
});
