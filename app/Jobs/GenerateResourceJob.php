<?php

namespace App\Jobs;

use App\Models\Resource;
use App\Services\ResourceGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateResourceJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

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
     * Create a new job instance.
     */
    public function __construct(
        private Resource $resource
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ResourceGenerationService $resourceGenerator): void
    {
        // Ensure resource is still in generating status
        $this->resource->refresh();

        if ($this->resource->status !== 'generating') {
            Log::warning('Resource is not in generating status, skipping generation', [
                'resource_id' => $this->resource->id,
                'current_status' => $this->resource->status,
            ]);

            return;
        }

        // Get current attempt number from job attempts or metadata
        // Use metadata to track attempts if available (for testing), otherwise use job attempts
        $metadata = $this->resource->metadata ?? [];
        $previousAttempts = $metadata['generation_attempts'] ?? [];
        $attemptNumber = ! empty($previousAttempts) ? count($previousAttempts) + 1 : $this->attempts();

        // Log attempt
        Log::info('Attempting to generate resource', [
            'resource_id' => $this->resource->id,
            'type' => $this->resource->type,
            'attempt' => $attemptNumber,
            'max_attempts' => $this->tries,
        ]);

        try {
            // Generate the resource based on type
            match ($this->resource->type) {
                'avatar_image' => $resourceGenerator->generateAvatarTemplateForResource($this->resource),
                'planet_image' => $resourceGenerator->generatePlanetImageTemplateForResource($this->resource),
                'planet_video' => $resourceGenerator->generatePlanetVideoTemplateForResource($this->resource),
            };

            // Success - clear attempt history
            $this->resource->refresh();
            $metadata = $this->resource->metadata ?? [];
            unset($metadata['generation_attempts']);
            $this->resource->update(['metadata' => $metadata]);

            Log::info('Resource generated successfully', [
                'resource_id' => $this->resource->id,
                'attempt' => $attemptNumber,
            ]);
        } catch (\Exception $e) {
            // Refresh to get latest state
            $this->resource->refresh();

            // Record this attempt in metadata
            $previousAttempts[] = [
                'attempt' => $attemptNumber,
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ];

            $metadata = $this->resource->metadata ?? [];
            $metadata['generation_attempts'] = $previousAttempts;
            $this->resource->update(['metadata' => $metadata]);

            Log::error('Failed to generate resource in job', [
                'resource_id' => $this->resource->id,
                'attempt' => $attemptNumber,
                'max_attempts' => $this->tries,
                'error' => $e->getMessage(),
            ]);

            // Check if this was the last attempt
            if ($attemptNumber >= $this->tries) {
                // Last attempt failed - check if file exists before deleting
                if ($this->resource->file_path && $this->resource->file_url) {
                    // File exists, set to pending for review even though generation had issues
                    Log::warning('Setting resource to pending after max attempts, file exists but generation had errors', [
                        'resource_id' => $this->resource->id,
                    ]);
                    $this->resource->update(['status' => 'pending']);
                } else {
                    // No valid file after all attempts, delete the resource
                    Log::info('Deleting resource after max attempts without valid file', [
                        'resource_id' => $this->resource->id,
                        'attempts' => $attemptNumber,
                    ]);
                    $this->resource->delete();
                }
            } else {
                // Not the last attempt, throw exception to trigger retry
                Log::info('Will retry resource generation', [
                    'resource_id' => $this->resource->id,
                    'next_attempt' => $attemptNumber + 1,
                    'max_attempts' => $this->tries,
                ]);
            }

            throw $e;
        }
    }
}
