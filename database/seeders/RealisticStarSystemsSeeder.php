<?php

namespace Database\Seeders;

use App\Models\StarSystem;
use App\Models\User;
use App\Services\StarSystemGeneratorService;
use Illuminate\Database\Seeder;

class RealisticStarSystemsSeeder extends Seeder
{
    /**
     * Seed realistic star systems with proper interstellar distances.
     *
     * Uses astronomical units (AU) where:
     * - 1 light-year = 63,241 AU
     * - Minimum distance between stars: ~4 light-years = ~250,000 AU
     * - Exploration radius: ~10 light-years = ~630,000 AU
     */
    public function run(): void
    {
        $this->command->info('🌌 Generating realistic star systems...');
        $this->command->newLine();

        $generator = app(StarSystemGeneratorService::class);

        // Configuration for realistic distances
        // 1 unit = 1 AU, so we need large coordinates for interstellar distances
        $minDistanceAU = 250000; // ~4 light-years minimum between systems
        $maxDistanceAU = 10000000; // ~158 light-years max from origin

        // Get count from command option or ask interactively
        $systemsToGenerate = 50; // Default
        if ($this->command) {
            // Try to get count from command option
            try {
                if (method_exists($this->command, 'option')) {
                    $countOption = $this->command->option('count');
                    if ($countOption !== null && $countOption !== false) {
                        $systemsToGenerate = (int) $countOption;
                    } else {
                        $systemsToGenerate = (int) $this->command->ask('How many star systems to generate?', 50);
                    }
                } else {
                    $systemsToGenerate = (int) $this->command->ask('How many star systems to generate?', 50);
                }
            } catch (\Exception $e) {
                // Fallback to asking
                $systemsToGenerate = (int) $this->command->ask('How many star systems to generate?', 50);
            }
        }

        $this->command->info("Generating {$systemsToGenerate} star systems...");
        $this->command->info('Minimum distance between systems: '.number_format($minDistanceAU).' AU (~4 light-years)');
        $this->command->newLine();

        $bar = $this->command->getOutput()->createProgressBar($systemsToGenerate);
        $bar->start();

        $generatedSystems = [];
        $attempts = 0;
        $maxAttempts = $systemsToGenerate * 10; // Prevent infinite loops

        while (count($generatedSystems) < $systemsToGenerate && $attempts < $maxAttempts) {
            $attempts++;

            // Generate random coordinates in a sphere
            // Using larger range for realistic interstellar distances
            // Generate coordinates directly in AU (no division by 100)
            $x = rand(-$maxDistanceAU, $maxDistanceAU);
            $y = rand(-$maxDistanceAU, $maxDistanceAU);
            $z = rand(-$maxDistanceAU, $maxDistanceAU);

            $distance = sqrt($x * $x + $y * $y + $z * $z);

            // Ensure minimum distance from origin
            if ($distance < $minDistanceAU) {
                continue;
            }

            // Check minimum distance from existing systems
            $tooClose = false;
            foreach ($generatedSystems as $existing) {
                $existingDistance = sqrt(
                    pow($x - $existing['x'], 2) +
                        pow($y - $existing['y'], 2) +
                        pow($z - $existing['z'], 2)
                );

                if ($existingDistance < $minDistanceAU) {
                    $tooClose = true;
                    break;
                }
            }

            if ($tooClose) {
                continue;
            }

            // Generate system at this position
            try {
                // Pass coordinates directly, skip minDistance check since we handle it manually
                $system = $generator->generateSystem($x, $y, $z, 0);

                $generatedSystems[] = [
                    'id' => $system->id,
                    'name' => $system->name,
                    'x' => $x,
                    'y' => $y,
                    'z' => $z,
                ];

                $bar->advance();
            } catch (\Exception $e) {
                // Skip if generation fails
                continue;
            }
        }

        $bar->finish();
        $this->command->newLine(2);

        if (count($generatedSystems) < $systemsToGenerate) {
            $this->command->warn('⚠️  Only generated '.count($generatedSystems)." out of {$systemsToGenerate} requested systems.");
            $this->command->warn('   This may be due to space constraints. Try reducing the number or increasing max distance.');
        } else {
            $this->command->info('✅ Successfully generated '.count($generatedSystems).' star systems!');
        }

        $this->command->newLine();
        $this->command->info('📊 Statistics:');

        // Calculate distances
        $distances = [];
        for ($i = 0; $i < min(10, count($generatedSystems)); $i++) {
            for ($j = $i + 1; $j < min(10, count($generatedSystems)); $j++) {
                $dist = sqrt(
                    pow($generatedSystems[$i]['x'] - $generatedSystems[$j]['x'], 2) +
                        pow($generatedSystems[$i]['y'] - $generatedSystems[$j]['y'], 2) +
                        pow($generatedSystems[$i]['z'] - $generatedSystems[$j]['z'], 2)
                );
                $distances[] = $dist;
            }
        }

        if (! empty($distances)) {
            $avgDistance = array_sum($distances) / count($distances);
            $minDistance = min($distances);
            $maxDistance = max($distances);

            $this->command->line('   • Average distance: '.number_format($avgDistance, 0).' AU (~'.number_format($avgDistance / 63241, 1).' ly)');
            $this->command->line('   • Minimum distance: '.number_format($minDistance, 0).' AU (~'.number_format($minDistance / 63241, 1).' ly)');
            $this->command->line('   • Maximum distance: '.number_format($maxDistance, 0).' AU (~'.number_format($maxDistance / 63241, 1).' ly)');
        }

        $this->command->newLine();

        // Mark player systems as discovered and create discovery routes
        $this->markPlayerSystemsAsDiscovered();

        $this->command->info('✨ Realistic star systems seeded successfully!');
    }

    /**
     * Mark player home systems as discovered and create logical discovery routes.
     */
    private function markPlayerSystemsAsDiscovered(): void
    {
        $this->command->info('🔍 Creating discovery routes from player starting systems...');
        $this->command->newLine();

        // Get all users with home planets
        $users = User::whereNotNull('home_planet_id')
            ->with('homePlanet.starSystem')
            ->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  No users with home planets found. Skipping discovery routes.');

            return;
        }

        // Get all star systems
        $allSystems = StarSystem::all();

        // Exploration radius: ~10 light-years = ~630,000 AU
        // But we'll use a more reasonable radius for initial discovery: ~5 light-years = ~315,000 AU
        $explorationRadius = 315000; // ~5 light-years

        // Number of systems to discover per player starting system
        $systemsPerPlayer = 3; // Discover 3 nearby systems per player

        $discoveredCount = 0;
        $playerSystemsDiscovered = 0;

        foreach ($users as $user) {
            $homePlanet = $user->homePlanet;
            if (! $homePlanet || ! $homePlanet->starSystem) {
                continue;
            }

            $playerSystem = $homePlanet->starSystem;

            // Mark player's home system as discovered
            if (! $playerSystem->discovered) {
                $playerSystem->update(['discovered' => true]);
                $playerSystemsDiscovered++;
                $discoveredCount++;
            }

            // Find nearby systems within exploration radius
            $nearbySystems = StarSystem::nearby(
                $playerSystem->x,
                $playerSystem->y,
                $playerSystem->z,
                $explorationRadius
            )
                ->filter(function ($system) use ($playerSystem) {
                    return $system->id !== $playerSystem->id && ! $system->discovered;
                })
                ->sortBy(function ($system) use ($playerSystem) {
                    return $playerSystem->distanceTo($system);
                });

            // Discover the closest systems (up to systemsPerPlayer)
            $systemsToDiscover = $nearbySystems->take($systemsPerPlayer);

            foreach ($systemsToDiscover as $system) {
                $system->update(['discovered' => true]);
                $discoveredCount++;
            }

            $this->command->line("   • Player: {$user->name} - System: {$playerSystem->name}");
            $this->command->line("     Discovered: {$systemsToDiscover->count()} nearby systems");
        }

        // Create additional discovery routes between discovered systems
        // This creates "trade routes" or "exploration paths" between player areas
        $this->createDiscoveryRoutes($allSystems, $explorationRadius);

        $this->command->newLine();
        $this->command->info('✅ Discovery routes created:');
        $this->command->line("   • Player systems marked as discovered: {$playerSystemsDiscovered}");
        $this->command->line("   • Total systems discovered: {$discoveredCount}");

        $totalSystems = StarSystem::count();
        $discoveredSystems = StarSystem::where('discovered', true)->count();
        $discoveryPercentage = $totalSystems > 0 ? round(($discoveredSystems / $totalSystems) * 100, 1) : 0;

        $this->command->line("   • Discovery rate: {$discoveredSystems}/{$totalSystems} ({$discoveryPercentage}%)");
    }

    /**
     * Create discovery routes between discovered systems.
     * This connects discovered systems to create logical exploration paths.
     * Uses a single-pass approach to avoid discovering too many systems.
     */
    private function createDiscoveryRoutes($allSystems, float $explorationRadius): void
    {
        // Get all discovered systems (from player starting points)
        $discoveredSystems = StarSystem::where('discovered', true)->get();

        if ($discoveredSystems->count() < 2) {
            return; // Need at least 2 discovered systems to create routes
        }

        $routesCreated = 0;
        $maxRoutesPerSystem = 1; // Each discovered system can discover up to 1 more system
        $maxTotalRoutes = 10; // Maximum total routes to create (prevents over-discovery)

        // For each discovered system, try to discover ONE nearby system
        // This creates "bridges" between player areas without discovering everything
        foreach ($discoveredSystems as $discoveredSystem) {
            if ($routesCreated >= $maxTotalRoutes) {
                break; // Stop if we've created enough routes
            }

            // Find undiscovered systems within exploration radius
            $nearbyUndiscovered = StarSystem::nearby(
                $discoveredSystem->x,
                $discoveredSystem->y,
                $discoveredSystem->z,
                $explorationRadius
            )
                ->filter(function ($system) {
                    return ! $system->discovered;
                })
                ->sortBy(function ($system) use ($discoveredSystem) {
                    return $discoveredSystem->distanceTo($system);
                });

            // Discover only the closest system (creates a route)
            $systemToDiscover = $nearbyUndiscovered->first();

            if ($systemToDiscover) {
                $systemToDiscover->update(['discovered' => true]);
                $routesCreated++;
            }
        }

        if ($routesCreated > 0) {
            $this->command->line("   • Additional routes created: {$routesCreated} systems");
        }
    }
}
