<?php

namespace App\Services;

use App\Models\StarSystem;

class StarSystemGeneratorService
{
    /**
     * Star types with probabilities
     */
    private const STAR_TYPES = [
        'yellow_dwarf' => 0.35,      // Like the Sun
        'red_dwarf' => 0.40,         // Very common
        'orange_dwarf' => 0.15,      // K-type
        'red_giant' => 0.05,         // Evolved stars
        'blue_giant' => 0.03,        // Rare
        'white_dwarf' => 0.02,       // Very rare
    ];

    /**
     * Nombre de planètes par système (distribution)
     */
    private const PLANETS_PER_SYSTEM = [
        1 => 0.10,  // 10% des systèmes ont 1 planète
        2 => 0.15,  // 15% ont 2 planètes
        3 => 0.25,  // 25% ont 3 planètes
        4 => 0.25,  // 25% ont 4 planètes
        5 => 0.15,  // 15% ont 5 planètes
        6 => 0.08,  // 8% ont 6 planètes
        7 => 0.02,  // 2% ont 7+ planètes
    ];

    /**
     * Génère un système stellaire complet avec ses planètes.
     *
     * @param  float|null  $x  Position X (si null, génère aléatoirement)
     * @param  float|null  $y  Position Y (si null, génère aléatoirement)
     * @param  float|null  $z  Position Z (si null, génère aléatoirement)
     * @param  float  $minDistance  Distance minimale depuis l'origine (pour éviter les collisions)
     */
    public function generateSystem(?float $x = null, ?float $y = null, ?float $z = null, float $minDistance = 100): StarSystem
    {
        // Générer les coordonnées si non fournies
        if ($x === null || $y === null || $z === null) {
            [$x, $y, $z] = $this->generateRandomCoordinates($minDistance);
        }

        // Sélectionner un type d'étoile
        $starType = $this->selectStarType();

        // Générer le nom du système
        $name = $this->generateSystemName();

        // Créer le système stellaire
        $system = StarSystem::create([
            'name' => $name,
            'x' => $x,
            'y' => $y,
            'z' => $z,
            'star_type' => $starType,
            'discovered' => false,
            'planet_count' => 0,
        ]);

        // Générer les planètes du système
        $planetCount = $this->selectPlanetCount();
        $this->generatePlanetsForSystem($system, $planetCount);

        // Mettre à jour le compteur de planètes
        $system->update(['planet_count' => $planetCount]);

        return $system->fresh();
    }

    /**
     * Génère des coordonnées aléatoires dans l'espace.
     */
    private function generateRandomCoordinates(float $minDistance = 100): array
    {
        // Générer des coordonnées dans une sphère
        // Utiliser une distribution uniforme dans une sphère
        do {
            $x = (rand(-10000, 10000) / 100);
            $y = (rand(-10000, 10000) / 100);
            $z = (rand(-10000, 10000) / 100);
            $distance = sqrt($x * $x + $y * $y + $z * $z);
        } while ($distance < $minDistance);

        return [$x, $y, $z];
    }

    /**
     * Sélectionne un type d'étoile selon les probabilités.
     */
    private function selectStarType(): string
    {
        $rand = mt_rand() / mt_getrandmax();
        $cumulative = 0;

        foreach (self::STAR_TYPES as $type => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $type;
            }
        }

        return 'yellow_dwarf'; // Fallback
    }

    /**
     * Sélectionne le nombre de planètes selon la distribution.
     */
    private function selectPlanetCount(): int
    {
        $rand = mt_rand() / mt_getrandmax();
        $cumulative = 0;

        foreach (self::PLANETS_PER_SYSTEM as $count => $probability) {
            $cumulative += $probability;
            if ($rand <= $cumulative) {
                return $count;
            }
        }

        return 3; // Fallback
    }

    /**
     * Génère les planètes pour un système stellaire.
     */
    private function generatePlanetsForSystem(StarSystem $system, int $count): void
    {
        $planetGenerator = app(PlanetGeneratorService::class);

        for ($i = 0; $i < $count; $i++) {
            // Générer une planète avec le service existant
            $planet = $planetGenerator->generate();

            // Calculer les coordonnées orbitales
            $orbitalDistance = $this->calculateOrbitalDistance($i, $count);
            $orbitalAngle = ($i * 360) / $count + rand(-10, 10); // Répartir autour de l'étoile
            $orbitalInclination = rand(-15, 15); // Légère inclinaison

            // Convertir les coordonnées orbitales en coordonnées absolues
            [$x, $y, $z] = $this->orbitalToAbsolute(
                $system->x,
                $system->y,
                $system->z,
                $orbitalDistance,
                $orbitalAngle,
                $orbitalInclination
            );

            // Mettre à jour la planète avec les coordonnées
            $planet->update([
                'star_system_id' => $system->id,
                'x' => $x,
                'y' => $y,
                'z' => $z,
                'orbital_distance' => $orbitalDistance,
                'orbital_angle' => $orbitalAngle,
                'orbital_inclination' => $orbitalInclination,
            ]);
        }
    }

    /**
     * Calcule la distance orbitale d'une planète.
     * Les planètes plus proches de l'étoile ont des distances plus petites.
     */
    private function calculateOrbitalDistance(int $index, int $total): float
    {
        // Distance minimale et maximale (unités arbitraires)
        $minDistance = 5.0;
        $maxDistance = 50.0;

        // Répartir les planètes de manière réaliste
        // Les premières planètes sont plus proches
        $ratio = ($index + 1) / ($total + 1);
        $distance = $minDistance + ($maxDistance - $minDistance) * $ratio;

        // Ajouter un peu de variation aléatoire
        $variation = rand(-20, 20) / 100; // ±20%

        return $distance * (1 + $variation);
    }

    /**
     * Convertit les coordonnées orbitales en coordonnées absolues 3D.
     */
    private function orbitalToAbsolute(
        float $systemX,
        float $systemY,
        float $systemZ,
        float $orbitalDistance,
        float $orbitalAngle,
        float $orbitalInclination
    ): array {
        // Convertir l'angle en radians
        $angleRad = deg2rad($orbitalAngle);
        $inclinationRad = deg2rad($orbitalInclination);

        // Calculer la position dans le plan orbital
        $x = $orbitalDistance * cos($angleRad);
        $y = $orbitalDistance * sin($angleRad) * cos($inclinationRad);
        $z = $orbitalDistance * sin($angleRad) * sin($inclinationRad);

        // Ajouter la position du système
        return [
            $systemX + $x,
            $systemY + $y,
            $systemZ + $z,
        ];
    }

    /**
     * Génère un nom pour le système stellaire.
     */
    private function generateSystemName(): string
    {
        $prefixes = ['Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon', 'Zeta', 'Eta', 'Theta'];
        $suffixes = ['Centauri', 'Orionis', 'Lyrae', 'Draconis', 'Cygni', 'Aquilae', 'Pegasi', 'Andromedae'];

        $prefix = $prefixes[array_rand($prefixes)];
        $suffix = $suffixes[array_rand($suffixes)];
        $number = rand(1, 999);

        return "{$prefix} {$suffix}-{$number}";
    }

    /**
     * Génère un système stellaire proche d'une position donnée (pour l'exploration).
     */
    public function generateNearbySystem(float $x, float $y, float $z, float $minDistance = 50, float $maxDistance = 200): StarSystem
    {
        // Générer une direction aléatoire
        $angle1 = deg2rad(rand(0, 360));
        $angle2 = deg2rad(rand(-90, 90));
        $distance = rand($minDistance * 100, $maxDistance * 100) / 100;

        // Calculer la nouvelle position
        $newX = $x + $distance * cos($angle1) * cos($angle2);
        $newY = $y + $distance * sin($angle1) * cos($angle2);
        $newZ = $z + $distance * sin($angle2);

        return $this->generateSystem($newX, $newY, $newZ, 0);
    }
}

