<?php

namespace App\Console\Commands;

use App\Models\StarSystem;
use Database\Seeders\RealisticStarSystemsSeeder;
use Illuminate\Console\Command;

class SeedRealisticStarSystems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'star-systems:seed-realistic {--count=50 : Number of star systems to generate} {--clear : Clear existing star systems first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate realistic star systems with proper interstellar distances (in AU)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');

        if ($count < 1 || $count > 1000) {
            $this->error('Count must be between 1 and 1000');

            return 1;
        }

        // Clear existing systems if requested
        if ($this->option('clear')) {
            if (! $this->confirm('This will delete all existing star systems. Are you sure?')) {
                $this->info('Cancelled.');

                return 0;
            }

            $deleted = StarSystem::count();
            StarSystem::query()->delete();
            $this->info("Deleted {$deleted} existing star systems.");
            $this->newLine();
        }

        // Create seeder instance and set command
        $seeder = new RealisticStarSystemsSeeder;
        $seeder->setCommand($this);

        // Call the seeder
        $seeder->run();

        return 0;
    }
}
