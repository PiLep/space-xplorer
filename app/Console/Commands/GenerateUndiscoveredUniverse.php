<?php

namespace App\Console\Commands;

use App\Models\Planet;
use App\Models\StarSystem;
use App\Services\StarSystemGeneratorService;
use Illuminate\Console\Command;

class GenerateUndiscoveredUniverse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'universe:generate-undiscovered 
                            {--count=10 : Number of star systems to generate}
                            {--min-distance=30 : Minimum distance between systems (in AU)}
                            {--max-attempts=500 : Maximum attempts to find valid coordinates}
                            {--expand-range : Expand coordinate range for more space}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate undiscovered star systems (excluding home star systems of users)';

    /**
     * Execute the console command.
     */
    public function handle(StarSystemGeneratorService $generator): int
    {
        $count = (int) $this->option('count');
        $minDistance = (float) $this->option('min-distance');
        $maxAttempts = (int) $this->option('max-attempts');
        $expandRange = $this->option('expand-range');

        if ($count < 1) {
            $this->error('Count must be at least 1');

            return 1;
        }

        $this->info("🌌 Generating {$count} undiscovered star systems...");
        $this->newLine();

        // Get all home star systems (systems that contain home planets)
        $homeStarSystemIds = $this->getHomeStarSystemIds();

        $this->info('📊 Found '.count($homeStarSystemIds).' home star systems to exclude');
        $this->newLine();

        // Get all existing star systems for distance checking
        // We load all systems to check distances, but this could be optimized
        // for very large universes by using spatial queries
        $existingSystems = StarSystem::all();

        // Calculate coordinate range based on existing systems
        $coordinateRange = $this->calculateCoordinateRange($existingSystems, $expandRange);
        $this->info("📍 Using coordinate range: ±{$coordinateRange} AU");
        $this->newLine();

        $generated = 0;
        $failed = 0;

        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        for ($i = 0; $i < $count; $i++) {
            $system = $this->generateValidSystem(
                $generator,
                $existingSystems,
                $homeStarSystemIds,
                $minDistance,
                $maxAttempts,
                $coordinateRange
            );

            if ($system) {
                $existingSystems->push($system);
                $generated++;
            } else {
                $failed++;
                $this->newLine();
                $this->warn('⚠️  Failed to generate system #'.($i + 1)." after {$maxAttempts} attempts");
                $this->warn('   Try reducing --min-distance or using --expand-range');
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('✅ Generation complete!');
        $this->line("   • Generated: {$generated} star systems");
        if ($failed > 0) {
            $this->line("   • Failed: {$failed} star systems");
        }

        $totalUndiscovered = StarSystem::where('discovered', false)->count();
        $this->line("   • Total undiscovered systems: {$totalUndiscovered}");

        return 0;
    }

    /**
     * Get IDs of all star systems that contain home planets.
     *
     * @return array<string>
     */
    private function getHomeStarSystemIds(): array
    {
        // Optimized query: get star system IDs directly from planets table
        return Planet::whereIn('id', function ($query) {
            $query->select('home_planet_id')
                ->from('users')
                ->whereNotNull('home_planet_id');
        })
            ->whereNotNull('star_system_id')
            ->distinct()
            ->pluck('star_system_id')
            ->toArray();
    }

    /**
     * Calculate appropriate coordinate range based on existing systems.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $existingSystems
     */
    private function calculateCoordinateRange($existingSystems, bool $expandRange): float
    {
        if ($existingSystems->isEmpty()) {
            return $expandRange ? 200.0 : 100.0;
        }

        // Find the maximum absolute coordinate value
        $maxCoord = $existingSystems->map(function ($system) {
            return max(abs($system->x), abs($system->y), abs($system->z));
        })->max();

        // Add buffer and expand if requested
        $baseRange = max($maxCoord * 1.5, 100.0);

        return $expandRange ? $baseRange * 2 : $baseRange;
    }

    /**
     * Generate a valid star system that is not too close to existing systems.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $existingSystems
     * @param  array<string>  $homeStarSystemIds  Not used directly, but kept for potential future use
     */
    private function generateValidSystem(
        StarSystemGeneratorService $generator,
        $existingSystems,
        array $homeStarSystemIds,
        float $minDistance,
        int $maxAttempts,
        float $coordinateRange
    ): ?StarSystem {
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            try {
                // Generate coordinates within the calculated range
                [$x, $y, $z] = $this->generateRandomCoordinatesInRange($coordinateRange);

                // Generate a new system at these coordinates
                $system = $generator->generateSystem($x, $y, $z, 0);

                // Check distance to all existing systems (including home systems)
                // This ensures we don't generate systems too close to any existing system
                $tooClose = false;
                foreach ($existingSystems as $existingSystem) {
                    // Skip if this is the same system (shouldn't happen, but safety check)
                    if ($system->id === $existingSystem->id) {
                        continue;
                    }

                    $distance = $system->distanceTo($existingSystem);
                    if ($distance < $minDistance) {
                        $tooClose = true;
                        break;
                    }
                }

                if ($tooClose) {
                    // Delete the system and its planets if too close
                    $system->planets()->delete();
                    $system->delete();

                    continue;
                }

                // Valid system found - ensure it's marked as undiscovered
                if ($system->discovered) {
                    $system->update(['discovered' => false]);
                }

                // Valid system found
                return $system;
            } catch (\Exception $e) {
                // Log error but continue trying
                \Log::warning('Error generating star system', [
                    'attempt' => $attempt + 1,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }
        }

        return null;
    }

    /**
     * Generate random coordinates within a specified range.
     *
     * @return array{float, float, float}
     */
    private function generateRandomCoordinatesInRange(float $range): array
    {
        // Generate coordinates in a cube, then filter by distance from origin
        // This ensures we don't generate systems too close to origin (where players start)
        $minDistanceFromOrigin = 50.0;

        do {
            $x = (rand(-$range * 100, $range * 100)) / 100;
            $y = (rand(-$range * 100, $range * 100)) / 100;
            $z = (rand(-$range * 100, $range * 100)) / 100;
            $distance = sqrt($x * $x + $y * $y + $z * $z);
        } while ($distance < $minDistanceFromOrigin);

        return [$x, $y, $z];
    }
}

