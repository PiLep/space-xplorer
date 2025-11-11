<?php

use App\Events\PlanetExplored;
use App\Models\Planet;
use App\Models\User;

it('can be instantiated with user and planet', function () {
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $event = new PlanetExplored($user, $planet);

    expect($event->user)->toBe($user)
        ->and($event->planet)->toBe($planet)
        ->and($event->shouldBroadcast)->toBeFalse();
});
