<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UniverseTimeConfigSeeder extends Seeder
{
    /**
     * Seed the universe time configuration.
     */
    public function run(): void
    {
        // Vérifier si la config existe déjà
        $existing = DB::table('universe_time_config')->first();

        if ($existing) {
            $this->command->info('⏰ Universe time config already exists, skipping...');

            return;
        }

        // Date de référence RÉELLE : maintenant (quand l'univers est créé)
        // La date universelle de départ sera calculée à partir de base_year (2436)
        $referenceDate = now(); // Date réelle de création
        $baseYear = 2436; // Année universelle de départ
        $realDaysPerGameWeek = 7; // 1 jour réel = 1 semaine de jeu

        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate, // Date réelle de création
            'real_days_per_game_week' => $realDaysPerGameWeek,
            'base_year' => $baseYear,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Universe time config seeded successfully!');
        $this->command->line("   📅 Reference date: <fg=cyan>{$referenceDate->format('Y-m-d')}</>");
        $this->command->line("   🌍 Base year: <fg=cyan>{$baseYear}</>");
        $this->command->line("   ⚡ Ratio: <fg=cyan>1 day real = 1 week game ({$realDaysPerGameWeek} days)</>");
    }
}

