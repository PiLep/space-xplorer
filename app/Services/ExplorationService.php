<?php

namespace App\Services;

use App\Models\Planet;
use App\Models\StarSystem;
use App\Models\User;

class ExplorationService
{
    public function __construct(
        private StarSystemGeneratorService $starSystemGenerator
    ) {}

    /**
     * Explore les systèmes stellaires proches d'une position.
     */
    public function exploreNearbySystems(User $user, float $x, float $y, float $z, float $radius = 200): \Illuminate\Database\Eloquent\Collection
    {
        // Trouver les systèmes existants dans le rayon
        $existingSystems = StarSystem::nearby($x, $y, $z, $radius);

        // Générer de nouveaux systèmes si nécessaire
        $systemsToGenerate = max(0, config('star-systems.generation.max_nearby_systems') - $existingSystems->count());

        for ($i = 0; $i < $systemsToGenerate; $i++) {
            $newSystem = $this->starSystemGenerator->generateNearbySystem($x, $y, $z);
            $existingSystems->push($newSystem);
        }

        return $existingSystems;
    }

    /**
     * Calcule le temps de voyage entre deux planètes.
     */
    public function calculateTravelTime(Planet $from, Planet $to): float
    {
        return $from->travelTimeTo($to);
    }
}

