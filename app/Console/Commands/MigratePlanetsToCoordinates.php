<?php

namespace App\Console\Commands;

use App\Models\Planet;
use App\Models\StarSystem;
use App\Services\PlanetGeneratorService;
use App\Services\StarSystemGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigratePlanetsToCoordinates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'planets:migrate-coordinates
                            {--force : Force migration even if planets already have coordinates}
                            {--planets-per-system=3 : Number of planets per system (for grouping)}
                            {--assign-existing : Assign planets to existing systems instead of creating new ones}
                            {--isolate : Create one system per planet (isolated)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing planets to the new coordinate system by grouping them into star systems (default) or assigning to existing systems';

    /**
     * Execute the console command.
     */
    public function handle(
        StarSystemGeneratorService $starSystemGenerator,
        PlanetGeneratorService $planetGenerator
    ): int {
        $force = $this->option('force');
        $planetsPerSystem = (int) $this->option('planets-per-system');
        $assignExisting = $this->option('assign-existing');
        $isolate = $this->option('isolate');

        if ($planetsPerSystem < 1) {
            $this->error('Planets per system must be at least 1.');

            return Command::FAILURE;
        }

        // Vérifier si la table star_systems existe
        if (! Schema::hasTable('star_systems')) {
            $this->error('The star_systems table does not exist. Please run migrations first.');

            return Command::FAILURE;
        }

        // Trouver les planètes à migrer
        $query = Planet::query();

        if (! $force) {
            $query->whereNull('star_system_id')
                ->where(function ($q) {
                    $q->whereNull('x')
                        ->orWhereNull('y')
                        ->orWhereNull('z');
                });
        }

        $planetsToMigrate = $query->get();
        $totalPlanets = $planetsToMigrate->count();

        if ($totalPlanets === 0) {
            $this->info('No planets need migration. All planets already have coordinates.');

            return Command::SUCCESS;
        }

        // Séparer les planètes d'origine (home planets) des autres planètes
        $homePlanets = $planetsToMigrate->filter(function ($planet) {
            return $planet->users()->exists(); // Planètes qui sont des planètes d'origine
        });

        $otherPlanets = $planetsToMigrate->filter(function ($planet) {
            return ! $planet->users()->exists(); // Planètes qui ne sont pas des planètes d'origine
        });

        $homePlanetsCount = $homePlanets->count();
        $otherPlanetsCount = $otherPlanets->count();

        $this->info("Found {$totalPlanets} planet(s) to migrate:");
        $this->info("  - {$homePlanetsCount} home planet(s) (will create their own system, one per player)");
        $this->info("  - {$otherPlanetsCount} other planet(s)");
        $this->newLine();

        if (! $this->confirm('Do you want to proceed?', true)) {
            $this->info('Migration cancelled.');

            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($totalPlanets);
        $bar->start();

        $migrated = 0;
        $failed = 0;
        $systemsCreated = 0;

        DB::beginTransaction();

        try {
            // Traiter les planètes d'origine : créer un système pour chacune
            // Chaque joueur a son propre système (pas de partage entre joueurs)
            foreach ($homePlanets as $planet) {
                try {
                    // Créer un système complet avec plusieurs planètes pour cette planète d'origine
                    $system = $starSystemGenerator->generateSystem();

                    // Supprimer toutes les planètes générées
                    $system->planets->each->delete();

                    // Assigner la planète d'origine au système
                    $orbitalDistance = 10.0;
                    $orbitalAngle = 0;
                    $orbitalInclination = 0;

                    [$x, $y, $z] = $this->orbitalToAbsolute(
                        $system->x,
                        $system->y,
                        $system->z,
                        $orbitalDistance,
                        $orbitalAngle,
                        $orbitalInclination
                    );

                    $planet->update([
                        'star_system_id' => $system->id,
                        'x' => $x,
                        'y' => $y,
                        'z' => $z,
                        'orbital_distance' => $orbitalDistance,
                        'orbital_angle' => $orbitalAngle,
                        'orbital_inclination' => $orbitalInclination,
                    ]);

                    // Mettre à jour le compteur (seulement la planète d'origine pour l'instant)
                    $system->update(['planet_count' => 1]);

                    $migrated++;
                    $systemsCreated++;
                } catch (\Exception $e) {
                    $failed++;
                    $this->newLine();
                    $this->warn("Failed to migrate home planet {$planet->id} ({$planet->name}): {$e->getMessage()}");
                }
                $bar->advance();
            }

            // Traiter les autres planètes selon le mode choisi
            if ($isolate) {
                // Mode isolé : une planète = un système
                foreach ($otherPlanets as $planet) {
                    try {
                        $this->migratePlanetIsolated($planet, $starSystemGenerator);
                        $migrated++;
                        $systemsCreated++;
                    } catch (\Exception $e) {
                        $failed++;
                        $this->newLine();
                        $this->warn("Failed to migrate planet {$planet->id} ({$planet->name}): {$e->getMessage()}");
                    }
                    $bar->advance();
                }
            } elseif ($assignExisting) {
                // Mode assignation : assigner aux systèmes existants (y compris ceux des planètes d'origine)
                // ou créer si nécessaire
                foreach ($otherPlanets as $planet) {
                    try {
                        $this->migratePlanetToExistingOrNewSystem($planet, $starSystemGenerator);
                        $migrated++;
                    } catch (\Exception $e) {
                        $failed++;
                        $this->newLine();
                        $this->warn("Failed to migrate planet {$planet->id} ({$planet->name}): {$e->getMessage()}");
                    }
                    $bar->advance();
                }
            } else {
                // Mode par défaut : regrouper les planètes en systèmes
                // (mais pas dans les systèmes des planètes d'origine pour éviter le partage entre joueurs)
                $chunks = $otherPlanets->chunk($planetsPerSystem);

                foreach ($chunks as $chunk) {
                    try {
                        $this->createSystemForPlanets($chunk, $starSystemGenerator);
                        $systemsCreated++;
                        $migrated += $chunk->count();
                    } catch (\Exception $e) {
                        $failed += $chunk->count();
                        $this->newLine();
                        $this->warn("Failed to create system for chunk: {$e->getMessage()}");
                    }
                    $bar->advance($chunk->count());
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $bar->finish();
            $this->newLine(2);
            $this->error("Migration failed: {$e->getMessage()}");

            return Command::FAILURE;
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Migration complete!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Planets migrated', $migrated],
                ['Systems created', $systemsCreated],
                ['Failed', $failed],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Migrate a planet in isolated mode (one planet = one system).
     */
    private function migratePlanetIsolated(
        Planet $planet,
        StarSystemGeneratorService $starSystemGenerator
    ): void {
        // Générer un système stellaire avec une seule planète
        $system = $starSystemGenerator->generateSystem();

        // Supprimer la planète générée et utiliser celle existante
        $generatedPlanet = $system->planets->first();
        if ($generatedPlanet) {
            $generatedPlanet->delete();
        }

        // Calculer les coordonnées orbitales pour la planète existante
        $orbitalDistance = 10.0; // Distance par défaut
        $orbitalAngle = 0;
        $orbitalInclination = 0;

        // Convertir en coordonnées absolues
        [$x, $y, $z] = $this->orbitalToAbsolute(
            $system->x,
            $system->y,
            $system->z,
            $orbitalDistance,
            $orbitalAngle,
            $orbitalInclination
        );

        // Mettre à jour la planète
        $planet->update([
            'star_system_id' => $system->id,
            'x' => $x,
            'y' => $y,
            'z' => $z,
            'orbital_distance' => $orbitalDistance,
            'orbital_angle' => $orbitalAngle,
            'orbital_inclination' => $orbitalInclination,
        ]);

        // Mettre à jour le compteur du système
        $system->update(['planet_count' => 1]);
    }

    /**
     * Create a star system for a group of planets.
     */
    private function createSystemForPlanets(
        \Illuminate\Support\Collection $planets,
        StarSystemGeneratorService $starSystemGenerator
    ): void {
        // Générer un système stellaire
        $system = $starSystemGenerator->generateSystem();

        // Supprimer les planètes générées
        $system->planets->each->delete();

        // Assigner les planètes existantes au système
        $index = 0;
        foreach ($planets as $planet) {
            $orbitalDistance = $this->calculateOrbitalDistance($index, $planets->count());
            $orbitalAngle = ($index * 360) / $planets->count();
            $orbitalInclination = rand(-15, 15);

            [$x, $y, $z] = $this->orbitalToAbsolute(
                $system->x,
                $system->y,
                $system->z,
                $orbitalDistance,
                $orbitalAngle,
                $orbitalInclination
            );

            $planet->update([
                'star_system_id' => $system->id,
                'x' => $x,
                'y' => $y,
                'z' => $z,
                'orbital_distance' => $orbitalDistance,
                'orbital_angle' => $orbitalAngle,
                'orbital_inclination' => $orbitalInclination,
            ]);

            $index++;
        }

        // Mettre à jour le compteur
        $system->update(['planet_count' => $planets->count()]);
    }

    /**
     * Migrate a planet to an existing system or create a new one.
     */
    private function migratePlanetToExistingOrNewSystem(
        Planet $planet,
        StarSystemGeneratorService $starSystemGenerator
    ): void {
        // Chercher un système existant avec de la place
        $system = StarSystem::where('planet_count', '<', 7) // Max 7 planètes par système
            ->inRandomOrder()
            ->first();

        if (! $system) {
            // Créer un nouveau système
            $system = $starSystemGenerator->generateSystem();
            $system->planets->each->delete(); // Supprimer la planète générée
            $system->update(['planet_count' => 0]);
        }

        // Calculer la position orbitale
        $currentPlanetCount = $system->planets()->count();
        $orbitalDistance = $this->calculateOrbitalDistance($currentPlanetCount, $currentPlanetCount + 1);
        $orbitalAngle = ($currentPlanetCount * 360) / ($currentPlanetCount + 1);
        $orbitalInclination = rand(-15, 15);

        [$x, $y, $z] = $this->orbitalToAbsolute(
            $system->x,
            $system->y,
            $system->z,
            $orbitalDistance,
            $orbitalAngle,
            $orbitalInclination
        );

        // Mettre à jour la planète
        $planet->update([
            'star_system_id' => $system->id,
            'x' => $x,
            'y' => $y,
            'z' => $z,
            'orbital_distance' => $orbitalDistance,
            'orbital_angle' => $orbitalAngle,
            'orbital_inclination' => $orbitalInclination,
        ]);

        // Mettre à jour le compteur
        $system->increment('planet_count');
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
}

