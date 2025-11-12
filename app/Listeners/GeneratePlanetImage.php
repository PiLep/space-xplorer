<?php

namespace App\Listeners;

use App\Events\PlanetCreated;
use App\Events\PlanetImageGenerated;
use App\Exceptions\ImageGenerationException;
use App\Exceptions\StorageException;
use App\Models\Resource;
use App\Services\ImageGenerationService;
use App\Services\ResourceGenerationService;
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
        private ImageGenerationService $imageGenerator,
        private ResourceGenerationService $resourceGenerator
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
            // Reload planet to ensure we have the latest data and load properties
            $planet = $event->planet->fresh(['properties']);

            // Prevent duplicate image generation - if planet already has an image, skip
            $existingImage = $planet->getAttributes()['image_url'] ?? null;
            if ($existingImage) {
                // Reset generating status if image already exists
                $planet->update(['image_generating' => false]);

                return;
            }

            // Mark as generating before starting
            $planet->update(['image_generating' => true]);

            // Try to use an approved planet image resource matching planet tags
            $planetTags = $this->mapPlanetCharacteristicsToTags($planet);
            $planetResource = Resource::findRandomApproved('planet_image', $planetTags);

            if ($planetResource && $planetResource->file_path) {
                // Use resource file path
                $planet->update([
                    'image_url' => $planetResource->file_path,
                    'image_generating' => false,
                ]);

                Log::info('Planet image assigned from resource', [
                    'planet_id' => $planet->id,
                    'planet_name' => $planet->name,
                    'resource_id' => $planetResource->id,
                    'planet_tags' => $planetTags,
                    'resource_tags' => $planetResource->tags,
                    'image_path' => $planetResource->file_path,
                ]);

                // Dispatch event to notify that planet image assignment is complete
                event(new PlanetImageGenerated($planet, $planetResource->file_path, $planetResource->file_url));

                return;
            }

            // Fallback to direct generation (either no template available or 30% chance)
            // Generate planet image prompt in Alien style
            $prompt = $this->generatePlanetPrompt($planet);

            // Generate planet image in 'planets' subfolder
            $result = $this->imageGenerator->generate($prompt, null, 'planets');

            // Extract tags from planet characteristics for resource
            $planetTags = $this->mapPlanetCharacteristicsToTags($planet);

            // Create a resource from this generated image so it can be reused
            try {
                $resource = Resource::create([
                    'type' => 'planet_image',
                    'status' => 'pending', // Requires admin approval before being reused
                    'file_path' => $result['path'],
                    'prompt' => $prompt,
                    'tags' => $planetTags,
                    'description' => "Auto-generated planet image for {$planet->name} ({$planet->type})",
                    'metadata' => [
                        'provider' => $result['provider'] ?? null,
                        'generated_at' => now()->toIso8601String(),
                        'auto_generated' => true,
                        'planet_id' => $planet->id,
                        'planet_type' => $planet->type,
                        'planet_size' => $planet->size,
                        'planet_temperature' => $planet->temperature,
                        'planet_atmosphere' => $planet->atmosphere,
                        'planet_terrain' => $planet->terrain,
                    ],
                    'created_by' => null, // System-generated
                ]);

                Log::info('Planet image resource created from direct generation', [
                    'planet_id' => $planet->id,
                    'planet_name' => $planet->name,
                    'resource_id' => $resource->id,
                    'image_path' => $result['path'],
                ]);
            } catch (\Exception $resourceException) {
                // Log but don't fail the planet image generation if resource creation fails
                Log::warning('Failed to create resource from generated planet image', [
                    'planet_id' => $planet->id,
                    'error' => $resourceException->getMessage(),
                ]);
            }

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

            // Dispatch event to notify that planet image generation is complete
            event(new PlanetImageGenerated($planet, $result['path'], $result['url']));
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
     * Map planet characteristics to tags for resource matching.
     *
     * Converts planet characteristics (type, size, temperature, atmosphere, terrain)
     * into an array of tags that can be used to find matching resources.
     *
     * @param  \App\Models\Planet  $planet  The planet to map characteristics from
     * @return array<string> Array of tags for resource matching
     */
    private function mapPlanetCharacteristicsToTags(\App\Models\Planet $planet): array
    {
        $tags = [];

        // Add planet type as tag
        if ($planet->type) {
            $tags[] = strtolower($planet->type);
        }

        // Add size as tag
        if ($planet->size) {
            $tags[] = strtolower($planet->size);
        }

        // Add temperature as tag
        if ($planet->temperature) {
            $tags[] = strtolower($planet->temperature);
        }

        // Add atmosphere as tag
        if ($planet->atmosphere) {
            $tags[] = strtolower($planet->atmosphere);
        }

        // Add terrain as tag
        if ($planet->terrain) {
            $tags[] = strtolower($planet->terrain);
        }

        // Add resources level as tag
        if ($planet->resources) {
            $tags[] = strtolower($planet->resources);
        }

        return array_unique($tags);
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
        // Map planet characteristics to visual descriptions (using English values)
        $sizeDescriptions = [
            'small' => 'small, compact planet',
            'medium' => 'medium-sized planet',
            'large' => 'massive, imposing planet',
        ];

        $temperatureDescriptions = [
            'cold' => 'icy, frozen surface covered in ice and snow, crystalline formations, polar ice caps visible',
            'temperate' => 'temperate climate with varied landscapes, green and blue tones, visible continents and oceans',
            'hot' => 'scorching hot surface, reddish-orange tones, volcanic activity, heat distortion visible in atmosphere',
        ];

        $atmosphereDescriptions = [
            'breathable' => 'clear, breathable atmosphere with white cloud formations, blue atmospheric glow at the edges',
            'toxic' => 'toxic, swirling atmosphere with yellow, green, or purple colored clouds, chemical haze visible',
            'nonexistent' => 'airless void, no atmospheric glow, stark shadows, cratered surface clearly visible',
        ];

        $terrainDescriptions = [
            'rocky' => 'rugged, rocky terrain with sharp mountain ranges, deep canyons, gray and brown rocky surface',
            'oceanic' => 'vast blue oceans covering most of the surface, minimal landmasses, white ocean foam visible',
            'desert' => 'endless sand dunes creating flowing patterns, orange and beige tones, dry cracked surface, no vegetation',
            'forested' => 'dense alien forests covering the surface, dark green patches, strange vegetation patterns',
            'urban' => 'abandoned industrial structures and ruins, geometric patterns, metallic gray surfaces, urban sprawl',
            'mixed' => 'diverse, mixed terrain with multiple biomes creating a patchwork surface of different colors',
            'icy' => 'frozen wasteland covered in ice and snow, white and blue tones, glaciers and ice sheets visible',
        ];

        $typeDescriptions = [
            'terrestrial' => 'rocky terrestrial planet with solid surface',
            'gaseous' => 'gas giant with swirling atmospheric bands in various colors',
            'icy' => 'ice planet completely covered in frozen layers',
            'desert' => 'barren desert planet with sand and rock',
            'oceanic' => 'ocean world with water covering most of the surface',
        ];

        // Build the visual description based on planet characteristics
        $sizeDesc = $sizeDescriptions[$planet->size] ?? 'planet';
        $tempDesc = $temperatureDescriptions[$planet->temperature] ?? '';
        $atmoDesc = $atmosphereDescriptions[$planet->atmosphere] ?? '';
        $terrainDesc = $terrainDescriptions[$planet->terrain] ?? '';
        $typeDesc = $typeDescriptions[$planet->type] ?? 'alien planet';

        // Build color palette based on planet characteristics
        $colorPalette = 'deep blues, dark grays';
        if ($planet->terrain === 'desert' || $planet->type === 'desert') {
            $colorPalette = 'sandy beige, orange, reddish-brown, tan';
        } elseif ($planet->temperature === 'hot') {
            $colorPalette = 'reddish-orange, deep reds, dark browns';
        } elseif ($planet->temperature === 'cold' || $planet->terrain === 'icy' || $planet->type === 'icy') {
            $colorPalette = 'icy whites, pale blues, silver';
        } elseif ($planet->terrain === 'oceanic' || $planet->type === 'oceanic') {
            $colorPalette = 'deep blues, turquoise, white';
        } elseif ($planet->atmosphere === 'toxic') {
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
