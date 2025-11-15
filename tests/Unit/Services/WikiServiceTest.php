<?php

use App\Models\Planet;
use App\Models\User;
use App\Models\WikiEntry;
use App\Services\AIDescriptionService;
use App\Services\WikiService;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    Event::fake(); // Fake events to prevent actual listener execution
    $this->aiDescriptionService = \Mockery::mock(AIDescriptionService::class);
    $this->service = new WikiService($this->aiDescriptionService);
    $this->planet = Planet::factory()->create();
    $this->planet->properties()->create([
        'type' => 'tellurique',
        'size' => 'moyenne',
        'temperature' => 'temperee',
        'atmosphere' => 'breathable',
        'terrain' => 'rocky',
        'resources' => 'moderate',
    ]);
    $this->user = User::factory()->create();
});

it('creates a wiki entry for a planet', function () {
    $this->aiDescriptionService->shouldReceive('generatePlanetDescription')
        ->once()
        ->with($this->planet)
        ->andReturn('A beautiful planet with diverse ecosystems.');

    $entry = $this->service->createEntryForPlanet($this->planet, $this->user);

    expect($entry)->toBeInstanceOf(WikiEntry::class)
        ->and($entry->planet_id)->toBe($this->planet->id)
        ->and($entry->discovered_by_user_id)->toBe($this->user->id)
        ->and($entry->fallback_name)->not->toBeNull()
        ->and($entry->description)->toBe('A beautiful planet with diverse ecosystems.')
        ->and($entry->is_named)->toBeFalse()
        ->and($entry->is_public)->toBeTrue();
});

it('does not create duplicate entries for the same planet', function () {
    $this->aiDescriptionService->shouldReceive('generatePlanetDescription')
        ->once()
        ->andReturn('Description');

    $entry1 = $this->service->createEntryForPlanet($this->planet, $this->user);
    $entry2 = $this->service->createEntryForPlanet($this->planet, $this->user);

    expect($entry1->id)->toBe($entry2->id);
});

it('generates a fallback name based on planet type', function () {
    $fallbackName = $this->service->generateFallbackName($this->planet);

    expect($fallbackName)->toBeString()
        ->and($fallbackName)->toContain('Planète')
        ->and($fallbackName)->toContain('Tellurique');
});

it('validates a valid planet name', function () {
    expect(fn () => $this->service->validateName('Alpha Centauri'))
        ->not->toThrow(ValidationException::class);
});

it('throws validation exception for name that is too short', function () {
    expect(fn () => $this->service->validateName('AB'))
        ->toThrow(ValidationException::class);
});

it('throws validation exception for name that is too long', function () {
    $longName = str_repeat('A', 51);
    expect(fn () => $this->service->validateName($longName))
        ->toThrow(ValidationException::class);
});

it('throws validation exception for name with invalid characters', function () {
    expect(fn () => $this->service->validateName('Planet@123'))
        ->toThrow(ValidationException::class);
});

it('throws validation exception for duplicate name', function () {
    WikiEntry::factory()->create([
        'name' => 'Alpha Centauri',
        'planet_id' => Planet::factory()->create()->id,
    ]);

    expect(fn () => $this->service->validateName('Alpha Centauri'))
        ->toThrow(ValidationException::class);
});

it('names a planet successfully', function () {
    $this->aiDescriptionService->shouldReceive('generatePlanetDescription')
        ->once()
        ->andReturn('Description');

    $entry = $this->service->createEntryForPlanet($this->planet, $this->user);
    $namedEntry = $this->service->namePlanet($entry, $this->user, 'Alpha Centauri');

    expect($namedEntry->name)->toBe('Alpha Centauri')
        ->and($namedEntry->is_named)->toBeTrue();
});

it('checks if user can name planet', function () {
    $this->aiDescriptionService->shouldReceive('generatePlanetDescription')
        ->once()
        ->andReturn('Description');

    $entry = $this->service->createEntryForPlanet($this->planet, $this->user);
    $otherUser = User::factory()->create();

    expect($this->service->canUserNamePlanet($entry, $this->user))->toBeTrue()
        ->and($this->service->canUserNamePlanet($entry, $otherUser))->toBeFalse();
});

it('searches entries by query', function () {
    $entry1 = WikiEntry::factory()->create([
        'name' => 'Alpha Centauri',
        'fallback_name' => 'Planète Tellurique #1234',
        'planet_id' => $this->planet->id,
    ]);
    $entry2 = WikiEntry::factory()->create([
        'name' => null,
        'fallback_name' => 'Planète Gazeuse #5678',
        'planet_id' => Planet::factory()->create()->id,
    ]);

    $results = $this->service->searchEntries('Alpha', 10);

    expect($results)->toHaveCount(1)
        ->and($results->first()->id)->toBe($entry1->id);
});

afterEach(function () {
    \Mockery::close();
});

