<?php

use App\Models\Planet;
use App\Models\PlanetProperty;

it('belongs to a planet', function () {
    $planet = Planet::factory()->create();
    // PlanetFactory creates a property automatically, so we update it
    $property = PlanetProperty::updateOrCreate(
        ['planet_id' => $planet->id],
        [
            'type' => 'terrestrial',
            'size' => 'medium',
            'temperature' => 'temperate',
            'atmosphere' => 'breathable',
            'terrain' => 'rocky',
            'resources' => 'moderate',
            'description' => 'A beautiful planet',
        ]
    );

    expect($property->planet)->not->toBeNull()
        ->and($property->planet->id)->toBe($planet->id)
        ->and($property->planet)->toBeInstanceOf(Planet::class);
});

it('can be created with all fillable attributes', function () {
    $planet = Planet::factory()->create();
    // PlanetFactory creates a property automatically, so we update it
    $property = PlanetProperty::updateOrCreate(
        ['planet_id' => $planet->id],
        [
            'type' => 'gaseous',
            'size' => 'large',
            'temperature' => 'hot',
            'atmosphere' => 'toxic',
            'terrain' => 'mixed',
            'resources' => 'abundant',
            'description' => 'A gas giant with swirling clouds',
        ]
    );

    expect($property->planet_id)->toBe($planet->id)
        ->and($property->type)->toBe('gaseous')
        ->and($property->size)->toBe('large')
        ->and($property->temperature)->toBe('hot')
        ->and($property->atmosphere)->toBe('toxic')
        ->and($property->terrain)->toBe('mixed')
        ->and($property->resources)->toBe('abundant')
        ->and($property->description)->toBe('A gas giant with swirling clouds');
});

it('can be updated', function () {
    $planet = Planet::factory()->create();
    // PlanetFactory creates a property automatically, so we update it
    $property = PlanetProperty::updateOrCreate(
        ['planet_id' => $planet->id],
        [
            'type' => 'terrestrial',
            'size' => 'small',
            'temperature' => 'cold',
            'atmosphere' => 'nonexistent',
            'terrain' => 'icy',
            'resources' => 'rare',
            'description' => 'A cold planet',
        ]
    );

    $property->update([
        'temperature' => 'temperate',
        'atmosphere' => 'breathable',
        'description' => 'A temperate planet with breathable atmosphere',
    ]);

    expect($property->fresh()->temperature)->toBe('temperate')
        ->and($property->fresh()->atmosphere)->toBe('breathable')
        ->and($property->fresh()->description)->toBe('A temperate planet with breathable atmosphere')
        ->and($property->fresh()->type)->toBe('terrestrial') // Unchanged
        ->and($property->fresh()->size)->toBe('small'); // Unchanged
});

it('has correct table name', function () {
    $property = new PlanetProperty;

    expect($property->getTable())->toBe('planet_properties');
});

it('uses ULIDs for primary key', function () {
    $planet = Planet::factory()->create();
    // PlanetFactory creates a property automatically, so we get it
    $property = $planet->properties;

    expect($property->id)->not->toBeNull()
        ->and(strlen($property->id))->toBe(26) // ULID length
        ->and($property->getKeyType())->toBe('string');
});

it('can have null description', function () {
    $planet = Planet::factory()->create();
    // PlanetFactory creates a property automatically, so we update it
    $property = PlanetProperty::updateOrCreate(
        ['planet_id' => $planet->id],
        [
            'type' => 'terrestrial',
            'size' => 'medium',
            'temperature' => 'temperate',
            'atmosphere' => 'breathable',
            'terrain' => 'rocky',
            'resources' => 'moderate',
            'description' => null,
        ]
    );

    expect($property->description)->toBeNull();
});

it('can access planet properties through relationship', function () {
    $planet = Planet::factory()->create([
        'name' => 'Test Planet',
    ]);
    // PlanetFactory creates a property automatically, so we get it
    $property = $planet->properties;

    $loadedProperty = PlanetProperty::with('planet')->find($property->id);

    expect($loadedProperty->planet)->not->toBeNull()
        ->and($loadedProperty->planet->name)->toBe('Test Planet');
});

it('can be mass assigned', function () {
    $planet = Planet::factory()->create();
    // PlanetFactory creates a property automatically, so we update it
    $data = [
        'type' => 'oceanic',
        'size' => 'medium',
        'temperature' => 'temperate',
        'atmosphere' => 'breathable',
        'terrain' => 'oceanic',
        'resources' => 'abundant',
        'description' => 'An oceanic planet',
    ];

    $property = PlanetProperty::updateOrCreate(
        ['planet_id' => $planet->id],
        $data
    );

    expect($property->type)->toBe('oceanic')
        ->and($property->terrain)->toBe('oceanic')
        ->and($property->resources)->toBe('abundant');
});

it('can have different property combinations', function () {
    $planet = Planet::factory()->create();
    // PlanetFactory creates a property automatically, so we update it
    $property1 = PlanetProperty::updateOrCreate(
        ['planet_id' => $planet->id],
        [
            'type' => 'desert',
            'size' => 'small',
            'temperature' => 'hot',
            'atmosphere' => 'nonexistent',
            'terrain' => 'desert',
            'resources' => 'rare',
            'description' => 'A desert planet',
        ]
    );

    $planet2 = Planet::factory()->create();
    // PlanetFactory creates a property automatically, so we update it
    $property2 = PlanetProperty::updateOrCreate(
        ['planet_id' => $planet2->id],
        [
            'type' => 'icy',
            'size' => 'large',
            'temperature' => 'cold',
            'atmosphere' => 'toxic',
            'terrain' => 'icy',
            'resources' => 'moderate',
            'description' => 'An icy planet',
        ]
    );

    expect($property1->type)->toBe('desert')
        ->and($property1->temperature)->toBe('hot')
        ->and($property2->type)->toBe('icy')
        ->and($property2->temperature)->toBe('cold')
        ->and($property1->planet_id)->not->toBe($property2->planet_id);
});

