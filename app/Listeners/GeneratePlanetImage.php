<?php

namespace App\Listeners;

use App\Events\PlanetCreated;
use App\Services\ImageGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class GeneratePlanetImage implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 30;

    /**
     * Create the event listener.
     */
    public function __construct(
        private ImageGenerationService $imageGenerator
    ) {
        //
    }

    /**
     * Handle the event.
     *
     * Generates an image for the newly created planet.
     * This runs asynchronously in a queue job, so it doesn't block planet creation.
     * If generation fails, logs the error but does not block planet creation.
     */
    public function handle(PlanetCreated $event): void
    {
        try {
            // Reload planet to ensure we have the latest data
            $planet = $event->planet->fresh();

            // Prevent duplicate image generation - if planet already has an image, skip
            $existingImage = $planet->getAttributes()['image_url'] ?? null;
            if ($existingImage) {
                return;
            }

            // Generate planet image prompt in Alien style
            $prompt = $this->generatePlanetPrompt($planet);

            // Generate planet image in 'planets' subfolder
            $result = $this->imageGenerator->generate($prompt, null, 'planets');

            // Store the path instead of full URL for flexibility
            // The URL will be reconstructed dynamically via the model accessor
            $planet->update(['image_url' => $result['path']]);

            Log::info('Planet image generated successfully', [
                'planet_id' => $planet->id,
                'planet_name' => $planet->name,
                'image_path' => $result['path'],
                'image_url' => $result['url'], // Full URL for logging, but path is stored
            ]);
        } catch (\Exception $e) {
            // Log the error but don't block planet creation
            Log::error('Failed to generate planet image', [
                'planet_id' => $event->planet->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to mark job as failed (will be retried according to queue config)
            throw $e;
        }
    }

    /**
     * Generate a planet image prompt in Alien style.
     *
     * Creates a cinematic, atmospheric prompt that captures the essence of
     * the planet's characteristics in the style of Alien (1979) - dark,
     * industrial, realistic sci-fi aesthetic.
     *
     * @param  \App\Models\Planet  $planet  The planet to generate an image for
     * @return string The prompt for image generation
     */
    private function generatePlanetPrompt(\App\Models\Planet $planet): string
    {
        // Map planet characteristics to visual descriptions
        $sizeDescriptions = [
            'petite' => 'small, compact planet',
            'moyenne' => 'medium-sized planet',
            'grande' => 'massive, imposing planet',
        ];

        $temperatureDescriptions = [
            'froide' => 'icy, frozen surface with crystalline formations',
            'tempérée' => 'temperate climate with varied landscapes',
            'chaude' => 'scorching surface with volcanic activity and heat distortion',
        ];

        $atmosphereDescriptions = [
            'respirable' => 'clear, breathable atmosphere with visible cloud formations',
            'toxique' => 'toxic, swirling atmosphere with ominous colored clouds',
            'inexistante' => 'airless void, stark and desolate',
        ];

        $terrainDescriptions = [
            'rocheux' => 'rugged, rocky terrain with sharp formations',
            'océanique' => 'vast oceans with turbulent waves',
            'désertique' => 'endless desert dunes under harsh light',
            'forestier' => 'alien forests with strange, twisted vegetation',
            'urbain' => 'abandoned industrial structures and ruins',
            'mixte' => 'diverse, mixed terrain with multiple biomes',
            'glacé' => 'frozen wasteland with ice formations',
        ];

        $typeDescriptions = [
            'tellurique' => 'rocky terrestrial planet',
            'gazeuse' => 'gas giant with swirling atmospheric bands',
            'glacée' => 'ice planet covered in frozen layers',
            'désertique' => 'barren desert planet',
            'océanique' => 'ocean world with minimal landmass',
        ];

        // Build the visual description based on planet characteristics
        $sizeDesc = $sizeDescriptions[$planet->size] ?? 'planet';
        $tempDesc = $temperatureDescriptions[$planet->temperature] ?? '';
        $atmoDesc = $atmosphereDescriptions[$planet->atmosphere] ?? '';
        $terrainDesc = $terrainDescriptions[$planet->terrain] ?? '';
        $typeDesc = $typeDescriptions[$planet->type] ?? 'alien planet';

        // Create a prompt that generates a cinematic planet image
        // in the style of Alien (1979) - dark, atmospheric, realistic sci-fi
        return "Cinematic space view of {$planet->name}, a {$sizeDesc} and {$typeDesc}, "
            .'in the style of Alien (1979) movie aesthetic. '
            .'Wide-angle shot from space, showing the planet in full view against the void of space. '
            ."Planet surface details: {$tempDesc}, {$terrainDesc}. "
            ."Atmospheric conditions: {$atmoDesc}. "
            .'Dark, moody lighting with dramatic shadows and highlights. '
            .'Industrial sci-fi color palette: deep blues, dark grays, muted oranges, '
            .'and subtle greens creating an ominous, atmospheric feel. '
            .'Realistic space environment with stars visible in the background, '
            .'subtle nebula clouds, and cosmic dust particles catching light. '
            .'The planet should feel mysterious, potentially dangerous, and awe-inspiring. '
            .'Cinematic composition with the planet dominating the frame, '
            .'surrounded by the vast emptiness of space. '
            .'High detail on surface features, atmospheric layers, and lighting effects. '
            .'Photorealistic style with sharp focus on the planet, '
            .'moody and atmospheric, cinematic quality, high resolution, '
            .'detailed surface texture, realistic shadows and highlights, '
            .'16:9 aspect ratio, professional space photography aesthetic.';
    }
}
