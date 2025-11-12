<?php

namespace App\Console\Commands;

use App\Models\Planet;
use App\Models\PlanetProperty;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TranslatePlanets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'planets:translate
                            {--dry-run : Show what would be translated without making changes}
                            {--force : Force translation even if translations already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create planet properties in English from French planet data';

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
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('Creating planet properties in English from French planet data...');
        $this->newLine();

        // Find all planets that have French data but no English properties yet
        $query = Planet::query()->whereNotNull('type');

        if (! $force) {
            // Only process planets that don't have properties yet
            $query->whereDoesntHave('properties');
        }

        $planetsToProcess = $query->get();
        $totalPlanets = $planetsToProcess->count();

        if ($totalPlanets === 0) {
            $this->info('No planets found that need properties creation.');

            return Command::SUCCESS;
        }

        $this->info("Found {$totalPlanets} planet(s) to process.");
        $this->newLine();

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made.');
            $this->newLine();
        }

        $bar = $this->output->createProgressBar($totalPlanets);
        $bar->start();

        $created = 0;
        $skipped = 0;
        $failed = 0;

        DB::beginTransaction();

        try {
            foreach ($planetsToProcess as $planet) {
                // Skip if planet already has properties and we're not forcing
                if (! $force && $planet->properties) {
                    $skipped++;
                    $bar->advance();

                    continue;
                }

                try {
                    // Get raw values from database columns (not from accessors that read from PlanetProperty)
                    $typeFr = trim($planet->getRawOriginal('type') ?? '');
                    $sizeFr = trim($planet->getRawOriginal('size') ?? '');
                    $temperatureFr = trim($planet->getRawOriginal('temperature') ?? '');
                    $atmosphereFr = trim($planet->getRawOriginal('atmosphere') ?? '');
                    $terrainFr = trim($planet->getRawOriginal('terrain') ?? '');
                    $resourcesFr = trim($planet->getRawOriginal('resources') ?? '');
                    $descriptionFr = $planet->getRawOriginal('description');

                    // Check if required fields are null or empty
                    $missingFields = [];
                    if (empty($typeFr)) {
                        $missingFields[] = 'type';
                    }
                    if (empty($sizeFr)) {
                        $missingFields[] = 'size';
                    }
                    if (empty($temperatureFr)) {
                        $missingFields[] = 'temperature';
                    }
                    if (empty($atmosphereFr)) {
                        $missingFields[] = 'atmosphere';
                    }
                    if (empty($terrainFr)) {
                        $missingFields[] = 'terrain';
                    }
                    if (empty($resourcesFr)) {
                        $missingFields[] = 'resources';
                    }

                    if (! empty($missingFields)) {
                        $failed++;
                        $this->newLine();
                        $this->warn("Skipping planet {$planet->id} ({$planet->name}): Missing required fields: ".implode(', ', $missingFields));
                        $bar->advance();

                        continue;
                    }

                    // Translate French values to English (or keep if already in English)
                    $typeEn = self::TYPE_TRANSLATIONS[$typeFr] ?? $typeFr;
                    $sizeEn = self::SIZE_TRANSLATIONS[$sizeFr] ?? $sizeFr;
                    $temperatureEn = self::TEMPERATURE_TRANSLATIONS[$temperatureFr] ?? $temperatureFr;
                    $atmosphereEn = self::ATMOSPHERE_TRANSLATIONS[$atmosphereFr] ?? $atmosphereFr;
                    $terrainEn = self::TERRAIN_TRANSLATIONS[$terrainFr] ?? $terrainFr;
                    $resourcesEn = self::RESOURCES_TRANSLATIONS[$resourcesFr] ?? $resourcesFr;
                    $descriptionEn = $descriptionFr ? $this->translateDescription($descriptionFr) : null;

                    // Normalize translated values
                    $typeEn = trim($typeEn);
                    $sizeEn = trim($sizeEn);
                    $temperatureEn = trim($temperatureEn);
                    $atmosphereEn = trim($atmosphereEn);
                    $terrainEn = trim($terrainEn);
                    $resourcesEn = trim($resourcesEn);

                    // Validate that translated values are not null or empty
                    $invalidFields = [];
                    if (empty($typeEn)) {
                        $invalidFields[] = "type (original: '{$typeFr}')";
                    }
                    if (empty($sizeEn)) {
                        $invalidFields[] = "size (original: '{$sizeFr}')";
                    }
                    if (empty($temperatureEn)) {
                        $invalidFields[] = "temperature (original: '{$temperatureFr}')";
                    }
                    if (empty($atmosphereEn)) {
                        $invalidFields[] = "atmosphere (original: '{$atmosphereFr}')";
                    }
                    if (empty($terrainEn)) {
                        $invalidFields[] = "terrain (original: '{$terrainFr}')";
                    }
                    if (empty($resourcesEn)) {
                        $invalidFields[] = "resources (original: '{$resourcesFr}')";
                    }

                    if (! empty($invalidFields)) {
                        $failed++;
                        $this->newLine();
                        $this->warn("Skipping planet {$planet->id} ({$planet->name}): Invalid or untranslatable values: ".implode(', ', $invalidFields));
                        $bar->advance();

                        continue;
                    }

                    if (! $dryRun) {
                        // Create or update properties
                        try {
                            PlanetProperty::updateOrCreate(
                                ['planet_id' => $planet->id],
                                [
                                    'type' => $typeEn,
                                    'size' => $sizeEn,
                                    'temperature' => $temperatureEn,
                                    'atmosphere' => $atmosphereEn,
                                    'terrain' => $terrainEn,
                                    'resources' => $resourcesEn,
                                    'description' => $descriptionEn,
                                ]
                            );
                        } catch (\Exception $e) {
                            $failed++;
                            $this->newLine();
                            $this->error("Failed to create properties for planet {$planet->id} ({$planet->name}): {$e->getMessage()}");
                            $this->line("  Original values: type='{$typeFr}', size='{$sizeFr}', temperature='{$temperatureFr}', atmosphere='{$atmosphereFr}', terrain='{$terrainFr}', resources='{$resourcesFr}'");
                            $this->line("  Translated values: type='{$typeEn}', size='{$sizeEn}', temperature='{$temperatureEn}', atmosphere='{$atmosphereEn}', terrain='{$terrainEn}', resources='{$resourcesEn}'");
                            $bar->advance();

                            continue;
                        }
                    }

                    $created++;
                } catch (\Exception $e) {
                    $failed++;
                    $this->newLine();
                    $this->error("Unexpected error processing planet {$planet->id} ({$planet->name}): {$e->getMessage()}");
                    $this->line("  Stack trace: {$e->getTraceAsString()}");
                }

                $bar->advance();
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $bar->finish();
            $this->newLine(2);
            $this->error("Translation failed: {$e->getMessage()}");

            return Command::FAILURE;
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info('DRY RUN - No changes were made.');
            $this->newLine();
        } else {
            $this->info('Translation complete!');
            $this->newLine();
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Properties created', $created],
                ['Planets skipped', $skipped],
                ['Failed', $failed],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Translate a French description to English.
     * This is a simple translation that replaces French keywords with English equivalents.
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
}
