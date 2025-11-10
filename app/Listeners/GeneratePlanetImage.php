<?php

namespace App\Listeners;

use App\Events\PlanetCreated;
use App\Exceptions\ImageGenerationException;
use App\Exceptions\StorageException;
use App\Services\ImageGenerationService;
use Aws\S3\Exception\S3Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeneratePlanetImage implements ShouldQueue, ShouldQueueAfterCommit
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
                // Reset generating status if image already exists
                $planet->update(['image_generating' => false]);

                return;
            }

            // Mark as generating before starting
            $planet->update(['image_generating' => true]);

            // Generate planet image prompt in Alien style
            $prompt = $this->generatePlanetPrompt($planet);

            // Generate planet image in 'planets' subfolder
            $result = $this->imageGenerator->generate($prompt, null, 'planets');

            // Store the path instead of full URL for flexibility
            // The URL will be reconstructed dynamically via the model accessor
            $planet->update([
                'image_url' => $result['path'],
                'image_generating' => false,
            ]);

            Log::info('Planet image generated successfully', [
                'planet_id' => $planet->id,
                'planet_name' => $planet->name,
                'image_path' => $result['path'],
                'image_url' => $result['url'], // Full URL for logging, but path is stored
            ]);
        } catch (S3Exception $s3Exception) {
            // Critical: Always reset generating status, even on S3 errors
            $this->resetGeneratingStatus($event->planet->id, 'image');

            // Log detailed S3 error information
            Log::error('S3 error during planet image generation', [
                'planet_id' => $event->planet->id,
                's3_error_code' => $s3Exception->getAwsErrorCode(),
                's3_error_message' => $s3Exception->getAwsErrorMessage(),
                's3_request_id' => $s3Exception->getAwsRequestId(),
                'http_status' => $s3Exception->getStatusCode(),
                'error' => $s3Exception->getMessage(),
            ]);

            // Re-throw to mark job as failed (will be retried according to queue config)
            throw new StorageException(
                "S3 error during image generation: {$s3Exception->getAwsErrorMessage()} (Code: {$s3Exception->getAwsErrorCode()})",
                0,
                $s3Exception
            );
        } catch (ImageGenerationException|StorageException $e) {
            // Critical: Always reset generating status on any error
            $this->resetGeneratingStatus($event->planet->id, 'image');

            // Log the error but don't block planet creation
            Log::error('Failed to generate planet image', [
                'planet_id' => $event->planet->id,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw custom exceptions as-is
            throw $e;
        } catch (\Exception $e) {
            // Critical: Always reset generating status on any error
            $this->resetGeneratingStatus($event->planet->id, 'image');

            // Log the error but don't block planet creation
            Log::error('Failed to generate planet image', [
                'planet_id' => $event->planet->id,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            // Wrap unknown exceptions in ImageGenerationException
            throw new ImageGenerationException("Failed to generate planet image: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(PlanetCreated $event, Throwable $exception): void
    {
        // Critical: Always reset generating status when job fails permanently
        $this->resetGeneratingStatus($event->planet->id, 'image');

        $logContext = [
            'planet_id' => $event->planet->id,
            'planet_name' => $event->planet->name,
            'exception' => $exception->getMessage(),
            'exception_class' => get_class($exception),
        ];

        // Add S3-specific error details if applicable
        if ($exception instanceof S3Exception) {
            $logContext['s3_error_code'] = $exception->getAwsErrorCode();
            $logContext['s3_error_message'] = $exception->getAwsErrorMessage();
            $logContext['s3_request_id'] = $exception->getAwsRequestId();
            $logContext['http_status'] = $exception->getStatusCode();
        } else {
            $logContext['trace'] = $exception->getTraceAsString();
        }

        Log::error('Planet image generation failed after all retries', $logContext);

        // TODO: Could notify admin, create a ticket, or trigger manual retry here
    }

    /**
     * Reset generating status for a planet.
     * This is a critical operation that must always succeed to prevent infinite loading states.
     *
     * @param  string  $planetId  The planet ID
     * @param  string  $type  Type of generation ('image' or 'video')
     */
    private function resetGeneratingStatus(string $planetId, string $type): void
    {
        try {
            $planet = \App\Models\Planet::find($planetId);
            if ($planet) {
                $field = $type === 'video' ? 'video_generating' : 'image_generating';
                $planet->update([$field => false]);
            }
        } catch (\Exception $updateException) {
            // If we can't update, log CRITICALLY but continue
            // This prevents infinite loops but indicates a serious problem
            Log::critical("CRITICAL: Failed to reset {$type}_generating status", [
                'planet_id' => $planetId,
                'type' => $type,
                'error' => $updateException->getMessage(),
                'exception_class' => get_class($updateException),
            ]);
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

        // Build the visual description based on planet characteristics
        $sizeDesc = $sizeDescriptions[$planet->size] ?? 'planet';
        $tempDesc = $temperatureDescriptions[$planet->temperature] ?? '';
        $atmoDesc = $atmosphereDescriptions[$planet->atmosphere] ?? '';
        $terrainDesc = $terrainDescriptions[$planet->terrain] ?? '';
        $typeDesc = $typeDescriptions[$planet->type] ?? 'alien planet';

        // Build color palette based on planet characteristics
        $colorPalette = 'deep blues, dark grays';
        if ($planet->terrain === 'désertique' || $planet->type === 'désertique') {
            $colorPalette = 'sandy beige, orange, reddish-brown, tan';
        } elseif ($planet->temperature === 'chaude') {
            $colorPalette = 'reddish-orange, deep reds, dark browns';
        } elseif ($planet->temperature === 'froide' || $planet->terrain === 'glacé' || $planet->type === 'glacée') {
            $colorPalette = 'icy whites, pale blues, silver';
        } elseif ($planet->terrain === 'océanique' || $planet->type === 'océanique') {
            $colorPalette = 'deep blues, turquoise, white';
        } elseif ($planet->atmosphere === 'toxique') {
            $colorPalette = 'yellow-green, purple, toxic colors';
        }

        // Create a prompt that generates a cinematic planet image
        // in the style of Alien (1979) - dark, atmospheric, realistic sci-fi
        return "Cinematic space view of {$planet->name}, a {$sizeDesc} {$typeDesc}, "
            ."viewed from space. The planet's surface shows {$terrainDesc}. "
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
}
