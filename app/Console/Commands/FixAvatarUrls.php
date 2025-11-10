<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixAvatarUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-avatar-urls 
                            {--dry-run : Show what would be changed without actually updating}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert stored avatar URLs to paths for better flexibility';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔧 Avatar URL Fix Tool');
        $this->newLine();

        // Find users with full URLs (old format)
        $users = User::whereNotNull('avatar_url')
            ->get()
            ->filter(function ($user) {
                $url = $user->getRawOriginal('avatar_url');

                // Check if it's a full URL (old format)
                return filter_var($url, FILTER_VALIDATE_URL);
            });

        if ($users->isEmpty()) {
            $this->info('✅ All avatars are already using the path format!');
            $this->newLine();

            return Command::SUCCESS;
        }

        $this->info("Found {$users->count()} user(s) with full URLs to convert.");
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Show what will be changed
        $this->table(
            ['User', 'Current URL', 'New Path'],
            $users->map(function ($user) {
                $currentUrl = $user->getRawOriginal('avatar_url');
                $newPath = $this->extractPathFromUrl($currentUrl);

                return [
                    $user->name,
                    substr($currentUrl, 0, 60).'...',
                    $newPath ?? 'Could not extract',
                ];
            })->toArray()
        );

        $this->newLine();

        if (! $this->option('dry-run') && ! $this->option('force')) {
            if (! $this->confirm('Do you want to convert these URLs to paths?', true)) {
                $this->info('Operation cancelled.');

                return Command::SUCCESS;
            }
        }

        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info('✅ Dry run complete. Use without --dry-run to apply changes.');

            return Command::SUCCESS;
        }

        // Progress bar
        $bar = $this->output->createProgressBar($users->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($users as $user) {
            $bar->setMessage("Processing {$user->name}...");

            try {
                $currentUrl = $user->getRawOriginal('avatar_url');
                $path = $this->extractPathFromUrl($currentUrl);

                if ($path) {
                    $user->update(['avatar_url' => $path]);
                    $successCount++;
                } else {
                    $errorCount++;
                    $this->warn("\n⚠️  Could not extract path from URL for {$user->name}");
                }
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("\n❌ Error processing {$user->name}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('📊 Summary:');
        $this->line("✅ Successfully converted: {$successCount}");
        $this->line("❌ Failed: {$errorCount}");
        $this->newLine();

        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Extract the storage path from a full URL.
     *
     * @param  string  $url  The full URL
     * @return string|null The extracted path or null if extraction fails
     */
    private function extractPathFromUrl(string $url): ?string
    {
        // Try to extract path from various URL formats
        // Format 1: http://localhost:9000/space-xplorer/images/generated/uuid.png
        // Format 2: https://bucket.s3.region.amazonaws.com/images/generated/uuid.png
        // Format 3: https://s3.region.amazonaws.com/bucket/images/generated/uuid.png

        // Remove query parameters if any
        $url = parse_url($url, PHP_URL_PATH);

        if (! $url) {
            return null;
        }

        // Remove leading slash
        $url = ltrim($url, '/');

        // Try to find the bucket name and remove it
        $bucket = config('filesystems.disks.s3.bucket');
        if ($bucket && str_starts_with($url, $bucket.'/')) {
            return substr($url, strlen($bucket) + 1);
        }

        // If bucket is not at the start, try to find 'images/generated' pattern
        if (str_contains($url, 'images/generated/')) {
            $pos = strpos($url, 'images/generated/');

            return substr($url, $pos);
        }

        // Last resort: return the path after removing known prefixes
        $knownPrefixes = [
            $bucket.'/',
            'space-xplorer/',
        ];

        foreach ($knownPrefixes as $prefix) {
            if (str_starts_with($url, $prefix)) {
                return substr($url, strlen($prefix));
            }
        }

        return $url;
    }
}
