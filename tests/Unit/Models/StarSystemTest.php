<?php

use App\Models\Planet;
use App\Models\StarSystem;

it('has planets relationship', function () {
    $system = StarSystem::factory()->create();
    $planet1 = Planet::factory()->create(['star_system_id' => $system->id]);
    $planet2 = Planet::factory()->create(['star_system_id' => $system->id]);
    $planet3 = Planet::factory()->create(['star_system_id' => $system->id]);

    expect($system->planets)->toHaveCount(3)
        ->and($system->planets->pluck('id'))->toContain($planet1->id, $planet2->id, $planet3->id);
});

it('returns empty collection when no planets', function () {
    $system = StarSystem::factory()->create();

    expect($system->planets)->toHaveCount(0);
});

it('can access planets through relationship', function () {
    $system = StarSystem::factory()->create([
        'name' => 'Alpha Centauri',
    ]);
    $planet = Planet::factory()->create([
        'star_system_id' => $system->id,
        'name' => 'Proxima Centauri b',
    ]);

    $loadedSystem = StarSystem::with('planets')->find($system->id);

    expect($loadedSystem->planets)->toHaveCount(1)
        ->and($loadedSystem->planets->first()->name)->toBe('Proxima Centauri b');
});

it('calculates distance to another star system', function () {
    $system1 = StarSystem::factory()->create([
        'x' => 0.0,
        'y' => 0.0,
        'z' => 0.0,
    ]);

    $system2 = StarSystem::factory()->create([
        'x' => 100.0,
        'y' => 0.0,
        'z' => 0.0,
    ]);

    $distance = $system1->distanceTo($system2);

    expect($distance)->toBe(100.0);
});

it('calculates distance for 3D coordinates', function () {
    $system1 = StarSystem::factory()->create([
        'x' => 0.0,
        'y' => 0.0,
        'z' => 0.0,
    ]);

    $system2 = StarSystem::factory()->create([
        'x' => 50.0,
        'y' => 50.0,
        'z' => 50.0,
    ]);

    $distance = $system1->distanceTo($system2);

    // Distance = sqrt(50² + 50² + 50²) = sqrt(7500) ≈ 86.60
    expect($distance)->toBeGreaterThan(86.0)
        ->and($distance)->toBeLessThan(87.0);
});

it('calculates zero distance for same system', function () {
    $system = StarSystem::factory()->create([
        'x' => 100.0,
        'y' => 200.0,
        'z' => 300.0,
    ]);

    $distance = $system->distanceTo($system);

    expect($distance)->toBe(0.0);
});

it('calculates distance symmetrically', function () {
    $system1 = StarSystem::factory()->create([
        'x' => 10.0,
        'y' => 20.0,
        'z' => 30.0,
    ]);

    $system2 = StarSystem::factory()->create([
        'x' => 50.0,
        'y' => 60.0,
        'z' => 70.0,
    ]);

    $distance1 = $system1->distanceTo($system2);
    $distance2 = $system2->distanceTo($system1);

    expect($distance1)->toBe($distance2);
});

it('finds nearby systems within radius', function () {
    $x = 100.0;
    $y = 200.0;
    $z = 300.0;
    $radius = 200.0;

    // Create systems within radius
    $system1 = StarSystem::factory()->create([
        'x' => $x + 50,
        'y' => $y + 50,
        'z' => $z + 50,
    ]);

    $system2 = StarSystem::factory()->create([
        'x' => $x - 100,
        'y' => $y - 100,
        'z' => $z - 100,
    ]);

    // Create system outside radius
    StarSystem::factory()->create([
        'x' => $x + 300,
        'y' => $y + 300,
        'z' => $z + 300,
    ]);

    $nearby = StarSystem::nearby($x, $y, $z, $radius);

    expect($nearby)->toHaveCount(2)
        ->and($nearby->pluck('id'))->toContain($system1->id, $system2->id);
});

it('finds systems exactly at radius boundary', function () {
    $x = 100.0;
    $y = 200.0;
    $z = 300.0;
    $radius = 100.0;

    // System exactly at radius (distance = 100)
    $system = StarSystem::factory()->create([
        'x' => $x + 100,
        'y' => $y,
        'z' => $z,
    ]);

    $nearby = StarSystem::nearby($x, $y, $z, $radius);

    expect($nearby)->toHaveCount(1)
        ->and($nearby->first()->id)->toBe($system->id);
});

it('excludes systems just outside radius', function () {
    $x = 100.0;
    $y = 200.0;
    $z = 300.0;
    $radius = 100.0;

    // System just outside radius (distance > 100)
    StarSystem::factory()->create([
        'x' => $x + 101,
        'y' => $y,
        'z' => $z,
    ]);

    $nearby = StarSystem::nearby($x, $y, $z, $radius);

    expect($nearby)->toHaveCount(0);
});

it('returns empty collection when no systems nearby', function () {
    $x = 100.0;
    $y = 200.0;
    $z = 300.0;
    $radius = 50.0;

    // Create systems far away
    StarSystem::factory()->create([
        'x' => $x + 1000,
        'y' => $y + 1000,
        'z' => $z + 1000,
    ]);

    $nearby = StarSystem::nearby($x, $y, $z, $radius);

    expect($nearby)->toHaveCount(0);
});

it('filters systems correctly in 3D space', function () {
    $x = 0.0;
    $y = 0.0;
    $z = 0.0;
    $radius = 100.0;

    // System within radius (distance ≈ 86.60)
    $system1 = StarSystem::factory()->create([
        'x' => 50.0,
        'y' => 50.0,
        'z' => 50.0,
    ]);

    // System outside radius (distance ≈ 173.20)
    StarSystem::factory()->create([
        'x' => 100.0,
        'y' => 100.0,
        'z' => 100.0,
    ]);

    $nearby = StarSystem::nearby($x, $y, $z, $radius);

    expect($nearby)->toHaveCount(1)
        ->and($nearby->first()->id)->toBe($system1->id);
});

it('has correct table name', function () {
    $system = new StarSystem;

    expect($system->getTable())->toBe('star_systems');
});

it('uses ULIDs for primary key', function () {
    $system = StarSystem::factory()->create();

    expect($system->id)->not->toBeNull()
        ->and(strlen($system->id))->toBe(26) // ULID length
        ->and($system->getKeyType())->toBe('string');
});

it('casts coordinates to decimal', function () {
    $system = StarSystem::factory()->create([
        'x' => 123.456789,
        'y' => 234.567890,
        'z' => 345.678901,
    ]);

    // Laravel returns decimal casts as strings
    expect($system->x)->toBe('123.46') // Rounded to 2 decimals
        ->and($system->y)->toBe('234.57')
        ->and($system->z)->toBe('345.68');
});

it('casts discovered to boolean', function () {
    $system1 = StarSystem::factory()->create(['discovered' => true]);
    $system2 = StarSystem::factory()->create(['discovered' => false]);

    expect($system1->discovered)->toBeTrue()
        ->and($system2->discovered)->toBeFalse()
        ->and(is_bool($system1->discovered))->toBeTrue()
        ->and(is_bool($system2->discovered))->toBeTrue();
});

it('casts planet_count to integer', function () {
    $system = StarSystem::factory()->create(['planet_count' => 5]);

    expect($system->planet_count)->toBe(5)
        ->and(is_int($system->planet_count))->toBeTrue();
});

it('can be created with all fillable attributes', function () {
    $system = StarSystem::create([
        'name' => 'Alpha Centauri',
        'x' => 100.50,
        'y' => 200.75,
        'z' => 300.25,
        'star_type' => 'yellow_dwarf',
        'planet_count' => 3,
        'discovered' => true,
    ]);

    // Laravel returns decimal casts as strings
    expect($system->name)->toBe('Alpha Centauri')
        ->and($system->x)->toBe('100.50')
        ->and($system->y)->toBe('200.75')
        ->and($system->z)->toBe('300.25')
        ->and($system->star_type)->toBe('yellow_dwarf')
        ->and($system->planet_count)->toBe(3)
        ->and($system->discovered)->toBeTrue();
});

it('can be updated', function () {
    $system = StarSystem::factory()->create([
        'name' => 'Alpha Centauri',
        'discovered' => false,
        'planet_count' => 0,
    ]);

    $system->update([
        'discovered' => true,
        'planet_count' => 5,
        'name' => 'Beta Centauri',
    ]);

    expect($system->fresh()->discovered)->toBeTrue()
        ->and($system->fresh()->planet_count)->toBe(5)
        ->and($system->fresh()->name)->toBe('Beta Centauri');
});

it('can have multiple planets', function () {
    $system = StarSystem::factory()->create();

    $planets = Planet::factory()->count(5)->create([
        'star_system_id' => $system->id,
    ]);

    expect($system->planets)->toHaveCount(5)
        ->and($system->planets->pluck('id')->toArray())->toBe($planets->pluck('id')->toArray());
});

it('can access planets with eager loading', function () {
    $system = StarSystem::factory()->create();
    Planet::factory()->count(3)->create(['star_system_id' => $system->id]);

    $loadedSystem = StarSystem::with('planets')->find($system->id);

    expect($loadedSystem->planets)->toHaveCount(3)
        ->and($loadedSystem->relationLoaded('planets'))->toBeTrue();
});

