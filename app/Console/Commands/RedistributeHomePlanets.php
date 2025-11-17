<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RedistributeHomePlanets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'universe:redistribute-home-planets 
                            {--min-distance=100 : Minimum distance from origin (in AU)}
                            {--spacing=200 : Minimum spacing between home systems (in AU)}
                            {--dry-run : Show what would be changed without actually moving anything}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redistribute player home planets and their star systems uniformly in space';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $minDistance = (float) $this->option('min-distance');
        $spacing = (float) $this->option('spacing');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // Get all users with home planets
        $users = User::whereNotNull('home_planet_id')
            ->with(['homePlanet.starSystem'])
            ->get();

        if ($users->isEmpty()) {
            $this->info('No users with home planets found.');

            return 0;
        }

        $this->info("📊 Found {$users->count()} users with home planets");
        $this->newLine();

        // Analyze current distribution
        $this->analyzeCurrentDistribution($users);

        // Calculate new positions
        $newPositions = $this->calculateUniformDistribution($users->count(), $minDistance, $spacing);

        // Show what will be changed
        $this->displayRedistributionPlan($users, $newPositions);

        if ($dryRun) {
            $this->info('🔍 DRY RUN - No changes made');
            $this->newLine();

            return 0;
        }

        // Confirm before proceeding
        if (! $force && ! $this->confirm('Do you want to proceed with the redistribution?', true)) {
            $this->info('Cancelled.');

            return 0;
        }

        // Perform redistribution
        $this->performRedistribution($users, $newPositions);

        $this->newLine();
        $this->info('✅ Redistribution complete!');

        return 0;
    }

    /**
     * Analyze current distribution of home planets.
     */
    private function analyzeCurrentDistribution($users): void
    {
        $this->info('📈 Current distribution analysis:');

        $systems = $users->map(function ($user) {
            return $user->homePlanet?->starSystem;
        })->filter();

        if ($systems->isEmpty()) {
            $this->warn('   No star systems found for home planets');
            $this->newLine();

            return;
        }

        // Calculate statistics
        $xCoords = $systems->pluck('x')->filter();
        $yCoords = $systems->pluck('y')->filter();
        $zCoords = $systems->pluck('z')->filter();

        $this->line("   • Systems analyzed: {$systems->count()}");

        if ($xCoords->isNotEmpty()) {
            $this->line("   • X range: {$xCoords->min()} to {$xCoords->max()} AU");
            $this->line("   • Y range: {$yCoords->min()} to {$yCoords->max()} AU");
            $this->line("   • Z range: {$zCoords->min()} to {$zCoords->max()} AU");

            // Calculate average distance from origin
            $avgDistance = $systems->map(function ($system) {
                return sqrt($system->x ** 2 + $system->y ** 2 + $system->z ** 2);
            })->avg();

            $this->line('   • Average distance from origin: '.number_format($avgDistance, 2).' AU');
        }

        // Check for clustering
        $minDistance = $this->findMinimumDistanceBetweenSystems($systems);
        $this->line('   • Minimum distance between systems: '.number_format($minDistance, 2).' AU');

        $this->newLine();
    }

    /**
     * Find minimum distance between any two systems.
     */
    private function findMinimumDistanceBetweenSystems($systems): float
    {
        $minDistance = PHP_FLOAT_MAX;
        $systemArray = $systems->all();

        for ($i = 0; $i < count($systemArray); $i++) {
            for ($j = $i + 1; $j < count($systemArray); $j++) {
                $system1 = $systemArray[$i];
                $system2 = $systemArray[$j];

                if (! $system1 || ! $system2) {
                    continue;
                }

                $distance = sqrt(
                    pow($system1->x - $system2->x, 2) +
                    pow($system1->y - $system2->y, 2) +
                    pow($system1->z - $system2->z, 2)
                );

                if ($distance < $minDistance) {
                    $minDistance = $distance;
                }
            }
        }

        return $minDistance === PHP_FLOAT_MAX ? 0 : $minDistance;
    }

    /**
     * Calculate uniform distribution positions for systems.
     *
     * Distributes systems uniformly on concentric spheres starting from the center.
     * Uses Fibonacci sphere algorithm for uniform distribution on each sphere layer.
     *
     * @return array<array{x: float, y: float, z: float}>
     */
    private function calculateUniformDistribution(int $count, float $minDistance, float $spacing): array
    {
        $positions = [];

        if ($count === 1) {
            // Single system: place at minimum distance from origin
            $positions[] = ['x' => $minDistance, 'y' => 0.0, 'z' => 0.0];

            return $positions;
        }

        // Calculate how many systems per sphere layer
        // We want to distribute systems on multiple concentric spheres
        // to ensure good spacing from the center outward
        $systemsPerLayer = max(8, (int) ceil(sqrt($count * 2))); // At least 8 per layer
        $layers = (int) ceil($count / $systemsPerLayer);

        // Calculate radius for each layer
        // First layer starts at minDistance, each subsequent layer adds spacing
        $layerRadius = [];
        for ($layer = 0; $layer < $layers; $layer++) {
            $layerRadius[$layer] = $minDistance + ($layer * $spacing * 0.8); // 0.8 to allow some overlap
        }

        // Using Fibonacci sphere algorithm for uniform distribution on sphere
        $goldenAngle = M_PI * (3 - sqrt(5)); // Golden angle in radians

        $systemIndex = 0;

        for ($layer = 0; $layer < $layers && $systemIndex < $count; $layer++) {
            $radius = $layerRadius[$layer];

            // Calculate how many systems on this layer
            $systemsOnThisLayer = min($systemsPerLayer, $count - $systemIndex);

            // Distribute systems uniformly on this sphere layer
            for ($i = 0; $i < $systemsOnThisLayer && $systemIndex < $count; $i++) {
                // Calculate angle using golden angle
                $theta = $goldenAngle * $i;

                // Calculate y coordinate (from -1 to 1)
                $y = 1 - (2 * $i) / max($systemsOnThisLayer - 1, 1);

                // Calculate radius at this y level (for sphere)
                $radiusAtY = sqrt(1 - $y * $y);

                // Calculate x and z on the sphere
                $x = $radiusAtY * cos($theta);
                $z = $radiusAtY * sin($theta);

                // Scale to desired radius (distance from origin)
                $positions[] = [
                    'x' => $x * $radius,
                    'y' => $y * $radius,
                    'z' => $z * $radius,
                ];

                $systemIndex++;
            }
        }

        // Verify minimum spacing and adjust if needed
        // This ensures systems aren't too close to each other
        $this->ensureMinimumSpacing($positions, $spacing);

        // Ensure all systems are at least minDistance from origin
        foreach ($positions as &$pos) {
            $distanceFromOrigin = sqrt($pos['x'] ** 2 + $pos['y'] ** 2 + $pos['z'] ** 2);
            if ($distanceFromOrigin < $minDistance) {
                // Normalize and scale to minDistance
                $factor = $minDistance / max($distanceFromOrigin, 0.001);
                $pos['x'] *= $factor;
                $pos['y'] *= $factor;
                $pos['z'] *= $factor;
            }
        }

        return $positions;
    }

    /**
     * Ensure minimum spacing between positions by adjusting if too close.
     *
     * Moves systems apart while maintaining their distance from origin as much as possible.
     *
     * @param  array<array{x: float, y: float, z: float}>  $positions
     */
    private function ensureMinimumSpacing(array &$positions, float $minSpacing): void
    {
        $maxIterations = 20;
        $iteration = 0;
        $adjustmentFactor = 0.5; // How much to move systems apart (0.5 = move halfway)

        while ($iteration < $maxIterations) {
            $adjusted = false;

            for ($i = 0; $i < count($positions); $i++) {
                for ($j = $i + 1; $j < count($positions); $j++) {
                    $distance = sqrt(
                        pow($positions[$i]['x'] - $positions[$j]['x'], 2) +
                        pow($positions[$i]['y'] - $positions[$j]['y'], 2) +
                        pow($positions[$i]['z'] - $positions[$j]['z'], 2)
                    );

                    if ($distance < $minSpacing) {
                        // Calculate direction vector from j to i
                        $dx = ($positions[$i]['x'] - $positions[$j]['x']) / max($distance, 0.001);
                        $dy = ($positions[$i]['y'] - $positions[$j]['y']) / max($distance, 0.001);
                        $dz = ($positions[$i]['z'] - $positions[$j]['z']) / max($distance, 0.001);

                        // Calculate how much to move
                        $moveDistance = ($minSpacing - $distance) * $adjustmentFactor;

                        // Move both systems apart
                        // System i moves away from j
                        $positions[$i]['x'] += $dx * $moveDistance;
                        $positions[$i]['y'] += $dy * $moveDistance;
                        $positions[$i]['z'] += $dz * $moveDistance;

                        // System j moves away from i (opposite direction)
                        $positions[$j]['x'] -= $dx * $moveDistance;
                        $positions[$j]['y'] -= $dy * $moveDistance;
                        $positions[$j]['z'] -= $dz * $moveDistance;

                        $adjusted = true;
                    }
                }
            }

            if (! $adjusted) {
                break;
            }

            $iteration++;

            // Reduce adjustment factor over iterations for smoother convergence
            if ($iteration % 5 === 0) {
                $adjustmentFactor *= 0.8;
            }
        }
    }

    /**
     * Display redistribution plan.
     */
    private function displayRedistributionPlan($users, array $newPositions): void
    {
        $this->info('📋 Redistribution plan:');
        $this->newLine();

        $table = [];
        foreach ($users as $index => $user) {
            $system = $user->homePlanet?->starSystem;
            $newPos = $newPositions[$index] ?? null;

            if (! $system || ! $newPos) {
                continue;
            }

            $oldDistance = sqrt($system->x ** 2 + $system->y ** 2 + $system->z ** 2);
            $newDistance = sqrt($newPos['x'] ** 2 + $newPos['y'] ** 2 + $newPos['z'] ** 2);
            $moveDistance = sqrt(
                pow($system->x - $newPos['x'], 2) +
                pow($system->y - $newPos['y'], 2) +
                pow($system->z - $newPos['z'], 2)
            );

            $table[] = [
                'User' => $user->name,
                'System' => $system->name,
                'Old Distance' => number_format($oldDistance, 2).' AU',
                'New Distance' => number_format($newDistance, 2).' AU',
                'Move Distance' => number_format($moveDistance, 2).' AU',
            ];
        }

        $this->table(
            ['User', 'System', 'Old Distance', 'New Distance', 'Move Distance'],
            $table
        );
        $this->newLine();
    }

    /**
     * Perform the actual redistribution.
     */
    private function performRedistribution($users, array $newPositions): void
    {
        $this->info('🚀 Starting redistribution...');
        $this->newLine();

        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        DB::transaction(function () use ($users, $newPositions, $progressBar) {
            foreach ($users as $index => $user) {
                $homePlanet = $user->homePlanet;
                $starSystem = $homePlanet?->starSystem;

                if (! $homePlanet || ! $starSystem) {
                    $progressBar->advance();

                    continue;
                }

                $newPos = $newPositions[$index] ?? null;
                if (! $newPos) {
                    $progressBar->advance();

                    continue;
                }

                // Move the star system first
                $starSystem->update([
                    'x' => $newPos['x'],
                    'y' => $newPos['y'],
                    'z' => $newPos['z'],
                ]);

                // Recalculate absolute coordinates for all planets in this system
                // using their orbital coordinates and the new system position
                $planets = $starSystem->planets;
                foreach ($planets as $planet) {
                    if ($planet->orbital_distance !== null &&
                        $planet->orbital_angle !== null &&
                        $planet->orbital_inclination !== null) {

                        // Recalculate absolute coordinates from orbital coordinates
                        [$x, $y, $z] = $this->orbitalToAbsolute(
                            $newPos['x'],
                            $newPos['y'],
                            $newPos['z'],
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

                $progressBar->advance();
            }
        });

        $progressBar->finish();
        $this->newLine();
    }

    /**
     * Convert orbital coordinates to absolute 3D coordinates.
     *
     * @return array{float, float, float}
     */
    private function orbitalToAbsolute(
        float $systemX,
        float $systemY,
        float $systemZ,
        float $orbitalDistance,
        float $orbitalAngle,
        float $orbitalInclination
    ): array {
        // Convert angle to radians
        $angleRad = deg2rad($orbitalAngle);
        $inclinationRad = deg2rad($orbitalInclination);

        // Calculate position in orbital plane
        $x = $orbitalDistance * cos($angleRad);
        $y = $orbitalDistance * sin($angleRad) * cos($inclinationRad);
        $z = $orbitalDistance * sin($angleRad) * sin($inclinationRad);

        // Add system position
        return [
            $systemX + $x,
            $systemY + $y,
            $systemZ + $z,
        ];
    }
}

