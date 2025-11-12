<?php

namespace App\Console\Commands;

use App\Models\Resource;
use Aws\S3\Exception\S3Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCheckFileExistence;

class CacheResourceUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resources:cache-urls {--force : Force update even if cache exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache file URLs for all resources to avoid expensive S3 calls';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔄 Caching resource URLs...');
        $this->newLine();

        $force = $this->option('force');

        // Get resources that need caching
        $query = Resource::whereNotNull('file_path');
        if (! $force) {
            $query->whereNull('file_url_cached');
        }

        $totalResources = $query->count();

        if ($totalResources === 0) {
            $this->info('✅ No resources need URL caching.');
            if (! $force) {
                $this->info('   (Use --force to update all resources)');
            }

            return Command::SUCCESS;
        }

        $this->info("Found {$totalResources} resource(s) to process.");
        $this->newLine();

        $bar = $this->output->createProgressBar($totalResources);
        $bar->start();

        $successCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        $query->chunk(100, function ($resources) use (&$successCount, &$errorCount, &$skippedCount, $bar) {
            foreach ($resources as $resource) {
                try {
                    $result = $this->cacheResourceUrl($resource);

                    if ($result === 'success') {
                        $successCount++;
                    } elseif ($result === 'skipped') {
                        $skippedCount++;
                    } else {
                        $errorCount++;
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('Error caching URL for resource', [
                        'resource_id' => $resource->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info('✅ Caching complete!');
        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Success', $successCount],
                ['⏭️  Skipped', $skippedCount],
                ['❌ Errors', $errorCount],
                ['📊 Total', $totalResources],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Cache the URL for a single resource.
     *
     * @return string 'success', 'skipped', or 'error'
     */
    protected function cacheResourceUrl(Resource $resource): string
    {
        // Skip if file_path is null
        if (! $resource->file_path) {
            return 'skipped';
        }

        // If it's already a full URL (old format), cache it as is
        if (filter_var($resource->file_path, FILTER_VALIDATE_URL)) {
            $resource->updateQuietly(['file_url_cached' => $resource->file_path]);

            return 'success';
        }

        // Determine storage disk based on resource type
        $disk = match ($resource->type) {
            'avatar_image', 'planet_image' => config('image-generation.storage.disk', 's3'),
            'planet_video' => config('video-generation.storage.disk', 's3'),
            default => 's3',
        };

        try {
            // Check if file exists and get URL
            if (Storage::disk($disk)->exists($resource->file_path)) {
                $url = Storage::disk($disk)->url($resource->file_path);
                $resource->updateQuietly(['file_url_cached' => $url]);

                return 'success';
            } else {
                // File doesn't exist, clear cache
                $resource->updateQuietly(['file_url_cached' => null]);

                return 'skipped';
            }
        } catch (UnableToCheckFileExistence|S3Exception $e) {
            Log::warning('Failed to cache URL for resource', [
                'resource_id' => $resource->id,
                'file_path' => $resource->file_path,
                'error' => $e->getMessage(),
            ]);

            return 'error';
        } catch (\Exception $e) {
            Log::warning('Unexpected error caching URL for resource', [
                'resource_id' => $resource->id,
                'file_path' => $resource->file_path,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
            ]);

            return 'error';
        }
    }
}
