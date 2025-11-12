<?php

namespace App\Console\Commands;

use App\Models\StarSystem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TranslateStarTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'star-systems:translate-types
                            {--dry-run : Show what would be translated without making changes}
                            {--force : Force translation even if types are already in English}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate star types from French to English in existing star systems';

    /**
     * Mapping of French star types to English star types.
     *
     * @var array<string, string>
     */
    private const TYPE_TRANSLATIONS = [
        'naine_jaune' => 'yellow_dwarf',
        'naine_rouge' => 'red_dwarf',
        'naine_orange' => 'orange_dwarf',
        'geante_rouge' => 'red_giant',
        'geante_bleue' => 'blue_giant',
        'naine_blanche' => 'white_dwarf',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('Translating star types from French to English...');
        $this->newLine();

        // Find all star systems with French types
        $query = StarSystem::query();

        if (! $force) {
            // Only translate systems with French types
            $frenchTypes = array_keys(self::TYPE_TRANSLATIONS);
            $query->whereIn('star_type', $frenchTypes);
        } else {
            // Translate all systems (including those already in English)
            $query->whereNotNull('star_type');
        }

        $systemsToTranslate = $query->get();
        $totalSystems = $systemsToTranslate->count();

        if ($totalSystems === 0) {
            $this->info('No star systems found that need translation.');

            return Command::SUCCESS;
        }

        $this->info("Found {$totalSystems} star system(s) to translate.");
        $this->newLine();

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made.');
            $this->newLine();
        }

        $bar = $this->output->createProgressBar($totalSystems);
        $bar->start();

        $translated = 0;
        $skipped = 0;
        $failed = 0;

        $translationStats = [];

        DB::beginTransaction();

        try {
            foreach ($systemsToTranslate as $system) {
                $oldType = $system->star_type;
                $newType = self::TYPE_TRANSLATIONS[$oldType] ?? null;

                if (! $newType) {
                    // Type is already in English or unknown
                    if ($force && $oldType && ! in_array($oldType, array_values(self::TYPE_TRANSLATIONS))) {
                        // Unknown type, skip
                        $skipped++;
                        $bar->advance();

                        continue;
                    }

                    // Already in English
                    $skipped++;
                    $bar->advance();

                    continue;
                }

                if ($oldType === $newType) {
                    // Already translated
                    $skipped++;
                    $bar->advance();

                    continue;
                }

                try {
                    if (! $dryRun) {
                        $system->update(['star_type' => $newType]);
                    }

                    $translated++;
                    $translationStats[$oldType] = ($translationStats[$oldType] ?? 0) + 1;
                } catch (\Exception $e) {
                    $failed++;
                    $this->newLine();
                    $this->warn("Failed to translate system {$system->id} ({$system->name}): {$e->getMessage()}");
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
                ['Systems translated', $translated],
                ['Systems skipped', $skipped],
                ['Failed', $failed],
            ]
        );

        if (! empty($translationStats)) {
            $this->newLine();
            $this->info('Translation breakdown:');
            $breakdown = [];
            foreach ($translationStats as $oldType => $count) {
                $newType = self::TYPE_TRANSLATIONS[$oldType] ?? 'unknown';
                $breakdown[] = ["{$oldType} → {$newType}", $count];
            }
            $this->table(
                ['Translation', 'Count'],
                $breakdown
            );
        }

        return Command::SUCCESS;
    }
}
