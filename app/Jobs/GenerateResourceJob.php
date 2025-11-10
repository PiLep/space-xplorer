<?php

namespace App\Jobs;

use App\Models\Resource;
use App\Services\ResourceGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateResourceJob implements ShouldQueue
{
    use Queueable, SerializesModels;

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
        try {
            // Ensure resource is still in generating status
            $this->resource->refresh();

            if ($this->resource->status !== 'generating') {
                Log::warning('Resource is not in generating status, skipping generation', [
                    'resource_id' => $this->resource->id,
                    'current_status' => $this->resource->status,
                ]);

                return;
            }

            // Generate the resource based on type
            match ($this->resource->type) {
                'avatar_image' => $resourceGenerator->generateAvatarTemplateForResource($this->resource),
                'planet_image' => $resourceGenerator->generatePlanetImageTemplateForResource($this->resource),
                'planet_video' => $resourceGenerator->generatePlanetVideoTemplateForResource($this->resource),
            };
        } catch (\Exception $e) {
            Log::error('Failed to generate resource in job', [
                'resource_id' => $this->resource->id,
                'error' => $e->getMessage(),
            ]);

            // Update resource status to pending on error (so it can be retried or reviewed)
            $this->resource->update(['status' => 'pending']);

            throw $e;
        }
    }
}
