<?php

namespace App\Console\Commands;

use App\Jobs\GenerateResourceJob;
use App\Models\Resource;
use App\Services\ResourceGenerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateDailyPlanetResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resources:generate-daily-planets 
                            {--count=20 : Number of planet image resources to generate per day}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily batch of planet image resources for admin approval (scheduled task)';

    /**
     * Execute the console command.
     */
    public function handle(ResourceGenerationService $resourceGenerator): int
    {
        $count = (int) $this->option('count');

        if ($count < 1 || $count > 50) {
            $this->error('Count must be between 1 and 50.');

            return Command::FAILURE;
        }

        $this->info("Generating {$count} planet image resources for daily batch...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $generated = 0;
        $failed = 0;

        // Generate varied prompts to cover different planet types and characteristics
        $prompts = $this->generateVariedPlanetPrompts($count);

        foreach ($prompts as $index => $prompt) {
            try {
                // Extract tags from prompt
                $extractedTags = $resourceGenerator->extractPlanetTagsFromPrompt($prompt);

                // Create resource with 'generating' status
                $resource = Resource::create([
                    'type' => 'planet_image',
                    'status' => 'generating',
                    'file_path' => null, // Will be set when generation completes
                    'prompt' => $prompt,
                    'tags' => $extractedTags,
                    'description' => "Daily auto-generated planet image #{$index} - Scheduled generation",
                    'metadata' => [
                        'auto_generated' => true,
                        'scheduled_generation' => true,
                        'generated_at' => now()->toIso8601String(),
                    ],
                    'created_by' => null, // System-generated
                ]);

                // Dispatch job for async generation
                GenerateResourceJob::dispatch($resource);

                $generated++;
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->warn("Failed to create resource #{$index}: {$e->getMessage()}");
                Log::error('Failed to create daily planet resource', [
                    'index' => $index,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Daily generation complete! Created: {$generated}, Failed: {$failed}");
        $this->info('Resources are being generated asynchronously and will be available for admin approval once complete.');

        Log::info('Daily planet resources generation completed', [
            'generated' => $generated,
            'failed' => $failed,
            'total_requested' => $count,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Generate varied planet prompts covering different types and characteristics.
     *
     * @param  int  $count  Number of prompts to generate
     * @return array<string> Array of planet image prompts
     */
    private function generateVariedPlanetPrompts(int $count): array
    {
        $prompts = [];
        $planetConfig = config('planets.types');

        // Generate prompts with variety based on actual planet characteristics
        for ($i = 0; $i < $count; $i++) {
            // Distribute prompts based on planet type weights
            $typeWeights = [
                'tellurique' => 40, // 40% probability
                'gazeuse' => 25,    // 25% probability
                'glacée' => 15,     // 15% probability
                'désertique' => 10, // 10% probability
                'océanique' => 10,  // 10% probability
            ];

            // Select type based on weights
            $selectedType = $this->weightedRandomSelect($typeWeights);
            $typeConfig = $planetConfig[$selectedType]['characteristics'];

            // Select random characteristics for this prompt
            $size = $this->weightedRandomSelect($typeConfig['size']);
            $temperature = $this->weightedRandomSelect($typeConfig['temperature']);
            $atmosphere = $this->weightedRandomSelect($typeConfig['atmosphere']);
            $terrain = $this->weightedRandomSelect($typeConfig['terrain']);

            // Generate prompt based on characteristics
            $prompt = $this->buildPlanetPrompt($selectedType, $size, $temperature, $atmosphere, $terrain);

            $prompts[] = $prompt;
        }

        return $prompts;
    }

    /**
     * Build a planet prompt from characteristics.
     */
    private function buildPlanetPrompt(string $type, string $size, string $temperature, string $atmosphere, string $terrain): string
    {
        $sizeDescriptions = [
            'petite' => 'small, compact planet',
            'moyenne' => 'medium-sized planet',
            'grande' => 'massive, imposing planet',
        ];

        $temperatureDescriptions = [
            'froide' => 'icy, frozen surface covered in ice and snow, crystalline formations, polar ice caps visible',
            'tempérée' => 'temperate climate with varied landscapes, green and blue tones, visible continents and oceans',
            'chaude' => 'scorching hot surface, reddish-orange tones, volcanic activity, heat distortion visible in atmosphere',
        ];

        $atmosphereDescriptions = [
            'respirable' => 'clear, breathable atmosphere with white cloud formations, blue atmospheric glow at the edges',
            'toxique' => 'toxic, swirling atmosphere with yellow, green, or purple colored clouds, chemical haze visible',
            'inexistante' => 'airless void, no atmospheric glow, stark shadows, cratered surface clearly visible',
        ];

        $terrainDescriptions = [
            'rocheux' => 'rugged, rocky terrain with sharp mountain ranges, deep canyons, gray and brown rocky surface',
            'océanique' => 'vast blue oceans covering most of the surface, minimal landmasses, white ocean foam visible',
            'désertique' => 'endless sand dunes creating flowing patterns, orange and beige tones, dry cracked surface, no vegetation',
            'forestier' => 'dense alien forests covering the surface, dark green patches, strange vegetation patterns',
            'urbain' => 'abandoned industrial structures and ruins, geometric patterns, metallic gray surfaces, urban sprawl',
            'mixte' => 'diverse, mixed terrain with multiple biomes creating a patchwork surface of different colors',
            'glacé' => 'frozen wasteland covered in ice and snow, white and blue tones, glaciers and ice sheets visible',
        ];

        $typeDescriptions = [
            'tellurique' => 'rocky terrestrial planet with solid surface',
            'gazeuse' => 'gas giant with swirling atmospheric bands in various colors',
            'glacée' => 'ice planet completely covered in frozen layers',
            'désertique' => 'barren desert planet with sand and rock',
            'océanique' => 'ocean world with water covering most of the surface',
        ];

        // Build color palette based on characteristics
        $colorPalette = 'deep blues, dark grays';
        if ($terrain === 'désertique' || $type === 'désertique') {
            $colorPalette = 'sandy beige, orange, reddish-brown, tan';
        } elseif ($temperature === 'chaude') {
            $colorPalette = 'reddish-orange, deep reds, dark browns';
        } elseif ($temperature === 'froide' || $terrain === 'glacé' || $type === 'glacée') {
            $colorPalette = 'icy whites, pale blues, silver';
        } elseif ($terrain === 'océanique' || $type === 'océanique') {
            $colorPalette = 'deep blues, turquoise, white';
        } elseif ($atmosphere === 'toxique') {
            $colorPalette = 'yellow-green, purple, toxic colors';
        }

        $sizeDesc = $sizeDescriptions[$size] ?? 'planet';
        $tempDesc = $temperatureDescriptions[$temperature] ?? '';
        $atmoDesc = $atmosphereDescriptions[$atmosphere] ?? '';
        $terrainDesc = $terrainDescriptions[$terrain] ?? '';
        $typeDesc = $typeDescriptions[$type] ?? 'alien planet';

        return "Cinematic space view of a {$sizeDesc} {$typeDesc}, viewed from space. "
            ."The planet's surface shows {$terrainDesc}. "
            ."Temperature characteristics: {$tempDesc}. "
            ."Atmosphere: {$atmoDesc}. "
            .'Wide-angle shot from space, showing the planet in full view against the dark void of space. '
            ."Color palette: {$colorPalette}, with dark, moody lighting in the style of Alien (1979). "
            .'Realistic sci-fi space environment with stars visible in the background. '
            .'Industrial, atmospheric aesthetic with cinematic quality. '
            .'The planet should clearly show its surface characteristics and atmospheric conditions. '
            .'Photorealistic style, high resolution, detailed surface texture, '
            .'16:9 aspect ratio, professional space photography aesthetic.';
    }

    /**
     * Select a random key based on weights.
     *
     * @param  array<string, int>  $weights  Array of keys => weights
     * @return string Selected key
     */
    private function weightedRandomSelect(array $weights): string
    {
        $totalWeight = array_sum($weights);
        $random = mt_rand(1, $totalWeight);
        $currentWeight = 0;

        foreach ($weights as $key => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $key;
            }
        }

        // Fallback to first key
        return array_key_first($weights);
    }
}
