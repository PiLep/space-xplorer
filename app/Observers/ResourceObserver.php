<?php

namespace App\Observers;

use App\Models\Resource;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCheckFileExistence;

class ResourceObserver
{
    /**
     * Handle the Resource "created" event.
     */
    public function created(Resource $resource): void
    {
        $this->updateFileUrlCache($resource);
    }

    /**
     * Handle the Resource "updated" event.
     */
    public function updated(Resource $resource): void
    {
        // Only update cache if file_path changed
        if ($resource->wasChanged('file_path')) {
            $this->updateFileUrlCache($resource);
        }
    }

    /**
     * Handle the Resource "deleted" event.
     */
    public function deleted(Resource $resource): void
    {
        //
    }

    /**
     * Handle the Resource "restored" event.
     */
    public function restored(Resource $resource): void
    {
        $this->updateFileUrlCache($resource);
    }

    /**
     * Handle the Resource "force deleted" event.
     */
    public function forceDeleted(Resource $resource): void
    {
        //
    }

    /**
     * Update the file_url_cached field for the resource.
     *
     * This method computes the URL once and stores it, avoiding repeated S3 calls.
     */
    protected function updateFileUrlCache(Resource $resource): void
    {
        // Skip if file_path is null or empty
        if (! $resource->file_path) {
            // Clear cache if file_path is removed
            if ($resource->file_url_cached) {
                $resource->updateQuietly(['file_url_cached' => null]);
            }

            return;
        }

        // If it's already a full URL (old format), cache it as is
        if (filter_var($resource->file_path, FILTER_VALIDATE_URL)) {
            $resource->updateQuietly(['file_url_cached' => $resource->file_path]);

            return;
        }

        // Determine storage disk based on resource type
        $disk = match ($resource->type) {
            'avatar_image', 'planet_image' => config('image-generation.storage.disk') ?? 's3',
            'planet_video' => config('video-generation.storage.disk') ?? 's3',
            default => 's3',
        };

        try {
            // Check if file exists and get URL
            // Note: Storage::fake() works correctly with this, so no special handling needed
            if (Storage::disk($disk)->exists($resource->file_path)) {
                $url = Storage::disk($disk)->url($resource->file_path);
                $resource->updateQuietly(['file_url_cached' => $url]);
            } else {
                // File doesn't exist, clear cache
                $resource->updateQuietly(['file_url_cached' => null]);
            }
        } catch (UnableToCheckFileExistence|S3Exception $e) {
            // Log error but don't fail - cache will be null and accessor will handle it
            // This is expected in tests when Storage is mocked or files don't exist
            if (! app()->environment('testing')) {
                Log::warning('Failed to update file_url_cached for resource', [
                    'resource_id' => $resource->id,
                    'file_path' => $resource->file_path,
                    'error' => $e->getMessage(),
                ]);
            }

            // Clear cache on error
            $resource->updateQuietly(['file_url_cached' => null]);
        } catch (\Exception $e) {
            // Log error but don't fail - cache will be null and accessor will handle it
            // This is expected in tests when Storage is mocked or files don't exist
            if (! app()->environment('testing')) {
                Log::warning('Unexpected error updating file_url_cached for resource', [
                    'resource_id' => $resource->id,
                    'file_path' => $resource->file_path,
                    'error' => $e->getMessage(),
                    'exception_class' => get_class($e),
                ]);
            }

            // Clear cache on error
            $resource->updateQuietly(['file_url_cached' => null]);
        }
    }
}
