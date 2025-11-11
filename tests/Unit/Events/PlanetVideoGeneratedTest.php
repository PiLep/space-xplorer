<?php

use App\Events\PlanetVideoGenerated;
use App\Models\Planet;

it('can be instantiated with planet, video path and url', function () {
    $planet = Planet::factory()->create();
    $videoPath = 'planets/video.mp4';
    $videoUrl = 'https://example.com/video.mp4';
    $event = new PlanetVideoGenerated($planet, $videoPath, $videoUrl);

    expect($event->planet)->toBe($planet)
        ->and($event->videoPath)->toBe($videoPath)
        ->and($event->videoUrl)->toBe($videoUrl)
        ->and($event->shouldBroadcast)->toBeFalse();
});
