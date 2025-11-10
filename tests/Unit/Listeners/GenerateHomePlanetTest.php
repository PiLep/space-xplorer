<?php

use App\Events\PlanetCreated;
use App\Events\UserRegistered;
use App\Listeners\GenerateHomePlanet;
use App\Models\Planet;
use App\Models\User;
use App\Services\PlanetGeneratorService;
use Illuminate\Support\Facades\Event;

it('generates and assigns a planet to the user', function () {
    Event::fake([PlanetCreated::class]); // Fake PlanetCreated to prevent image generation

    $user = User::factory()->create([
        'home_planet_id' => null,
    ]);

    $event = new UserRegistered($user);
    $listener = new GenerateHomePlanet(new PlanetGeneratorService);

    $listener->handle($event);

    // Refresh user to get updated data
    $user->refresh();

    expect($user->home_planet_id)->not->toBeNull();

    // Verify planet exists
    $planet = Planet::find($user->home_planet_id);
    expect($planet)
        ->not->toBeNull()
        ->toBeInstanceOf(Planet::class);
});

it('handles errors gracefully without blocking registration', function () {
    // Create a mock service that throws an exception
    $mockService = \Mockery::mock(PlanetGeneratorService::class);
    $mockService->shouldReceive('generate')
        ->once()
        ->andThrow(new \Exception('Test error'));

    $user = User::factory()->create([
        'home_planet_id' => null,
    ]);

    $event = new UserRegistered($user);
    $listener = new GenerateHomePlanet($mockService);

    // Should not throw exception
    $listener->handle($event);

    // User should still exist and home_planet_id should remain null
    $user->refresh();
    expect($user->home_planet_id)->toBeNull();
});

it('completes successfully without errors', function () {
    Event::fake([PlanetCreated::class]); // Fake PlanetCreated to prevent image generation

    $user = User::factory()->create([
        'home_planet_id' => null,
    ]);

    $event = new UserRegistered($user);
    $listener = new GenerateHomePlanet(new PlanetGeneratorService);

    // Should not throw exception
    $listener->handle($event);

    // Verify planet was assigned
    $user->refresh();
    expect($user->home_planet_id)->not->toBeNull();
});

it('assigns a planet with all required characteristics', function () {
    Event::fake([PlanetCreated::class]); // Fake PlanetCreated to prevent image generation

    $user = User::factory()->create([
        'home_planet_id' => null,
    ]);

    $event = new UserRegistered($user);
    $listener = new GenerateHomePlanet(new PlanetGeneratorService);

    $listener->handle($event);

    $user->refresh();
    $planet = Planet::find($user->home_planet_id);

    expect($planet->name)->not->toBeNull()
        ->and($planet->type)->not->toBeNull()
        ->and($planet->size)->not->toBeNull()
        ->and($planet->temperature)->not->toBeNull()
        ->and($planet->atmosphere)->not->toBeNull()
        ->and($planet->terrain)->not->toBeNull()
        ->and($planet->resources)->not->toBeNull()
        ->and($planet->description)->not->toBeNull();
});

it('assigns different home planets to multiple users', function () {
    Event::fake([PlanetCreated::class]); // Fake PlanetCreated to prevent image generation

    $user1 = User::factory()->create(['home_planet_id' => null]);
    $user2 = User::factory()->create(['home_planet_id' => null]);

    $listener = new GenerateHomePlanet(new PlanetGeneratorService);

    $listener->handle(new UserRegistered($user1));
    $listener->handle(new UserRegistered($user2));

    $user1->refresh();
    $user2->refresh();

    expect($user1->home_planet_id)->not->toBeNull()
        ->and($user2->home_planet_id)->not->toBeNull()
        ->and($user1->home_planet_id)->not->toBe($user2->home_planet_id);
});

afterEach(function () {
    \Mockery::close();
});
