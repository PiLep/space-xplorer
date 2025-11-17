<?php

use App\Models\Planet;
use App\Models\StarSystem;
use App\Models\User;
use App\Services\ExplorationService;
use App\Services\StarSystemGeneratorService;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake(); // Fake events to prevent actual listener execution
    $this->user = User::factory()->create();
    $this->starSystemGenerator = \Mockery::mock(StarSystemGeneratorService::class);
    $this->service = new ExplorationService($this->starSystemGenerator);
});

afterEach(function () {
    \Mockery::close();
});

describe('exploreNearbySystems', function () {
    it('returns existing systems within radius', function () {
        $x = 100.0;
        $y = 200.0;
        $z = 300.0;
        $radius = 200.0;
        $maxNearbySystems = config('star-systems.generation.max_nearby_systems');

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

        // Mock generation for systems that need to be created
        $systemsToGenerate = max(0, $maxNearbySystems - 2);

        if ($systemsToGenerate > 0) {
            $counter = 0;
            $this->starSystemGenerator
                ->shouldReceive('generateNearbySystem')
                ->with($x, $y, $z)
                ->times($systemsToGenerate)
                ->andReturnUsing(function () use ($x, $y, $z, &$counter) {
                    // Create system with coordinates in radius but offset to avoid detection before generation
                    $offset = ($counter++ + 10) * 15;

                    return StarSystem::factory()->create([
                        'x' => $x + $offset,
                        'y' => $y + $offset,
                        'z' => $z + $offset,
                    ]);
                });
        } else {
            $this->starSystemGenerator
                ->shouldReceive('generateNearbySystem')
                ->never();
        }

        $result = $this->service->exploreNearbySystems($this->user, $x, $y, $z, $radius);

        expect($result)->toHaveCount($maxNearbySystems)
            ->and($result->pluck('id')->contains($system1->id))->toBeTrue()
            ->and($result->pluck('id')->contains($system2->id))->toBeTrue();
    });

    it('generates new systems when not enough exist', function () {
        $x = 100.0;
        $y = 200.0;
        $z = 300.0;
        $radius = 200.0;
        $maxNearbySystems = config('star-systems.generation.max_nearby_systems');

        // Create only 2 systems (less than max)
        StarSystem::factory()->create([
            'x' => $x + 50,
            'y' => $y + 50,
            'z' => $z + 50,
        ]);

        StarSystem::factory()->create([
            'x' => $x - 50,
            'y' => $y - 50,
            'z' => $z - 50,
        ]);

        // Mock generation of new systems
        $systemsToGenerate = $maxNearbySystems - 2;
        $counter = 0;
        $this->starSystemGenerator
            ->shouldReceive('generateNearbySystem')
            ->with($x, $y, $z)
            ->times($systemsToGenerate)
            ->andReturnUsing(function () use ($x, $y, $z, &$counter) {
                // Create system with coordinates in radius
                $offset = ($counter++ + 10) * 15;

                return StarSystem::factory()->create([
                    'x' => $x + $offset,
                    'y' => $y + $offset,
                    'z' => $z + $offset,
                ]);
            });

        $result = $this->service->exploreNearbySystems($this->user, $x, $y, $z, $radius);

        expect($result)->toHaveCount($maxNearbySystems);
    });

    it('does not generate systems when max is reached', function () {
        $x = 100.0;
        $y = 200.0;
        $z = 300.0;
        $radius = 200.0;
        $maxNearbySystems = config('star-systems.generation.max_nearby_systems');

        // Create exactly max systems
        for ($i = 0; $i < $maxNearbySystems; $i++) {
            StarSystem::factory()->create([
                'x' => $x + ($i * 10),
                'y' => $y + ($i * 10),
                'z' => $z + ($i * 10),
            ]);
        }

        // Should not call generator
        $this->starSystemGenerator
            ->shouldReceive('generateNearbySystem')
            ->never();

        $result = $this->service->exploreNearbySystems($this->user, $x, $y, $z, $radius);

        expect($result)->toHaveCount($maxNearbySystems);
    });

    it('uses default radius when not specified', function () {
        $x = 100.0;
        $y = 200.0;
        $z = 300.0;
        $defaultRadius = config('star-systems.generation.exploration_radius');
        $maxNearbySystems = config('star-systems.generation.max_nearby_systems');

        // Create system within default radius
        $system = StarSystem::factory()->create([
            'x' => $x + ($defaultRadius / 2),
            'y' => $y + ($defaultRadius / 2),
            'z' => $z + ($defaultRadius / 2),
        ]);

        // Create system outside default radius
        StarSystem::factory()->create([
            'x' => $x + ($defaultRadius + 50),
            'y' => $y + ($defaultRadius + 50),
            'z' => $z + ($defaultRadius + 50),
        ]);

        // Mock generation for systems that need to be created
        $systemsToGenerate = max(0, $maxNearbySystems - 1);

        if ($systemsToGenerate > 0) {
            $counter = 0;
            $this->starSystemGenerator
                ->shouldReceive('generateNearbySystem')
                ->with($x, $y, $z)
                ->times($systemsToGenerate)
                ->andReturnUsing(function () use ($x, $y, $z, &$counter) {
                    // Create system with coordinates in radius
                    return StarSystem::factory()->create([
                        'x' => $x + (($counter++ + 10) * 15),
                        'y' => $y + (($counter + 10) * 15),
                        'z' => $z + (($counter + 10) * 15),
                    ]);
                });
        } else {
            $this->starSystemGenerator
                ->shouldReceive('generateNearbySystem')
                ->never();
        }

        $result = $this->service->exploreNearbySystems($this->user, $x, $y, $z);

        expect($result)->toHaveCount($maxNearbySystems)
            ->and($result->pluck('id')->contains($system->id))->toBeTrue();
    });

    it('handles empty result when no systems exist', function () {
        $x = 100.0;
        $y = 200.0;
        $z = 300.0;
        $radius = 200.0;
        $maxNearbySystems = config('star-systems.generation.max_nearby_systems');

        // Mock generation of all systems
        $counter = 0;
        $this->starSystemGenerator
            ->shouldReceive('generateNearbySystem')
            ->with($x, $y, $z)
            ->times($maxNearbySystems)
            ->andReturnUsing(function () use ($x, $y, $z, &$counter) {
                // Create system with coordinates in radius
                $offset = ($counter++ + 10) * 15;

                return StarSystem::factory()->create([
                    'x' => $x + $offset,
                    'y' => $y + $offset,
                    'z' => $z + $offset,
                ]);
            });

        $result = $this->service->exploreNearbySystems($this->user, $x, $y, $z, $radius);

        expect($result)->toHaveCount($maxNearbySystems);
    });
});

describe('calculateTravelTime', function () {
    it('calculates travel time between two planets', function () {
        $planet1 = Planet::factory()->create([
            'x' => 0.0,
            'y' => 0.0,
            'z' => 0.0,
        ]);

        $planet2 = Planet::factory()->create([
            'x' => 100.0,
            'y' => 0.0,
            'z' => 0.0,
        ]);

        $travelTime = $this->service->calculateTravelTime($planet1, $planet2);

        // Distance is 100, speed is 1.0 by default, so travel time should be 100
        expect($travelTime)->toBe(100.0);
    });

    it('calculates travel time for 3D coordinates', function () {
        $planet1 = Planet::factory()->create([
            'x' => 0.0,
            'y' => 0.0,
            'z' => 0.0,
        ]);

        $planet2 = Planet::factory()->create([
            'x' => 50.0,
            'y' => 50.0,
            'z' => 50.0,
        ]);

        $travelTime = $this->service->calculateTravelTime($planet1, $planet2);

        // Distance = sqrt(50² + 50² + 50²) = sqrt(7500) ≈ 86.60
        // Travel time = 86.60 / 1.0 ≈ 86.60
        expect($travelTime)->toBeGreaterThan(86.0)
            ->and($travelTime)->toBeLessThan(87.0);
    });

    it('calculates zero travel time for same planet', function () {
        $planet = Planet::factory()->create([
            'x' => 100.0,
            'y' => 200.0,
            'z' => 300.0,
        ]);

        $travelTime = $this->service->calculateTravelTime($planet, $planet);

        expect($travelTime)->toBe(0.0);
    });

    it('delegates to planet travelTimeTo method', function () {
        $planet1 = Planet::factory()->create([
            'x' => 0.0,
            'y' => 0.0,
            'z' => 0.0,
        ]);

        $planet2 = Planet::factory()->create([
            'x' => 200.0,
            'y' => 0.0,
            'z' => 0.0,
        ]);

        $travelTime = $this->service->calculateTravelTime($planet1, $planet2);

        // Verify it matches the planet's method
        $expectedTime = $planet1->travelTimeTo($planet2);
        expect($travelTime)->toBe($expectedTime);
    });
});

