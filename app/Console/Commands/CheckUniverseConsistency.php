<?php

namespace App\Console\Commands;

use App\Models\Planet;
use App\Models\StarSystem;
use App\Models\User;
use Illuminate\Console\Command;

class CheckUniverseConsistency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'universe:check-consistency 
                            {--fix : Attempt to fix issues automatically}
                            {--fix-orphans : Fix orphan planets by assigning them to systems}
                            {--fix-distances : Fix systems that are too close}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check universe data consistency (coordinates, relationships, counts, etc.)';

    /**
     * Issues found during checks.
     *
     * @var array<string, array>
     */
    private array $issues = [];

    /**
     * Statistics.
     *
     * @var array<string, int>
     */
    private array $stats = [
        'total_systems' => 0,
        'total_planets' => 0,
        'total_users' => 0,
        'issues_found' => 0,
        'issues_fixed' => 0,
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $fix = $this->option('fix');
        $fixOrphans = $this->option('fix-orphans');
        $fixDistances = $this->option('fix-distances');
        $verbose = $this->getOutput()->isVerbose();

        $this->info('🔍 Checking universe consistency...');
        $this->newLine();

        // Collect statistics
        $this->collectStatistics();

        // Run checks
        $this->checkStarSystems();
        $this->checkPlanets();
        $this->checkUsers();
        $this->checkCoordinates();
        $this->checkDistances();

        // Display results
        $this->displayResults($fix || $fixOrphans || $fixDistances, $verbose);

        // Attempt fixes if requested
        if ($fix || $fixOrphans) {
            $this->fixOrphanPlanets();
        }

        if ($fix || $fixDistances) {
            $this->fixSystemsTooClose();
        }

        if ($fix) {
            $this->attemptFixes();
        }

        // Summary
        $this->displaySummary();

        return count($this->issues) > 0 ? 1 : 0;
    }

    /**
     * Collect basic statistics.
     */
    private function collectStatistics(): void
    {
        $this->stats['total_systems'] = StarSystem::count();
        $this->stats['total_planets'] = Planet::count();
        $this->stats['total_users'] = User::whereNotNull('home_planet_id')->count();
    }

    /**
     * Check star systems consistency.
     */
    private function checkStarSystems(): void
    {
        $this->line('📊 Checking star systems...');

        // Check planet_count consistency
        $systems = StarSystem::withCount('planets')->get();

        foreach ($systems as $system) {
            $actualCount = $system->planets_count;
            $storedCount = $system->planet_count;

            if ($actualCount !== $storedCount) {
                $this->addIssue('star_system', 'planet_count_mismatch', [
                    'system_id' => $system->id,
                    'system_name' => $system->name,
                    'stored_count' => $storedCount,
                    'actual_count' => $actualCount,
                ]);
            }

            // Check for systems without coordinates
            if ($system->x === null || $system->y === null || $system->z === null) {
                $this->addIssue('star_system', 'missing_coordinates', [
                    'system_id' => $system->id,
                    'system_name' => $system->name,
                ]);
            }

            // Check for systems without planets
            if ($actualCount === 0) {
                $this->addIssue('star_system', 'no_planets', [
                    'system_id' => $system->id,
                    'system_name' => $system->name,
                ]);
            }

            // Check for systems with too many planets (> 7)
            if ($actualCount > 7) {
                $this->addIssue('star_system', 'too_many_planets', [
                    'system_id' => $system->id,
                    'system_name' => $system->name,
                    'planet_count' => $actualCount,
                ]);
            }
        }
    }

    /**
     * Check planets consistency.
     */
    private function checkPlanets(): void
    {
        $this->line('🪐 Checking planets...');

        $planets = Planet::with('starSystem')->get();

        foreach ($planets as $planet) {
            // Check for orphan planets (no star system)
            if ($planet->star_system_id === null) {
                $this->addIssue('planet', 'orphan_planet', [
                    'planet_id' => $planet->id,
                    'planet_name' => $planet->name,
                ]);

                continue;
            }

            // Check if star system exists
            if (! $planet->starSystem) {
                $this->addIssue('planet', 'invalid_star_system', [
                    'planet_id' => $planet->id,
                    'planet_name' => $planet->name,
                    'star_system_id' => $planet->star_system_id,
                ]);

                continue;
            }

            // Check for planets without coordinates
            if ($planet->x === null || $planet->y === null || $planet->z === null) {
                $this->addIssue('planet', 'missing_coordinates', [
                    'planet_id' => $planet->id,
                    'planet_name' => $planet->name,
                    'star_system_id' => $planet->star_system_id,
                ]);
            }

            // Check for planets without orbital coordinates
            if ($planet->orbital_distance === null ||
                $planet->orbital_angle === null ||
                $planet->orbital_inclination === null) {
                $this->addIssue('planet', 'missing_orbital_coordinates', [
                    'planet_id' => $planet->id,
                    'planet_name' => $planet->name,
                    'star_system_id' => $planet->star_system_id,
                ]);
            }

            // Check for planets without properties
            if (! $planet->properties) {
                $this->addIssue('planet', 'missing_properties', [
                    'planet_id' => $planet->id,
                    'planet_name' => $planet->name,
                ]);
            }
        }
    }

    /**
     * Check users consistency.
     */
    private function checkUsers(): void
    {
        $this->line('👥 Checking users...');

        $users = User::whereNotNull('home_planet_id')->with('homePlanet')->get();

        foreach ($users as $user) {
            // Check if home planet exists
            if (! $user->homePlanet) {
                $this->addIssue('user', 'invalid_home_planet', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'home_planet_id' => $user->home_planet_id,
                ]);

                continue;
            }

            // Check if home planet has a star system
            if (! $user->homePlanet->starSystem) {
                $this->addIssue('user', 'home_planet_no_star_system', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'planet_id' => $user->home_planet_id,
                ]);
            }
        }
    }

    /**
     * Check coordinate consistency.
     */
    private function checkCoordinates(): void
    {
        $this->line('📍 Checking coordinates...');

        $planets = Planet::with('starSystem')
            ->whereNotNull('star_system_id')
            ->whereNotNull('orbital_distance')
            ->whereNotNull('orbital_angle')
            ->whereNotNull('orbital_inclination')
            ->whereNotNull('x')
            ->whereNotNull('y')
            ->whereNotNull('z')
            ->get();

        foreach ($planets as $planet) {
            if (! $planet->starSystem) {
                continue;
            }

            // Calculate expected coordinates from orbital coordinates
            $expectedCoords = $this->orbitalToAbsolute(
                $planet->starSystem->x,
                $planet->starSystem->y,
                $planet->starSystem->z,
                $planet->orbital_distance,
                $planet->orbital_angle,
                $planet->orbital_inclination
            );

            // Check if actual coordinates match expected (with tolerance)
            $tolerance = 0.1; // Allow small floating point differences
            $distance = sqrt(
                pow($planet->x - $expectedCoords[0], 2) +
                pow($planet->y - $expectedCoords[1], 2) +
                pow($planet->z - $expectedCoords[2], 2)
            );

            if ($distance > $tolerance) {
                $this->addIssue('planet', 'coordinate_mismatch', [
                    'planet_id' => $planet->id,
                    'planet_name' => $planet->name,
                    'star_system_id' => $planet->star_system_id,
                    'expected_x' => $expectedCoords[0],
                    'expected_y' => $expectedCoords[1],
                    'expected_z' => $expectedCoords[2],
                    'actual_x' => $planet->x,
                    'actual_y' => $planet->y,
                    'actual_z' => $planet->z,
                    'distance_error' => $distance,
                ]);
            }
        }
    }

    /**
     * Check minimum distances between systems.
     */
    private function checkDistances(): void
    {
        $this->line('📏 Checking distances...');

        $systems = StarSystem::whereNotNull('x')
            ->whereNotNull('y')
            ->whereNotNull('z')
            ->get();

        $minDistance = 30.0; // Minimum expected distance
        $tooClose = [];

        for ($i = 0; $i < count($systems); $i++) {
            for ($j = $i + 1; $j < count($systems); $j++) {
                $system1 = $systems[$i];
                $system2 = $systems[$j];

                $distance = sqrt(
                    pow($system1->x - $system2->x, 2) +
                    pow($system1->y - $system2->y, 2) +
                    pow($system1->z - $system2->z, 2)
                );

                if ($distance < $minDistance && $distance > 0) {
                    $tooClose[] = [
                        'system1_id' => $system1->id,
                        'system1_name' => $system1->name,
                        'system2_id' => $system2->id,
                        'system2_name' => $system2->name,
                        'distance' => $distance,
                    ];
                }
            }
        }

        if (count($tooClose) > 0) {
            $this->addIssue('star_system', 'systems_too_close', [
                'pairs' => $tooClose,
            ]);
        }
    }

    /**
     * Convert orbital coordinates to absolute coordinates.
     */
    private function orbitalToAbsolute(
        float $systemX,
        float $systemY,
        float $systemZ,
        float $orbitalDistance,
        float $orbitalAngle,
        float $orbitalInclination
    ): array {
        $angleRad = deg2rad($orbitalAngle);
        $inclinationRad = deg2rad($orbitalInclination);

        $x = $orbitalDistance * cos($angleRad);
        $y = $orbitalDistance * sin($angleRad) * cos($inclinationRad);
        $z = $orbitalDistance * sin($angleRad) * sin($inclinationRad);

        return [
            $systemX + $x,
            $systemY + $y,
            $systemZ + $z,
        ];
    }

    /**
     * Add an issue to the issues list.
     */
    private function addIssue(string $type, string $code, array $data): void
    {
        if (! isset($this->issues[$type])) {
            $this->issues[$type] = [];
        }

        if (! isset($this->issues[$type][$code])) {
            $this->issues[$type][$code] = [];
        }

        $this->issues[$type][$code][] = $data;
        $this->stats['issues_found']++;
    }

    /**
     * Display results.
     */
    private function displayResults(bool $fix, bool $verbose): void
    {
        if (count($this->issues) === 0) {
            $this->info('✅ No issues found! Universe is consistent.');
            $this->newLine();

            return;
        }

        $this->warn('⚠️  Found '.$this->stats['issues_found'].' issue(s):');
        $this->newLine();

        foreach ($this->issues as $type => $typeIssues) {
            $this->line("📋 {$type}:");

            foreach ($typeIssues as $code => $items) {
                $count = count($items);
                $this->line("   • {$code}: {$count} occurrence(s)");

                // Show details for specific issue types
                if ($code === 'orphan_planet' && $count > 0) {
                    $this->displayOrphanPlanets($items, $verbose);
                } elseif ($code === 'systems_too_close' && $count > 0) {
                    $this->displaySystemsTooClose($items, $verbose);
                } elseif ($code === 'coordinate_mismatch' && $count > 0) {
                    $this->displayCoordinateMismatches($items, $verbose);
                } elseif ($verbose && $count <= 10) {
                    foreach ($items as $item) {
                        $this->line('     - '.json_encode($item, JSON_UNESCAPED_UNICODE));
                    }
                }
            }

            $this->newLine();
        }
    }

    /**
     * Display orphan planets details.
     */
    private function displayOrphanPlanets(array $items, bool $verbose): void
    {
        $this->line('     Orphan planets (no star system):');

        $displayCount = $verbose ? count($items) : min(5, count($items));

        for ($i = 0; $i < $displayCount; $i++) {
            $item = $items[$i];
            $this->line("       • {$item['planet_name']} (ID: {$item['planet_id']})");
        }

        if (count($items) > $displayCount) {
            $remaining = count($items) - $displayCount;
            $this->line("       ... and {$remaining} more");
        }

        if (! $verbose) {
            $this->line('     Use -v to see all orphan planets');
        }
    }

    /**
     * Display systems too close details.
     */
    private function displaySystemsTooClose(array $items, bool $verbose): void
    {
        $this->line('     Systems too close (< 30 AU):');

        // items[0] contains 'pairs' array
        $pairs = $items[0]['pairs'] ?? [];

        $displayCount = $verbose ? count($pairs) : min(5, count($pairs));

        for ($i = 0; $i < $displayCount; $i++) {
            $pair = $pairs[$i];
            $this->line("       • {$pair['system1_name']} ↔ {$pair['system2_name']} ({$pair['distance']} AU)");
        }

        if (count($pairs) > $displayCount) {
            $remaining = count($pairs) - $displayCount;
            $this->line("       ... and {$remaining} more pair(s)");
        }

        if (! $verbose) {
            $this->line('     Use -v to see all pairs');
        }
    }

    /**
     * Display coordinate mismatches details.
     */
    private function displayCoordinateMismatches(array $items, bool $verbose): void
    {
        $this->line('     Planets with coordinate mismatches:');

        $displayCount = $verbose ? count($items) : min(5, count($items));

        for ($i = 0; $i < $displayCount; $i++) {
            $item = $items[$i];
            $this->line("       • {$item['planet_name']} (error: ".number_format($item['distance_error'], 2).' AU)');

            if ($verbose) {
                $this->line("         Expected: ({$item['expected_x']}, {$item['expected_y']}, {$item['expected_z']})");
                $this->line("         Actual:   ({$item['actual_x']}, {$item['actual_y']}, {$item['actual_z']})");
            }
        }

        if (count($items) > $displayCount) {
            $remaining = count($items) - $displayCount;
            $this->line("       ... and {$remaining} more");
        }

        if (! $verbose) {
            $this->line('     Use -v to see all mismatches with details');
        }
    }

    /**
     * Attempt to fix issues automatically.
     */
    private function attemptFixes(): void
    {
        $this->info('🔧 Attempting to fix issues...');
        $this->newLine();

        // Fix planet_count mismatches
        if (isset($this->issues['star_system']['planet_count_mismatch'])) {
            $this->fixPlanetCounts();
        }

        // Fix coordinate mismatches
        if (isset($this->issues['planet']['coordinate_mismatch'])) {
            $this->fixCoordinates();
        }

        // Note: Orphan planets and systems too close can be fixed with specific flags
        if (isset($this->issues['planet']['orphan_planet']) && ! $this->option('fix-orphans')) {
            $count = count($this->issues['planet']['orphan_planet']);
            $this->warn("   ⚠️  {$count} orphan planet(s) found");
            $this->line('      Use --fix-orphans to assign them to systems automatically');
        }

        if (isset($this->issues['star_system']['systems_too_close']) && ! $this->option('fix-distances')) {
            $this->warn('   ⚠️  Systems too close found');
            $this->line('      Use --fix-distances to move them apart automatically');
        }
    }

    /**
     * Fix orphan planets by assigning them to systems.
     */
    private function fixOrphanPlanets(): void
    {
        if (! isset($this->issues['planet']['orphan_planet'])) {
            return;
        }

        $this->line('   Fixing orphan planets...');

        $orphanPlanets = Planet::whereNull('star_system_id')->get();
        $fixed = 0;

        foreach ($orphanPlanets as $planet) {
            // Try to find a system with available space (< 7 planets)
            $systems = StarSystem::withCount('planets')->get();
            $system = $systems->filter(function ($s) {
                return $s->planets_count < 7;
            })->shuffle()->first();

            if (! $system) {
                // Create a new system for this planet
                $system = app(\App\Services\StarSystemGeneratorService::class)->generateSystem();
                // Delete the generated planet
                $system->planets->each->delete();
                $system->update(['planet_count' => 0]);
            }

            // Calculate orbital coordinates
            $currentPlanetCount = $system->planets()->count();
            $orbitalDistance = $this->calculateOrbitalDistance($currentPlanetCount, $currentPlanetCount + 1);
            $orbitalAngle = ($currentPlanetCount * 360) / ($currentPlanetCount + 1);
            $orbitalInclination = rand(-15, 15);

            // Calculate absolute coordinates
            [$x, $y, $z] = $this->orbitalToAbsolute(
                $system->x,
                $system->y,
                $system->z,
                $orbitalDistance,
                $orbitalAngle,
                $orbitalInclination
            );

            // Update planet
            $planet->update([
                'star_system_id' => $system->id,
                'x' => $x,
                'y' => $y,
                'z' => $z,
                'orbital_distance' => $orbitalDistance,
                'orbital_angle' => $orbitalAngle,
                'orbital_inclination' => $orbitalInclination,
            ]);

            // Update system planet count
            $system->increment('planet_count');
            $fixed++;
        }

        $this->stats['issues_fixed'] += $fixed;
        $this->line("   ✅ Fixed {$fixed} orphan planet(s)");
    }

    /**
     * Fix systems that are too close by moving them apart.
     */
    private function fixSystemsTooClose(): void
    {
        if (! isset($this->issues['star_system']['systems_too_close'])) {
            return;
        }

        $this->line('   Fixing systems too close...');

        $minDistance = 30.0;
        $buffer = 1.0; // Add small buffer to ensure we're above minimum
        $maxIterations = 10; // Prevent infinite loops
        $iteration = 0;
        $totalFixed = 0;

        while ($iteration < $maxIterations) {
            // Reload systems fresh from database each iteration
            $systems = StarSystem::whereNotNull('x')
                ->whereNotNull('y')
                ->whereNotNull('z')
                ->get();

            $fixedThisIteration = 0;

            for ($i = 0; $i < count($systems); $i++) {
                for ($j = $i + 1; $j < count($systems); $j++) {
                    $system1 = $systems[$i];
                    $system2 = $systems[$j];

                    // Refresh from database to get latest coordinates
                    $system1->refresh();
                    $system2->refresh();

                    $distance = sqrt(
                        pow($system1->x - $system2->x, 2) +
                        pow($system1->y - $system2->y, 2) +
                        pow($system1->z - $system2->z, 2)
                    );

                    if ($distance < $minDistance && $distance > 0) {
                        // Calculate direction vector (normalized)
                        $dx = ($system1->x - $system2->x) / max($distance, 0.001);
                        $dy = ($system1->y - $system2->y) / max($distance, 0.001);
                        $dz = ($system1->z - $system2->z) / max($distance, 0.001);

                        // Calculate how much to move (add buffer to ensure we're above minimum)
                        $requiredSeparation = ($minDistance + $buffer) - $distance;
                        $moveDistance = $requiredSeparation / 2;

                        // Move system1 away from system2
                        $newX1 = $system1->x + $dx * $moveDistance;
                        $newY1 = $system1->y + $dy * $moveDistance;
                        $newZ1 = $system1->z + $dz * $moveDistance;

                        // Move system2 away from system1 (opposite direction)
                        $newX2 = $system2->x - $dx * $moveDistance;
                        $newY2 = $system2->y - $dy * $moveDistance;
                        $newZ2 = $system2->z - $dz * $moveDistance;

                        // Update systems
                        $system1->update([
                            'x' => $newX1,
                            'y' => $newY1,
                            'z' => $newZ1,
                        ]);

                        $system2->update([
                            'x' => $newX2,
                            'y' => $newY2,
                            'z' => $newZ2,
                        ]);

                        // Recalculate planet coordinates for both systems
                        $this->recalculatePlanetCoordinates($system1);
                        $this->recalculatePlanetCoordinates($system2);

                        $fixedThisIteration++;
                        $totalFixed++;
                    }
                }
            }

            // If no fixes this iteration, we're done
            if ($fixedThisIteration === 0) {
                break;
            }

            $iteration++;
        }

        $this->stats['issues_fixed'] += $totalFixed;
        $this->line("   ✅ Fixed {$totalFixed} system pair(s) that were too close");

        if ($iteration >= $maxIterations) {
            $this->warn('   ⚠️  Reached maximum iterations - some systems may still be too close');
        }
    }

    /**
     * Recalculate planet coordinates after system movement.
     */
    private function recalculatePlanetCoordinates(StarSystem $system): void
    {
        $planets = $system->planets()
            ->whereNotNull('orbital_distance')
            ->whereNotNull('orbital_angle')
            ->whereNotNull('orbital_inclination')
            ->get();

        foreach ($planets as $planet) {
            [$x, $y, $z] = $this->orbitalToAbsolute(
                $system->x,
                $system->y,
                $system->z,
                $planet->orbital_distance,
                $planet->orbital_angle,
                $planet->orbital_inclination
            );

            $planet->update([
                'x' => $x,
                'y' => $y,
                'z' => $z,
            ]);
        }
    }

    /**
     * Calculate orbital distance for a planet.
     */
    private function calculateOrbitalDistance(int $index, int $total): float
    {
        $minDistance = 5.0;
        $maxDistance = 50.0;
        $ratio = ($index + 1) / ($total + 1);
        $distance = $minDistance + ($maxDistance - $minDistance) * $ratio;
        $variation = rand(-20, 20) / 100;

        return $distance * (1 + $variation);
    }

    /**
     * Fix planet_count mismatches.
     */
    private function fixPlanetCounts(): void
    {
        $this->line('   Fixing planet_count mismatches...');

        $systems = StarSystem::withCount('planets')->get();
        $fixed = 0;

        foreach ($systems as $system) {
            if ($system->planet_count !== $system->planets_count) {
                $system->update(['planet_count' => $system->planets_count]);
                $fixed++;
            }
        }

        $this->stats['issues_fixed'] += $fixed;
        $this->line("   ✅ Fixed {$fixed} planet_count mismatch(es)");
    }

    /**
     * Fix coordinate mismatches.
     */
    private function fixCoordinates(): void
    {
        $this->line('   Fixing coordinate mismatches...');

        $planets = Planet::with('starSystem')
            ->whereNotNull('star_system_id')
            ->whereNotNull('orbital_distance')
            ->whereNotNull('orbital_angle')
            ->whereNotNull('orbital_inclination')
            ->get();

        $fixed = 0;

        foreach ($planets as $planet) {
            if (! $planet->starSystem) {
                continue;
            }

            $expectedCoords = $this->orbitalToAbsolute(
                $planet->starSystem->x,
                $planet->starSystem->y,
                $planet->starSystem->z,
                $planet->orbital_distance,
                $planet->orbital_angle,
                $planet->orbital_inclination
            );

            $planet->update([
                'x' => $expectedCoords[0],
                'y' => $expectedCoords[1],
                'z' => $expectedCoords[2],
            ]);

            $fixed++;
        }

        $this->stats['issues_fixed'] += $fixed;
        $this->line("   ✅ Fixed {$fixed} coordinate mismatch(es)");
    }

    /**
     * Display summary.
     */
    private function displaySummary(): void
    {
        $this->newLine();
        $this->info('📊 Summary:');
        $this->line("   • Star systems: {$this->stats['total_systems']}");
        $this->line("   • Planets: {$this->stats['total_planets']}");
        $this->line("   • Users with home planets: {$this->stats['total_users']}");
        $this->line("   • Issues found: {$this->stats['issues_found']}");

        if ($this->option('fix')) {
            $this->line("   • Issues fixed: {$this->stats['issues_fixed']}");
        }
    }
}

