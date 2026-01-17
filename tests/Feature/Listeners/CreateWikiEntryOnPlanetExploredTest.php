<?php

use App\Events\PlanetExplored;
use App\Listeners\CreateWikiEntryOnPlanetExplored;
use App\Models\Planet;
use App\Models\User;
use App\Models\WikiEntry;
use App\Services\WikiService;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake([PlanetExplored::class]);
    $this->wikiService = \Mockery::mock(WikiService::class);
    $this->listener = new CreateWikiEntryOnPlanetExplored($this->wikiService);
});

it('creates wiki entry when planet is explored', function () {
    $planet = Planet::factory()->create();
    $user = User::factory()->create();

    $entry = WikiEntry::factory()->make([
        'planet_id' => $planet->id,
        'discovered_by_user_id' => $user->id,
    ]);

    $this->wikiService->shouldReceive('createEntryForPlanet')
        ->once()
        ->with($planet, $user)
        ->andReturn($entry);

    $event = new PlanetExplored($user, $planet);
    $this->listener->handle($event);
});

afterEach(function () {
    \Mockery::close();
});

