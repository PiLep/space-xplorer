<?php

namespace App\Console\Commands;

use App\Models\Resource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TranslateResourceTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resources:translate-tags
                            {--dry-run : Show what would be translated without making changes}
                            {--force : Force translation even if tags are already in English}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate resource tags from French to English in existing resources';

    /**
     * Mapping of French tags to English tags.
     *
     * @var array<string, string>
     */
    private const TAG_TRANSLATIONS = [
        // Planet types
        'tellurique' => 'terrestrial',
        'gazeuse' => 'gaseous',
        'glacée' => 'icy',
        'désertique' => 'desert',
        'océanique' => 'oceanic',

        // Sizes
        'petite' => 'small',
        'moyenne' => 'medium',
        'grande' => 'large',

        // Temperatures
        'froide' => 'cold',
        'tempérée' => 'temperate',
        'chaude' => 'hot',

        // Atmospheres
        'respirable' => 'breathable',
        'toxique' => 'toxic',
        'inexistante' => 'nonexistent',

        // Terrains
        'rocheux' => 'rocky',
        'océanique' => 'oceanic',
        'désertique' => 'desert',
        'forestier' => 'forested',
        'urbain' => 'urban',
        'mixte' => 'mixed',
        'glacé' => 'icy',

        // Resources
        'abondantes' => 'abundant',
        'modérées' => 'moderate',
        'rares' => 'rare',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('Translating resource tags from French to English...');
        $this->newLine();

        // Find all resources with tags
        $query = Resource::query()->whereNotNull('tags');

        if (! $force) {
            // Only translate resources with French tags
            $frenchTags = array_keys(self::TAG_TRANSLATIONS);
            $query->where(function ($q) use ($frenchTags) {
                foreach ($frenchTags as $frenchTag) {
                    $q->orWhereJsonContains('tags', $frenchTag);
                }
            });
        }

        $resourcesToTranslate = $query->get();
        $totalResources = $resourcesToTranslate->count();

        if ($totalResources === 0) {
            $this->info('No resources found that need translation.');

            return Command::SUCCESS;
        }

        $this->info("Found {$totalResources} resource(s) to translate.");
        $this->newLine();

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made.');
            $this->newLine();
        }

        $bar = $this->output->createProgressBar($totalResources);
        $bar->start();

        $translated = 0;
        $skipped = 0;
        $failed = 0;

        DB::beginTransaction();

        try {
            foreach ($resourcesToTranslate as $resource) {
                $tags = $resource->tags ?? [];
                $originalTags = $tags;
                $updated = false;

                // Translate each tag
                $translatedTags = [];
                foreach ($tags as $tag) {
                    $tagLower = strtolower(trim($tag));
                    $translatedTag = self::TAG_TRANSLATIONS[$tagLower] ?? null;

                    if ($translatedTag && ($force || $tagLower !== strtolower($translatedTag))) {
                        // Replace French tag with English tag
                        $translatedTags[] = $translatedTag;
                        $updated = true;
                    } else {
                        // Keep original tag (already in English or unknown)
                        $translatedTags[] = $tag;
                    }
                }

                // Remove duplicates while preserving order
                $translatedTags = array_values(array_unique($translatedTags));

                if (! $updated && ! $force) {
                    $skipped++;
                    $bar->advance();

                    continue;
                }

                try {
                    if (! $dryRun) {
                        $resource->update(['tags' => $translatedTags]);
                    }

                    $translated++;
                } catch (\Exception $e) {
                    $failed++;
                    $this->newLine();
                    $this->warn("Failed to translate resource {$resource->id}: {$e->getMessage()}");
                }

                $bar->advance();
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $bar->finish();
            $this->newLine(2);
            $this->error("Translation failed: {$e->getMessage()}");

            return Command::FAILURE;
        }

        $bar->finish();
        $this->newLine(2);

        if ($dryRun) {
            $this->info('DRY RUN - No changes were made.');
            $this->newLine();
        } else {
            $this->info('Translation complete!');
            $this->newLine();
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Resources translated', $translated],
                ['Resources skipped', $skipped],
                ['Failed', $failed],
            ]
        );

        return Command::SUCCESS;
    }
}
