<?php

namespace Database\Seeders;

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
        $this->command->info('✨ Realistic star systems seeded successfully!');
    }
}

