<?php

use App\Events\PlanetCreated;
use App\Listeners\CreateWikiEntryOnPlanetCreated;
use App\Models\Planet;
use App\Models\User;
use App\Models\WikiEntry;
use App\Services\WikiService;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake([PlanetCreated::class]);
    $this->wikiService = \Mockery::mock(WikiService::class);
    $this->listener = new CreateWikiEntryOnPlanetCreated($this->wikiService);
});

it('creates wiki entry when planet is created', function () {
    $planet = Planet::factory()->create();
    $user = User::factory()->create([
        'home_planet_id' => $planet->id,
    ]);
    $planet->users()->attach($user->id);

    $entry = WikiEntry::factory()->make([
        'planet_id' => $planet->id,
        'discovered_by_user_id' => $user->id,
    ]);

    $this->wikiService->shouldReceive('createEntryForPlanet')
        ->once()
        ->with(\Mockery::on(fn ($p) => $p->id === $planet->id), $user)
        ->andReturn($entry);

    $event = new PlanetCreated($planet);
    $this->listener->handle($event);
});

it('creates wiki entry without discoverer if planet has no users', function () {
    $planet = Planet::factory()->create();

    $entry = WikiEntry::factory()->make([
        'planet_id' => $planet->id,
        'discovered_by_user_id' => null,
    ]);

    $this->wikiService->shouldReceive('createEntryForPlanet')
        ->once()
        ->with(\Mockery::on(fn ($p) => $p->id === $planet->id), null)
        ->andReturn($entry);

    $event = new PlanetCreated($planet);
    $this->listener->handle($event);
});

afterEach(function () {
    \Mockery::close();
});

