<?php

namespace App\Listeners;

use App\Events\PlanetCreated;
use App\Exceptions\StorageException;
use App\Exceptions\VideoGenerationException;
use App\Services\VideoGenerationService;
use Aws\S3\Exception\S3Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeneratePlanetVideo implements ShouldQueue, ShouldQueueAfterCommit
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
    public $backoff = 60; // Longer backoff for video generation (more expensive)

    /**
     * The number of seconds the job can run before timing out.
     * Video generation can take several minutes (up to 10+ minutes).
     *
     * @var int
     */
    public $timeout = 1200; // 20 minutes (60 attempts × 10 seconds + buffer for download)

    /**
     * Create the event listener.
     */
    public function __construct(
        private VideoGenerationService $videoGenerator
    ) {
        //
    }

    /**
     * Handle the event.
     *
     * Generates a video for the newly created planet.
     * This runs asynchronously in a queue job, so it doesn't block planet creation.
     * If generation fails, logs the error but does not block planet creation.
     */
    public function handle(PlanetCreated $event): void
    {
        // Check if video generation is enabled
        if (! config('video-generation.enabled', true)) {
            Log::info('Video generation is disabled, skipping planet video generation', [
                'planet_id' => $event->planet->id,
            ]);

            // Ensure generating status is false
            try {
                $planet = $event->planet->fresh();
                $planet->update(['video_generating' => false]);
            } catch (\Exception $e) {
                Log::warning('Failed to reset video_generating status when generation is disabled', [
                    'planet_id' => $event->planet->id,
                    'error' => $e->getMessage(),
                ]);
            }

            return;
        }

        try {
            // Reload planet to ensure we have the latest data
            $planet = $event->planet->fresh();

            // Prevent duplicate video generation - if planet already has a video, skip
            $existingVideo = $planet->getAttributes()['video_url'] ?? null;
            if ($existingVideo) {
                // Reset generating status if video already exists
                $planet->update(['video_generating' => false]);

                return;
            }

            // Mark as generating before starting
            $planet->update(['video_generating' => true]);

            // Generate planet video prompt in Alien style
            $prompt = $this->generatePlanetVideoPrompt($planet);

            // Generate planet video in 'planets' subfolder
            $result = $this->videoGenerator->generate($prompt, null, 'planets');

            // Store the path instead of full URL for flexibility
            // The URL will be reconstructed dynamically via the model accessor
            $planet->update([
                'video_url' => $result['path'],
                'video_generating' => false,
            ]);

            Log::info('Planet video generated successfully', [
                'planet_id' => $planet->id,
                'planet_name' => $planet->name,
                'video_path' => $result['path'],
                'video_url' => $result['url'], // Full URL for logging, but path is stored
            ]);
        } catch (S3Exception $s3Exception) {
            // Critical: Always reset generating status, even on S3 errors
            $this->resetGeneratingStatus($event->planet->id, 'video');

            // Log detailed S3 error information
            Log::error('S3 error during planet video generation', [
                'planet_id' => $event->planet->id,
                's3_error_code' => $s3Exception->getAwsErrorCode(),
                's3_error_message' => $s3Exception->getAwsErrorMessage(),
                's3_request_id' => $s3Exception->getAwsRequestId(),
                'http_status' => $s3Exception->getStatusCode(),
                'error' => $s3Exception->getMessage(),
            ]);

            // Re-throw to mark job as failed (will be retried according to queue config)
            throw new StorageException(
                "S3 error during video generation: {$s3Exception->getAwsErrorMessage()} (Code: {$s3Exception->getAwsErrorCode()})",
                0,
                $s3Exception
            );
        } catch (VideoGenerationException|StorageException $e) {
            // Critical: Always reset generating status on any error
            $this->resetGeneratingStatus($event->planet->id, 'video');

            // Log the error but don't block planet creation
            Log::error('Failed to generate planet video', [
                'planet_id' => $event->planet->id,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw custom exceptions as-is
            throw $e;
        } catch (\Exception $e) {
            // Critical: Always reset generating status on any error
            $this->resetGeneratingStatus($event->planet->id, 'video');

            // Log the error but don't block planet creation
            Log::error('Failed to generate planet video', [
                'planet_id' => $event->planet->id,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            // Wrap unknown exceptions in VideoGenerationException
            throw new VideoGenerationException("Failed to generate planet video: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(PlanetCreated $event, Throwable $exception): void
    {
        // Critical: Always reset generating status when job fails permanently
        $this->resetGeneratingStatus($event->planet->id, 'video');

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

        Log::error('Planet video generation failed after all retries', $logContext);

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
     * Generate a planet video prompt from spaceship orbit perspective.
     *
     * Creates a cinematic, immersive video showing the planet from a spaceship
     * in orbit. The camera slowly orbits around the planet, revealing its surface
     * details and atmospheric conditions. No UI overlays or text - pure cinematic
     * space exploration footage in the style of Alien (1979).
     *
     * @param  \App\Models\Planet  $planet  The planet to generate a video for
     * @return string The prompt for video generation
     */
    private function generatePlanetVideoPrompt(\App\Models\Planet $planet): string
    {
        // Map planet characteristics to visual descriptions
        $sizeDescriptions = [
            'petite' => 'small, compact planet',
            'moyenne' => 'medium-sized planet',
            'grande' => 'massive, imposing planet',
        ];

        $temperatureDescriptions = [
            'froide' => 'icy, frozen surface with crystalline formations glinting in starlight',
            'tempérée' => 'temperate climate with varied landscapes and visible weather patterns',
            'chaude' => 'scorching surface with volcanic activity, heat distortion, and glowing lava flows',
        ];

        $atmosphereDescriptions = [
            'respirable' => 'clear, breathable atmosphere with visible cloud formations drifting slowly',
            'toxique' => 'toxic, swirling atmosphere with ominous colored clouds and chemical haze',
            'inexistante' => 'airless void, stark and desolate with sharp shadows and no atmospheric glow',
        ];

        $terrainDescriptions = [
            'rocheux' => 'rugged, rocky terrain with sharp formations and deep canyons',
            'océanique' => 'vast oceans with turbulent waves and swirling currents visible from space',
            'désertique' => 'endless desert dunes creating flowing patterns under harsh light',
            'forestier' => 'alien forests with strange, twisted vegetation creating dark patches',
            'urbain' => 'abandoned industrial structures and ruins creating geometric patterns',
            'mixte' => 'diverse, mixed terrain with multiple biomes creating a patchwork surface',
            'glacé' => 'frozen wasteland with ice formations and glaciers reflecting light',
        ];

        $typeDescriptions = [
            'tellurique' => 'rocky terrestrial planet',
            'gazeuse' => 'gas giant with swirling atmospheric bands and storms',
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

        // Simple prompt: planet viewed from space with slow rotation
        return "Seamless looping video of {$planet->name}, a {$sizeDesc} and {$typeDesc}, "
            ."viewed from space. Planet surface: {$tempDesc}, {$terrainDesc}. "
            ."Atmosphere: {$atmoDesc}. "
            .'The planet slowly rotates in place, minimal camera movement. '
            .'Dark space background with stars. '
            .'Moody lighting in the style of Alien (1979) - deep blues, dark grays, muted colors. '
            .'Photorealistic, cinematic quality, 16:9 aspect ratio, perfect seamless loop.';
    }
}
