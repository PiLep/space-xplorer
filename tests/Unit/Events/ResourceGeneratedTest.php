<?php

use App\Events\ResourceGenerated;
use App\Models\Resource;

it('can be instantiated with resource', function () {
    $resource = Resource::factory()->create();
    $event = new ResourceGenerated($resource);

    expect($event->resource)->toBe($resource)
        ->and($event->shouldBroadcast)->toBeFalse();
});
