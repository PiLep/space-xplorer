<?php

use App\Models\Planet;
use App\Models\StarSystem;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    // Clean up before each test
    StarSystem::query()->delete();
    Planet::query()->delete();
    User::query()->delete();
});

it('generates undiscovered star systems successfully with default count', function () {
    $initialCount = StarSystem::count();

    $exitCode = Artisan::call('universe:generate-undiscovered', [
        '--count' => 5,
    ]);

    $output = Artisan::output();
    expect($exitCode)->toBe(0)
        ->and($output)->toContain('Generating 5 undiscovered star systems')
        ->and($output)->toContain('Generation complete!')
        ->and($output)->toContain('Generated: 5');

    $newCount = StarSystem::count();
    expect($newCount)->toBe($initialCount + 5);
});

it('generates systems marked as undiscovered', function () {
    Artisan::call('universe:generate-undiscovered', [
        '--count' => 3,
    ]);

    $generatedSystems = StarSystem::orderBy('created_at', 'desc')->take(3)->get();
    foreach ($generatedSystems as $system) {
        expect($system->discovered)->toBeFalse();
    }
});

it('generates systems with valid coordinates', function () {
    Artisan::call('universe:generate-undiscovered', [
        '--count' => 3,
    ]);

    $generatedSystems = StarSystem::orderBy('created_at', 'desc')->take(3)->get();
    foreach ($generatedSystems as $system) {
        expect($system->x)->not->toBeNull()
            ->and($system->y)->not->toBeNull()
            ->and($system->z)->not->toBeNull()
            ->and(is_numeric($system->x))->toBeTrue()
            ->and(is_numeric($system->y))->toBeTrue()
            ->and(is_numeric($system->z))->toBeTrue();
    }
});

it('generates systems with planets', function () {
    Artisan::call('universe:generate-undiscovered', [
        '--count' => 3,
    ]);

    $generatedSystems = StarSystem::orderBy('created_at', 'desc')->take(3)->get();
    foreach ($generatedSystems as $system) {
        expect($system->planets)->not->toBeEmpty()
            ->and($system->planet_count)->toBeGreaterThan(0)
            ->and($system->planets->count())->toBe($system->planet_count);
    }
});

it('respects minimum distance between systems', function () {
    $minDistance = 30.0;

    Artisan::call('universe:generate-undiscovered', [
        '--count' => 5,
        '--min-distance' => $minDistance,
    ]);

    $generatedSystems = StarSystem::orderBy('created_at', 'desc')->take(5)->get();

    // Check distances between all pairs
    for ($i = 0; $i < $generatedSystems->count(); $i++) {
        for ($j = $i + 1; $j < $generatedSystems->count(); $j++) {
            $system1 = $generatedSystems[$i];
            $system2 = $generatedSystems[$j];

            $distance = $system1->distanceTo($system2);
            expect($distance)->toBeGreaterThanOrEqual($minDistance);
        }
    }
});

it('excludes home star systems from generation', function () {
    // Create a user with a home planet
    $user = User::factory()->create();
    $homePlanet = Planet::factory()->create();
    $homeSystem = StarSystem::factory()->create();
    $homePlanet->update(['star_system_id' => $homeSystem->id]);
    $user->update(['home_planet_id' => $homePlanet->id]);

    $homeSystemId = $homeSystem->id;

    Artisan::call('universe:generate-undiscovered', [
        '--count' => 10,
    ]);

    $output = Artisan::output();
    expect($output)->toContain('Found 1 home star systems to exclude');

    // Verify the home system still exists and wasn't regenerated
    $homeSystem->refresh();
    expect($homeSystem->id)->toBe($homeSystemId);
});

it('uses expand-range option to increase coordinate range but respects max distance', function () {
    // Create some existing systems
    StarSystem::factory()->count(3)->create([
        'x' => 100.0,
        'y' => 100.0,
        'z' => 100.0,
    ]);

    Artisan::call('universe:generate-undiscovered', [
        '--count' => 2,
        '--expand-range' => true,
    ]);

    $output = Artisan::output();
    expect($output)->toContain('Using coordinate range:');

    // The range should be larger with expand-range, but systems should still respect max distance
    $generatedSystems = StarSystem::orderBy('created_at', 'desc')->take(2)->get();
    foreach ($generatedSystems as $system) {
        $distanceFromOrigin = sqrt(
            $system->x ** 2 + $system->y ** 2 + $system->z ** 2
        );
        // Even with expand-range, systems should not exceed max distance from origin
        expect($distanceFromOrigin)->toBeLessThanOrEqual(400.0)
            ->and($distanceFromOrigin)->toBeGreaterThanOrEqual(50.0);
    }
});

it('calculates coordinate range based on existing systems', function () {
    // Create systems at specific coordinates
    StarSystem::factory()->create([
        'x' => 200.0,
        'y' => 150.0,
        'z' => 100.0,
    ]);

    Artisan::call('universe:generate-undiscovered', [
        '--count' => 2,
    ]);

    $output = Artisan::output();
    expect($output)->toContain('Using coordinate range:');

    // The range should be calculated based on existing systems
    $generatedSystems = StarSystem::orderBy('created_at', 'desc')->take(2)->get();
    foreach ($generatedSystems as $system) {
        // Systems should be generated within a reasonable range
        expect(abs($system->x))->toBeLessThan(500.0)
            ->and(abs($system->y))->toBeLessThan(500.0)
            ->and(abs($system->z))->toBeLessThan(500.0);
    }
});

it('handles count less than 1', function () {
    $exitCode = Artisan::call('universe:generate-undiscovered', [
        '--count' => 0,
    ]);

    expect($exitCode)->toBe(1)
        ->and(Artisan::output())->toContain('Count must be at least 1');
});

it('displays summary with total undiscovered systems', function () {
    // Create some discovered systems
    StarSystem::factory()->count(3)->create(['discovered' => true]);
    StarSystem::factory()->count(2)->create(['discovered' => false]);

    Artisan::call('universe:generate-undiscovered', [
        '--count' => 5,
    ]);

    $output = Artisan::output();
    expect($output)->toContain('Total undiscovered systems:')
        ->and($output)->toContain('Generation complete!');
});

it('handles failures when systems cannot be generated', function () {
    // Create many systems very close together to make generation difficult
    for ($i = 0; $i < 20; $i++) {
        StarSystem::factory()->create([
            'x' => 100.0 + ($i * 0.1),
            'y' => 100.0 + ($i * 0.1),
            'z' => 100.0 + ($i * 0.1),
        ]);
    }

    $exitCode = Artisan::call('universe:generate-undiscovered', [
        '--count' => 10,
        '--min-distance' => 50.0, // Large minimum distance
        '--max-attempts' => 10, // Low attempts to trigger failure
    ]);

    $output = Artisan::output();
    // Should still complete but may have failures
    expect($exitCode)->toBe(0)
        ->and($output)->toContain('Generation complete!');
});

it('generates systems at minimum distance from origin', function () {
    Artisan::call('universe:generate-undiscovered', [
        '--count' => 5,
    ]);

    $generatedSystems = StarSystem::orderBy('created_at', 'desc')->take(5)->get();
    $minDistanceFromOrigin = 50.0;

    foreach ($generatedSystems as $system) {
        $distanceFromOrigin = sqrt(
            $system->x ** 2 + $system->y ** 2 + $system->z ** 2
        );
        expect($distanceFromOrigin)->toBeGreaterThanOrEqual($minDistanceFromOrigin);
    }
});

it('generates systems within maximum distance from origin', function () {
    Artisan::call('universe:generate-undiscovered', [
        '--count' => 10,
    ]);

    $generatedSystems = StarSystem::orderBy('created_at', 'desc')->take(10)->get();
    $maxDistanceFromOrigin = 400.0;

    foreach ($generatedSystems as $system) {
        $distanceFromOrigin = sqrt(
            $system->x ** 2 + $system->y ** 2 + $system->z ** 2
        );
        expect($distanceFromOrigin)->toBeLessThanOrEqual($maxDistanceFromOrigin)
            ->and($distanceFromOrigin)->toBeGreaterThanOrEqual(50.0);
    }
});

it('respects maximum distance even with expand-range option', function () {
    Artisan::call('universe:generate-undiscovered', [
        '--count' => 5,
        '--expand-range' => true,
    ]);

    $generatedSystems = StarSystem::orderBy('created_at', 'desc')->take(5)->get();
    $maxDistanceFromOrigin = 400.0;

    foreach ($generatedSystems as $system) {
        $distanceFromOrigin = sqrt(
            $system->x ** 2 + $system->y ** 2 + $system->z ** 2
        );
        // Even with expand-range, systems should not exceed max distance
        expect($distanceFromOrigin)->toBeLessThanOrEqual($maxDistanceFromOrigin);
    }
});

it('updates existing systems collection during generation', function () {
    // This tests that the command properly tracks generated systems
    // to avoid generating systems too close to each other
    Artisan::call('universe:generate-undiscovered', [
        '--count' => 5,
        '--min-distance' => 30.0,
    ]);

    $allSystems = StarSystem::all();
    expect($allSystems->count())->toBeGreaterThanOrEqual(5);

    // Verify all systems respect minimum distance
    for ($i = 0; $i < $allSystems->count(); $i++) {
        for ($j = $i + 1; $j < $allSystems->count(); $j++) {
            $distance = $allSystems[$i]->distanceTo($allSystems[$j]);
            expect($distance)->toBeGreaterThanOrEqual(30.0);
        }
    }
});

it('displays progress bar during generation', function () {
    Artisan::call('universe:generate-undiscovered', [
        '--count' => 3,
    ]);

    $output = Artisan::output();
    // Progress bar should be displayed
    expect($output)->toContain('Generation complete!');
});

it('handles custom min-distance option', function () {
    $customMinDistance = 50.0;

    Artisan::call('universe:generate-undiscovered', [
        '--count' => 3,
        '--min-distance' => $customMinDistance,
    ]);

    $generatedSystems = StarSystem::orderBy('created_at', 'desc')->take(3)->get();

    for ($i = 0; $i < $generatedSystems->count(); $i++) {
        for ($j = $i + 1; $j < $generatedSystems->count(); $j++) {
            $distance = $generatedSystems[$i]->distanceTo($generatedSystems[$j]);
            expect($distance)->toBeGreaterThanOrEqual($customMinDistance);
        }
    }
});

it('handles custom max-attempts option', function () {
    $exitCode = Artisan::call('universe:generate-undiscovered', [
        '--count' => 2,
        '--max-attempts' => 100,
    ]);

    expect($exitCode)->toBe(0);
    $output = Artisan::output();
    expect($output)->toContain('Generation complete!');
});

it('generates systems with unique names', function () {
    Artisan::call('universe:generate-undiscovered', [
        '--count' => 5,
    ]);

    $generatedSystems = StarSystem::orderBy('created_at', 'desc')->take(5)->get();
    $names = $generatedSystems->pluck('name')->unique();

    expect($names->count())->toBe(5); // All names should be unique
});

it('generates systems with valid star types', function () {
    $validStarTypes = ['yellow_dwarf', 'red_dwarf', 'orange_dwarf', 'red_giant', 'blue_giant', 'white_dwarf'];

    Artisan::call('universe:generate-undiscovered', [
        '--count' => 5,
    ]);

    $generatedSystems = StarSystem::orderBy('created_at', 'desc')->take(5)->get();
    foreach ($generatedSystems as $system) {
        expect($system->star_type)->toBeIn($validStarTypes);
    }
});

