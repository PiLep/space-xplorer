<?php

namespace App\Console\Commands;

use App\Services\ResourceGenerationService;
use Illuminate\Console\Command;

class GenerateResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resources:generate 
                            {--type= : Resource type (avatar_image, planet_image, planet_video)}
                            {--count=5 : Number of resources to generate}
                            {--tags= : Comma-separated tags}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a batch of resource templates automatically';

    /**
     * Execute the console command.
     */
    public function handle(ResourceGenerationService $resourceGenerator): int
    {
        $type = $this->option('type');
        $count = (int) $this->option('count');
        $tagsInput = $this->option('tags');
        $tags = $tagsInput ? array_map('trim', explode(',', $tagsInput)) : null;

        if ($count < 1 || $count > 50) {
            $this->error('Count must be between 1 and 50.');

            return Command::FAILURE;
        }

        $types = ['avatar_image', 'planet_image', 'planet_video'];

        if ($type && ! in_array($type, $types)) {
            $this->error('Invalid type. Must be one of: '.implode(', ', $types));

            return Command::FAILURE;
        }

        // If no type specified, generate all types
        $typesToGenerate = $type ? [$type] : $types;

        $this->info("Generating {$count} resource(s) of type(s): ".implode(', ', $typesToGenerate));

        $bar = $this->output->createProgressBar($count * count($typesToGenerate));
        $bar->start();

        $generated = 0;
        $failed = 0;

        foreach ($typesToGenerate as $resourceType) {
            for ($i = 0; $i < $count; $i++) {
                try {
                    $prompt = $this->generatePrompt($resourceType, $i);

                    match ($resourceType) {
                        'avatar_image' => $resourceGenerator->generateAvatarTemplate(
                            $prompt,
                            $tags,
                            "Auto-generated avatar template #{$i}",
                            null
                        ),
                        'planet_image' => $resourceGenerator->generatePlanetImageTemplate(
                            $prompt,
                            $tags,
                            "Auto-generated planet image template #{$i}",
                            null
                        ),
                        'planet_video' => $resourceGenerator->generatePlanetVideoTemplate(
                            $prompt,
                            $tags,
                            "Auto-generated planet video template #{$i}",
                            null
                        ),
                    };

                    $generated++;
                } catch (\Exception $e) {
                    $failed++;
                    $this->newLine();
                    $this->warn("Failed to generate {$resourceType} #{$i}: {$e->getMessage()}");
                }

                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Generation complete! Generated: {$generated}, Failed: {$failed}");

        return Command::SUCCESS;
    }

    /**
     * Generate a prompt based on resource type and index.
     */
    private function generatePrompt(string $type, int $index): string
    {
        return match ($type) {
            'avatar_image' => $this->generateAvatarPrompt($index),
            'planet_image' => $this->generatePlanetImagePrompt($index),
            'planet_video' => $this->generatePlanetVideoPrompt($index),
        };
    }

    /**
     * Generate avatar prompts with variety.
     */
    private function generateAvatarPrompt(int $index): string
    {
        $styles = [
            'Close-up professional portrait headshot of a space technician, weathered and experienced, in the style of Alien (1979) movie aesthetic. Industrial sci-fi setting, realistic lighting, cinematic composition.',
            'Professional headshot of a ship captain, determined expression, wearing a technical jumpsuit with patches and insignia. Atmospheric lighting with blue and orange tones, photorealistic style.',
            'Portrait of a space explorer, focused eyes, weathered skin with subtle scars, professional haircut. Dark, muted background, highly detailed facial features, moody and atmospheric.',
            'Close-up of a space engineer, holding a data pad, wearing a weathered jumpsuit. Industrial setting, realistic shadows and highlights, square format (1:1 aspect ratio).',
            'Professional portrait of a space pilot, determined expression, technical uniform visible. Cinematic quality, high resolution, detailed skin texture, atmospheric lighting.',
        ];

        $style = $styles[$index % count($styles)];

        return $style.' Single person only, no other people in frame, square format (1:1 aspect ratio), highly detailed, photorealistic style with sharp focus on the face.';
    }

    /**
     * Generate planet image prompts with variety.
     */
    private function generatePlanetImagePrompt(int $index): string
    {
        $planetTypes = [
            'A massive gas giant planet with swirling clouds of orange and red, visible from space, detailed atmospheric bands, dramatic lighting, cinematic composition.',
            'A rocky terrestrial planet with craters and mountains, seen from orbit, Earth-like but alien, detailed surface features, realistic space environment.',
            'An ice-covered planet with frozen oceans and glaciers, blue and white tones, seen from space, detailed surface textures, atmospheric perspective.',
            'A desert planet with vast sand dunes and rocky formations, warm orange and yellow tones, seen from orbit, detailed terrain, dramatic shadows.',
            'An ocean planet with deep blue waters and scattered islands, seen from space, realistic water reflections, detailed cloud formations, atmospheric lighting.',
        ];

        $base = $planetTypes[$index % count($planetTypes)];

        return $base.' High resolution, photorealistic style, detailed surface features, realistic space environment, cinematic quality, wide format (16:9 aspect ratio).';
    }

    /**
     * Generate planet video prompts with variety.
     */
    private function generatePlanetVideoPrompt(int $index): string
    {
        $videoTypes = [
            'Slow cinematic pan around a gas giant planet with swirling clouds, atmospheric bands moving, dramatic lighting, space environment, 10 seconds, smooth camera movement.',
            'Orbital view of a rocky planet rotating slowly, surface details visible, realistic space environment, dramatic lighting, 10 seconds, cinematic composition.',
            'Slow zoom into an ice-covered planet, frozen surface details becoming visible, blue and white tones, space environment, 10 seconds, atmospheric perspective.',
            'Pan across a desert planet surface, sand dunes and rocky formations, warm tones, dramatic shadows moving, 10 seconds, cinematic quality.',
            'Slow rotation of an ocean planet, water reflections and cloud formations moving, deep blue tones, realistic space environment, 10 seconds, atmospheric lighting.',
        ];

        return $videoTypes[$index % count($videoTypes)].' High resolution, photorealistic style, smooth motion, cinematic quality.';
    }
}
