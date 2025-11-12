<?php

namespace App\Services;

use App\Events\PlanetCreated;
use App\Models\Planet;
use App\Models\PlanetProperty;

/**
 * Service for procedural planet generation.
 *
 * This service generates planets with weighted probability distributions
 * based on planet types and their characteristic distributions.
 */
class PlanetGeneratorService
{
    /**
     * Maximum number of attempts to generate a unique planet name.
     */
    private const MAX_NAME_ATTEMPTS = 10;

    /**
     * Mapping of French planet types to English planet types.
     *
     * @var array<string, string>
     */
    private const TYPE_TRANSLATIONS = [
        'tellurique' => 'terrestrial',
        'gazeuse' => 'gaseous',
        'glacée' => 'icy',
        'désertique' => 'desert',
        'océanique' => 'oceanic',
    ];

    /**
     * Mapping of French sizes to English sizes.
     *
     * @var array<string, string>
     */
    private const SIZE_TRANSLATIONS = [
        'petite' => 'small',
        'moyenne' => 'medium',
        'grande' => 'large',
    ];

    /**
     * Mapping of French temperatures to English temperatures.
     *
     * @var array<string, string>
     */
    private const TEMPERATURE_TRANSLATIONS = [
        'froide' => 'cold',
        'tempérée' => 'temperate',
        'chaude' => 'hot',
    ];

    /**
     * Mapping of French atmospheres to English atmospheres.
     *
     * @var array<string, string>
     */
    private const ATMOSPHERE_TRANSLATIONS = [
        'respirable' => 'breathable',
        'toxique' => 'toxic',
        'inexistante' => 'nonexistent',
    ];

    /**
     * Mapping of French terrains to English terrains.
     *
     * @var array<string, string>
     */
    private const TERRAIN_TRANSLATIONS = [
        'rocheux' => 'rocky',
        'océanique' => 'oceanic',
        'désertique' => 'desert',
        'forestier' => 'forested',
        'urbain' => 'urban',
        'mixte' => 'mixed',
        'glacé' => 'icy',
    ];

    /**
     * Mapping of French resources to English resources.
     *
     * @var array<string, string>
     */
    private const RESOURCES_TRANSLATIONS = [
        'abondantes' => 'abundant',
        'modérées' => 'moderate',
        'rares' => 'rare',
    ];

    /**
     * Generate a complete planet with all characteristics.
     */
    public function generate(): Planet
    {
        $type = $this->selectPlanetType();
        $characteristics = $this->generateCharacteristics($type);
        $name = $this->generateName();
        $description = $this->generateDescription($type, $characteristics);

        // Create planet (with old columns for backward compatibility)
        $planet = Planet::create([
            'name' => $name,
            // Old columns (to be removed after data migration)
            'type' => $type,
            'size' => $characteristics['size'],
            'temperature' => $characteristics['temperature'],
            'atmosphere' => $characteristics['atmosphere'],
            'terrain' => $characteristics['terrain'],
            'resources' => $characteristics['resources'],
            'description' => $description,
        ]);

        // Translate French values to English and create properties
        $typeEn = self::TYPE_TRANSLATIONS[$type] ?? $type;
        $sizeEn = self::SIZE_TRANSLATIONS[$characteristics['size']] ?? $characteristics['size'];
        $temperatureEn = self::TEMPERATURE_TRANSLATIONS[$characteristics['temperature']] ?? $characteristics['temperature'];
        $atmosphereEn = self::ATMOSPHERE_TRANSLATIONS[$characteristics['atmosphere']] ?? $characteristics['atmosphere'];
        $terrainEn = self::TERRAIN_TRANSLATIONS[$characteristics['terrain']] ?? $characteristics['terrain'];
        $resourcesEn = self::RESOURCES_TRANSLATIONS[$characteristics['resources']] ?? $characteristics['resources'];
        $descriptionEn = $this->translateDescription($description);

        // Create planet properties in English
        PlanetProperty::create([
            'planet_id' => $planet->id,
            'type' => $typeEn,
            'size' => $sizeEn,
            'temperature' => $temperatureEn,
            'atmosphere' => $atmosphereEn,
            'terrain' => $terrainEn,
            'resources' => $resourcesEn,
            'description' => $descriptionEn,
        ]);

        // Dispatch event to generate planet image
        event(new PlanetCreated($planet));

        return $planet;
    }

    /**
     * Translate a French description to English.
     */
    private function translateDescription(string $description): string
    {
        $translations = [
            // Type descriptions
            'planète tellurique' => 'terrestrial planet',
            'planète géante gazeuse' => 'gas giant planet',
            'planète glacée' => 'icy planet',
            'planète désertique' => 'desert planet',
            'planète océanique' => 'oceanic planet',
            'Cette planète tellurique' => 'This terrestrial planet',
            'Cette planète géante gazeuse' => 'This gas giant planet',
            'Cette planète glacée' => 'This icy planet',
            'Cette planète désertique' => 'This desert planet',
            'Cette planète océanique' => 'This oceanic planet',

            // Size descriptions
            'de petite taille' => 'small in size',
            'de taille moyenne' => 'medium-sized',
            'de grande taille' => 'large in size',

            // Temperature descriptions
            'avec un climat froid' => 'with a cold climate',
            'avec un climat tempéré' => 'with a temperate climate',
            'avec un climat chaud' => 'with a hot climate',

            // Atmosphere descriptions
            'possédant une atmosphère respirable' => 'with a breathable atmosphere',
            'possédant une atmosphère toxique' => 'with a toxic atmosphere',
            'possédant aucune atmosphère' => 'with no atmosphere',

            // Terrain descriptions
            'un terrain rocheux' => 'rocky terrain',
            'un terrain océanique' => 'oceanic terrain',
            'un terrain désertique' => 'desert terrain',
            'un terrain forestier' => 'forested terrain',
            'un terrain urbain' => 'urban terrain',
            'un terrain mixte' => 'mixed terrain',
            'un terrain glacé' => 'icy terrain',

            // Resources descriptions
            'et des ressources abondantes' => 'and abundant resources',
            'et des ressources modérées' => 'and moderate resources',
            'et des ressources rares' => 'and rare resources',
        ];

        $translated = $description;
        foreach ($translations as $french => $english) {
            $translated = str_ireplace($french, $english, $translated);
        }

        return $translated;
    }

    /**
     * Select a planet type based on weighted probability.
     *
     * @return string The selected planet type
     */
    public function selectPlanetType(): string
    {
        $types = config('planets.types');
        $weights = [];
        $typeNames = [];

        foreach ($types as $typeName => $typeConfig) {
            $weights[] = $typeConfig['weight'];
            $typeNames[] = $typeName;
        }

        $selectedIndex = $this->weightedRandom($weights);
        $selectedType = $typeNames[$selectedIndex];

        return $selectedType;
    }

    /**
     * Generate characteristics for a planet based on its type.
     *
     * @param  string  $type  The planet type
     * @return array Array of characteristics (size, temperature, atmosphere, terrain, resources)
     */
    public function generateCharacteristics(string $type): array
    {
        $types = config('planets.types');
        $typeConfig = $types[$type]['characteristics'];

        return [
            'size' => $this->selectWeightedValue($typeConfig['size']),
            'temperature' => $this->selectWeightedValue($typeConfig['temperature']),
            'atmosphere' => $this->selectWeightedValue($typeConfig['atmosphere']),
            'terrain' => $this->selectWeightedValue($typeConfig['terrain']),
            'resources' => $this->selectWeightedValue($typeConfig['resources']),
        ];
    }

    /**
     * Generate a unique planet name.
     *
     * @return string A unique planet name
     */
    public function generateName(): string
    {
        $prefixes = config('planets.name_prefixes');
        $suffixes = config('planets.name_suffixes');

        for ($attempt = 0; $attempt < self::MAX_NAME_ATTEMPTS; $attempt++) {
            $prefix = $prefixes[array_rand($prefixes)];
            $suffix = $suffixes[array_rand($suffixes)];
            $name = $prefix.'-'.rand(100, 999).$suffix;

            // Check if name already exists
            if (! Planet::where('name', $name)->exists()) {
                return $name;
            }

            // If name exists, try with a different suffix or add a unique identifier
            if ($attempt === self::MAX_NAME_ATTEMPTS - 1) {
                // Last attempt: add a unique identifier to ensure uniqueness
                $name = $prefix.'-'.rand(100, 999).$suffix.'-'.uniqid();
            }
        }

        return $name;
    }

    /**
     * Generate a description for a planet based on its characteristics.
     *
     * @param  string  $type  The planet type
     * @param  array  $characteristics  The planet characteristics
     * @return string A descriptive text about the planet
     */
    public function generateDescription(string $type, array $characteristics): string
    {
        $typeDescriptions = [
            'tellurique' => 'Cette planète tellurique présente des caractéristiques similaires à la Terre',
            'gazeuse' => 'Cette planète géante gazeuse est composée principalement d\'hydrogène et d\'hélium',
            'glacée' => 'Cette planète glacée est recouverte de glace et de neige',
            'désertique' => 'Cette planète désertique est aride et inhospitalière',
            'océanique' => 'Cette planète océanique est principalement recouverte d\'eau',
        ];

        $sizeDescriptions = [
            'petite' => 'de petite taille',
            'moyenne' => 'de taille moyenne',
            'grande' => 'de grande taille',
        ];

        $temperatureDescriptions = [
            'froide' => 'avec un climat froid',
            'tempérée' => 'avec un climat tempéré',
            'chaude' => 'avec un climat chaud',
        ];

        $atmosphereDescriptions = [
            'respirable' => 'une atmosphère respirable',
            'toxique' => 'une atmosphère toxique',
            'inexistante' => 'aucune atmosphère',
        ];

        $terrainDescriptions = [
            'rocheux' => 'un terrain rocheux',
            'océanique' => 'un terrain océanique',
            'désertique' => 'un terrain désertique',
            'forestier' => 'un terrain forestier',
            'urbain' => 'un terrain urbain',
            'mixte' => 'un terrain mixte',
            'glacé' => 'un terrain glacé',
        ];

        $resourcesDescriptions = [
            'abondantes' => 'des ressources abondantes',
            'modérées' => 'des ressources modérées',
            'rares' => 'des ressources rares',
        ];

        $description = $typeDescriptions[$type].', ';
        $description .= $sizeDescriptions[$characteristics['size']].', ';
        $description .= $temperatureDescriptions[$characteristics['temperature']].', ';
        $description .= 'possédant '.$atmosphereDescriptions[$characteristics['atmosphere']].', ';
        $description .= $terrainDescriptions[$characteristics['terrain']].', ';
        $description .= 'et '.$resourcesDescriptions[$characteristics['resources']].'.';

        return $description;
    }

    /**
     * Select a value from a weighted array.
     *
     * @param  array  $weights  Array of values => weights
     * @return string The selected value
     */
    private function selectWeightedValue(array $weights): string
    {
        $values = array_keys($weights);
        $weightValues = array_values($weights);
        $selectedIndex = $this->weightedRandom($weightValues);

        return $values[$selectedIndex];
    }

    /**
     * Select a random index based on weighted probability.
     *
     * @param  array  $weights  Array of weights
     * @return int The selected index
     */
    private function weightedRandom(array $weights): int
    {
        $totalWeight = array_sum($weights);
        $random = mt_rand(1, $totalWeight);
        $currentWeight = 0;

        foreach ($weights as $index => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $index;
            }
        }

        // Fallback: return last index
        return count($weights) - 1;
    }
}
