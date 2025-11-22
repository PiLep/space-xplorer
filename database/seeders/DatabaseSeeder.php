<?php

namespace Database\Seeders;

use App\Models\Planet;
use App\Models\StarSystem;
use App\Models\User;
use App\Services\PlanetGeneratorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌌 Seeding Stellar database...');
        $this->command->newLine();

        // Seed universe time configuration first
        $this->call(UniverseTimeConfigSeeder::class);
        $this->command->newLine();

        // Seed scheduled tasks
        $this->call(ScheduledTaskSeeder::class);
        $this->command->newLine();

        // Ensure star systems exist (generate if needed)
        $this->ensureStarSystemsExist();
        $this->command->newLine();

        // Password par défaut pour tous les utilisateurs de test
        $defaultPassword = 'password';

        // Créer des utilisateurs de test avec leurs planètes
        $users = [
            [
                'name' => 'Alex Explorer',
                'email' => 'alex@stellar-game.test',
            ],
            [
                'name' => 'Sam Navigator',
                'email' => 'sam@stellar-game.test',
            ],
            [
                'name' => 'Morgan Pilot',
                'email' => 'morgan@stellar-game.test',
            ],
            [
                'name' => 'Jordan Commander',
                'email' => 'jordan@stellar-game.test',
            ],
            [
                'name' => 'Riley Explorer',
                'email' => 'riley@stellar-game.test',
            ],
        ];

        $createdUsers = [];
        $planetGenerator = app(PlanetGeneratorService::class);

        foreach ($users as $userData) {
            // Check if user already exists
            $user = User::where('email', $userData['email'])->first();

            if (! $user) {
                // Create new user
                $userAttributes = [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($defaultPassword),
                    'email_verified_at' => now(),
                ];

                // Make Alex Explorer a super admin
                if ($userData['email'] === 'alex@stellar-game.test') {
                    $userAttributes['is_super_admin'] = true;
                }

                $user = User::create($userAttributes);
            } else {
                // Update existing user to ensure super admin status
                if ($userData['email'] === 'alex@stellar-game.test' && ! $user->is_super_admin) {
                    $user->update(['is_super_admin' => true]);
                }
            }

            // Assigner un système stellaire existant au joueur (seulement s'il n'en a pas déjà)
            if (! $user->home_planet_id) {
                $homePlanet = $this->assignExistingSystemToUser($user, $planetGenerator);
            }

            // Rafraîchir pour obtenir la planète assignée
            $user->refresh();

            $createdUsers[] = [
                'user' => $user,
                'planet' => $user->homePlanet,
            ];
        }

        // Créer quelques planètes supplémentaires (non assignées)
        $extraPlanets = Planet::factory()->count(5)->create();

        // Afficher les informations de connexion
        $this->command->info('✅ Users created successfully!');
        $this->command->newLine();
        $this->command->info('📋 Login Credentials:');
        $this->command->newLine();

        foreach ($createdUsers as $data) {
            $user = $data['user'];
            $planet = $data['planet'];

            $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->command->line("👤 <fg=cyan>{$user->name}</>");
            $this->command->line("   📧 Email: <fg=yellow>{$user->email}</>");
            $this->command->line("   🔑 Password: <fg=yellow>{$defaultPassword}</>");
            $this->command->line("   🆔 User ID: <fg=gray>{$user->id}</>");

            if ($planet) {
                $system = $planet->starSystem;
                $this->command->line("   🪐 Home Planet: <fg=green>{$planet->name}</>");
                $this->command->line("      Type: {$planet->type} | Size: {$planet->size} | Temp: {$planet->temperature}");
                $this->command->line("      🌟 Star System: <fg=cyan>{$system->name}</> ({$system->star_type})");
                $this->command->line("      🆔 Planet ID: <fg=gray>{$planet->id}</>");
            } else {
                $this->command->line('   ⚠️  <fg=red>No home planet assigned</>');
            }
            $this->command->newLine();
        }

        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📊 Summary:');
        $this->command->line('   • Users created: <fg=cyan>'.count($createdUsers).'</>');
        $this->command->line('   • Planets created: <fg=cyan>'.(count($createdUsers) + $extraPlanets->count()).'</>');
        $this->command->line("   • Default password for all users: <fg=yellow>{$defaultPassword}</>");
        $this->command->newLine();

        // Mark player systems as discovered and create discovery routes
        $this->markPlayerSystemsAsDiscovered();

        $this->command->info('✨ Database seeded successfully!');
        $this->command->newLine();

        // Seed codex data
        $this->call(CodexSeeder::class);
    }

    /**
     * Ensure star systems exist in the database.
     * If none exist, generate a minimum number.
     */
    private function ensureStarSystemsExist(): void
    {
        $existingSystemsCount = StarSystem::count();
        $minSystems = 50; // Minimum systems needed for proper discovery distribution

        if ($existingSystemsCount === 0) {
            $this->command->warn('⚠️  No star systems found. Generating minimum systems...');
            $this->generateStarSystemsDirectly($minSystems);
        } elseif ($existingSystemsCount < $minSystems) {
            $systemsToGenerate = $minSystems - $existingSystemsCount;
            $this->command->info("✅ Found {$existingSystemsCount} existing star systems");
            $this->command->info("⚠️  Generating {$systemsToGenerate} additional systems to reach minimum of {$minSystems}...");
            $this->generateStarSystemsDirectly($systemsToGenerate);
        } else {
            $this->command->info("✅ Found {$existingSystemsCount} existing star systems (sufficient)");
        }
    }

    /**
     * Generate star systems directly using the service.
     */
    private function generateStarSystemsDirectly(int $count): void
    {
        $generator = app(\App\Services\StarSystemGeneratorService::class);
        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            try {
                $generator->generateSystem();
                $bar->advance();
            } catch (\Exception $e) {
                // Continue on error
            }
        }

        $bar->finish();
        $this->command->newLine();
    }

    /**
     * Assign an existing star system to a user by creating a home planet in it.
     * Prefers systems that don't already have a home planet assigned.
     */
    private function assignExistingSystemToUser(User $user, PlanetGeneratorService $planetGenerator): ?Planet
    {
        // Get all systems that have planets assigned as home planets
        $systemsWithHomePlanets = User::whereNotNull('home_planet_id')
            ->with('homePlanet.starSystem')
            ->get()
            ->pluck('homePlanet.starSystem.id')
            ->filter()
            ->unique();

        // Find systems that don't have a home planet assigned yet
        $availableSystems = StarSystem::whereNotIn('id', $systemsWithHomePlanets)->get();

        // If no completely free systems, use any system (we'll create a planet in it)
        if ($availableSystems->isEmpty()) {
            $availableSystems = StarSystem::all();
        }

        if ($availableSystems->isEmpty()) {
            $this->command->error("⚠️  No star systems available for user {$user->name}");

            return null;
        }

        // Select a random system
        $selectedSystem = $availableSystems->random();

        // Generate a planet in this system
        $planet = $planetGenerator->generate();

        // Calculate orbital coordinates
        $orbitalDistance = 10.0; // Standard distance for home planet
        $orbitalAngle = 0; // Start at 0 degrees
        $orbitalInclination = 0; // No inclination

        // Convert orbital coordinates to absolute coordinates
        [$x, $y, $z] = $this->orbitalToAbsolute(
            $selectedSystem->x,
            $selectedSystem->y,
            $selectedSystem->z,
            $orbitalDistance,
            $orbitalAngle,
            $orbitalInclination
        );

        // Update planet with system and coordinates
        $planet->update([
            'star_system_id' => $selectedSystem->id,
            'x' => $x,
            'y' => $y,
            'z' => $z,
            'orbital_distance' => $orbitalDistance,
            'orbital_angle' => $orbitalAngle,
            'orbital_inclination' => $orbitalInclination,
        ]);

        // Assign planet to user
        $user->update(['home_planet_id' => $planet->id]);

        // Update planet count for the system
        $selectedSystem->increment('planet_count');

        return $planet;
    }

    /**
     * Convert orbital coordinates to absolute 3D coordinates.
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

    /**
     * Mark player home systems as discovered and create logical discovery routes.
     */
    private function markPlayerSystemsAsDiscovered(): void
    {
        $this->command->info('🔍 Creating discovery routes from player starting systems...');
        $this->command->newLine();

        // Reset all systems to undiscovered first (to start fresh)
        StarSystem::query()->update(['discovered' => false]);
        $this->command->line('   • Reset all systems to undiscovered');

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
        $systemsPerPlayer = 2; // Discover 2 nearby systems per player (reduced to leave more undiscovered)

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
        $maxTotalRoutes = 5; // Maximum total routes to create (reduced to leave more undiscovered)

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
