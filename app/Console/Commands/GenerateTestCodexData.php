<?php

namespace App\Console\Commands;

use App\Models\CodexContribution;
use App\Models\CodexEntry;
use App\Models\Planet;
use App\Models\Resource;
use App\Models\User;
use App\Services\CodexService;
use Illuminate\Console\Command;

class GenerateTestCodexData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codex:generate-test-data
                            {--entries=20 : Number of codex entries to generate}
                            {--named=30 : Percentage of entries to name (0-100)}
                            {--contributions=10 : Number of contributions to generate}
                            {--with-ai : Generate AI descriptions (requires API key)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test data for Codex (entries, named planets, contributions)';

    /**
     * Execute the console command.
     */
    public function handle(CodexService $codexService): int
    {
        $entriesCount = (int) $this->option('entries');
        $namedPercentage = (int) $this->option('named');
        $contributionsCount = (int) $this->option('contributions');
        $withAI = $this->option('with-ai');

        // Validate inputs
        if ($entriesCount < 1 || $entriesCount > 100) {
            $this->error('Entries count must be between 1 and 100.');

            return Command::FAILURE;
        }

        if ($namedPercentage < 0 || $namedPercentage > 100) {
            $this->error('Named percentage must be between 0 and 100.');

            return Command::FAILURE;
        }

        // Get users and planets
        $users = User::all();
        $planets = Planet::with('properties', 'starSystem')
            ->whereHas('starSystem', function ($q) {
                $q->where('discovered', true);
            })
            ->get();

        if ($users->isEmpty()) {
            $this->error('No users found. Please create users first.');

            return Command::FAILURE;
        }

        if ($planets->isEmpty()) {
            $this->error('No discovered planets found. Please generate planets first.');

            return Command::FAILURE;
        }

        // Get approved resources for images and videos
        $approvedImages = Resource::approved()->ofType('planet_image')->get();
        $approvedVideos = Resource::approved()->ofType('planet_video')->get();

        if ($approvedImages->isEmpty()) {
            $this->warn('⚠️  No approved planet images found. Planets will be created without images.');
        } else {
            $this->info("Found {$approvedImages->count()} approved planet image(s)");
        }

        if ($approvedVideos->isEmpty()) {
            $this->warn('⚠️  No approved planet videos found. Planets will be created without videos.');
        } else {
            $this->info("Found {$approvedVideos->count()} approved planet video(s)");
        }

        $this->newLine();
        $this->info('Generating test Codex data...');
        $this->newLine();

        // Generate codex entries
        $this->info("Creating {$entriesCount} codex entries...");
        $bar = $this->output->createProgressBar($entriesCount);
        $bar->start();

        $entriesCreated = 0;
        $namedEntries = 0;
        $imagesAssigned = 0;
        $videosAssigned = 0;
        $planetsToUse = $planets->random(min($entriesCount, $planets->count()));

        foreach ($planetsToUse as $planet) {
            // Reload planet with properties to ensure we have all characteristics
            $planet = $planet->fresh(['properties']);

            // Map planet characteristics to tags for resource matching
            $planetTags = $this->mapPlanetCharacteristicsToTags($planet);

            // Assign image from approved resources matching planet tags
            if (! $planet->image_url) {
                $matchingImage = Resource::findRandomApproved('planet_image', $planetTags);
                if ($matchingImage && $matchingImage->file_path) {
                    $planet->update([
                        'image_url' => $matchingImage->file_path,
                        'image_generating' => false,
                    ]);
                    $imagesAssigned++;
                } elseif ($approvedImages->isNotEmpty()) {
                    // Fallback to any approved image if no matching one found
                    $randomImage = $approvedImages->random();
                    $planet->update([
                        'image_url' => $randomImage->file_path,
                        'image_generating' => false,
                    ]);
                    $imagesAssigned++;
                }
            }

            // Assign video from approved resources (tags matching optional for videos)
            if (! $planet->video_url && $approvedVideos->isNotEmpty()) {
                $matchingVideo = Resource::findRandomApproved('planet_video', $planetTags);
                if ($matchingVideo && $matchingVideo->file_path) {
                    $planet->update([
                        'video_url' => $matchingVideo->file_path,
                        'video_generating' => false,
                    ]);
                    $videosAssigned++;
                } else {
                    // Fallback to any approved video if no matching one found
                    $randomVideo = $approvedVideos->random();
                    $planet->update([
                        'video_url' => $randomVideo->file_path,
                        'video_generating' => false,
                    ]);
                    $videosAssigned++;
                }
            }

            // Check if entry already exists
            $existingEntry = CodexEntry::where('planet_id', $planet->id)->first();

            if ($existingEntry) {
                $entry = $existingEntry;
            } else {
                // Get a random user as discoverer
                $discoverer = $users->random();

                // Create entry
                try {
                    if ($withAI) {
                        // Use service to create with AI description
                        $entry = $codexService->createEntryForPlanet($planet, $discoverer);
                    } else {
                        // Create without AI (faster, no API calls)
                        $entry = $codexService->createEntryForPlanet($planet, $discoverer);
                        // Override description with simple one if AI failed or wasn't used
                        if (! $entry->description) {
                            $entry->update([
                                'description' => $this->generateSimpleDescription($planet),
                            ]);
                        }
                    }
                    $entriesCreated++;
                } catch (\Exception $e) {
                    $this->warn("Failed to create entry for planet {$planet->id}: {$e->getMessage()}");

                    continue;
                }
            }

            // Name planet if percentage allows
            if (rand(1, 100) <= $namedPercentage && ! $entry->is_named) {
                try {
                    $name = $this->generateUniquePlanetName($entry->discoveredBy ?? $users->random());
                    $codexService->namePlanet($entry, $entry->discoveredBy ?? $users->random(), $name);
                    $namedEntries++;
                } catch (\Exception $e) {
                    // Name might already exist, skip
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Created {$entriesCreated} codex entries ({$namedEntries} named)");
        $this->newLine();

        // Generate contributions
        if ($contributionsCount > 0) {
            $this->info("Creating {$contributionsCount} contributions...");
            $bar = $this->output->createProgressBar($contributionsCount);
            $bar->start();

            $entries = CodexEntry::with('planet')->get();
            $contributionsCreated = 0;

            if ($entries->isEmpty()) {
                $this->warn('No codex entries available for contributions.');
            } else {
                for ($i = 0; $i < $contributionsCount; $i++) {
                    $entry = $entries->random();
                    $contributor = $users->random();

                    try {
                        CodexContribution::create([
                            'codex_entry_id' => $entry->id,
                            'contributor_user_id' => $contributor->id,
                            'content_type' => 'description',
                            'content' => $this->generateContributionContent($entry),
                            'status' => $this->weightedRandom(['pending', 'approved', 'rejected'], [30, 60, 10]),
                        ]);
                        $contributionsCreated++;
                    } catch (\Exception $e) {
                        // Skip on error
                    }

                    $bar->advance();
                }
            }

            $bar->finish();
            $this->newLine();
            $this->info("✅ Created {$contributionsCreated} contributions");
            $this->newLine();
        }

        // Summary
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📊 Codex Test Data Summary:');
        $this->line('   • Total entries: <fg=cyan>'.CodexEntry::public()->discovered()->count().'</>');
        $this->line('   • Named planets: <fg=cyan>'.CodexEntry::public()->discovered()->named()->count().'</>');
        $this->line("   • Images assigned: <fg=cyan>{$imagesAssigned}</>");
        $this->line("   • Videos assigned: <fg=cyan>{$videosAssigned}</>");
        $this->line('   • Contributions: <fg=cyan>'.CodexContribution::count().'</>');
        $this->line('   • Pending contributions: <fg=yellow>'.CodexContribution::where('status', 'pending')->count().'</>');
        $this->line('   • Approved contributions: <fg=green>'.CodexContribution::where('status', 'approved')->count().'</>');
        $this->newLine();
        $this->info('✨ Test data generated successfully!');
        $this->info('   Visit /codex to see the Codex Stellaris');

        return Command::SUCCESS;
    }

    /**
     * Generate a unique planet name.
     */
    private function generateUniquePlanetName(User $user): string
    {
        $prefixes = [
            'Nova', 'Stellar', 'Aurora', 'Nebula', 'Cosmos', 'Celestia', 'Lumina',
            'Astral', 'Vortex', 'Eclipse', 'Phoenix', 'Titan', 'Atlas', 'Orion',
            'Pegasus', 'Andromeda', 'Cassiopeia', 'Sirius', 'Vega', 'Polaris',
            'Aether', 'Nyx', 'Helios', 'Selene', 'Gaia', 'Terra', 'Mars', 'Jupiter',
        ];

        $suffixes = [
            'Prime', 'Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon', 'Zeta',
            'Major', 'Minor', 'Superior', 'Inferior', 'Nova', 'Ultra',
        ];

        $maxAttempts = 20;
        $attempt = 0;

        do {
            $name = $prefixes[array_rand($prefixes)].' '.$suffixes[array_rand($suffixes)].' '.rand(1, 999);
            $exists = CodexEntry::where('name', $name)
                ->orWhere('fallback_name', $name)
                ->exists();
            $attempt++;
        } while ($exists && $attempt < $maxAttempts);

        return $name;
    }

    /**
     * Generate contribution content.
     */
    private function generateContributionContent(CodexEntry $entry): string
    {
        $contents = [
            "Cette planète présente des caractéristiques uniques dans notre galaxie. Sa composition atmosphérique et son terrain en font un objet d'étude fascinant pour les chercheurs spatiaux.",
            'Les observations récentes révèlent des formations géologiques intéressantes. Les explorateurs ont noté la présence de structures inhabituelles à la surface qui méritent une analyse approfondie.',
            "L'analyse spectrale indique une composition minérale riche. Cette planète pourrait être une source importante de ressources pour les futures missions d'exploration.",
            'Les conditions climatiques sont remarquables. Les variations de température et les phénomènes météorologiques observés méritent une étude approfondie par les scientifiques.',
            "Cette planète a été le théâtre de plusieurs découvertes scientifiques majeures. Son écosystème unique attire l'attention de nombreux chercheurs interstellaires.",
            "Les données collectées suggèrent un potentiel d'habitabilité intéressant. Des missions d'exploration supplémentaires sont recommandées pour évaluer ce potentiel.",
            "L'histoire géologique de cette planète est complexe. Les strates rocheuses révèlent des périodes d'activité intense qui ont façonné sa surface actuelle.",
            "Les observations astronomiques montrent des interactions intéressantes avec son système stellaire. L'influence gravitationnelle est notable et affecte son orbite.",
            'Cette planète possède des caractéristiques atmosphériques uniques qui la distinguent des autres corps célestes découverts jusqu\'à présent.',
            "L'exploration de cette planète a révélé des ressources naturelles abondantes qui pourraient être exploitées lors de futures missions coloniales.",
        ];

        return $contents[array_rand($contents)];
    }

    /**
     * Generate a simple description without AI API call.
     */
    private function generateSimpleDescription(Planet $planet): string
    {
        $properties = $planet->properties;

        if (! $properties) {
            return "Une planète mystérieuse découverte dans les profondeurs de l'espace. Ses caractéristiques restent largement inconnues, nécessitant une exploration et une étude approfondies par les équipes scientifiques.";
        }

        $type = $properties->type ?? 'inconnu';
        $size = $properties->size ?? 'inconnue';
        $temperature = $properties->temperature ?? 'inconnue';
        $atmosphere = $properties->atmosphere ?? 'inconnue';
        $terrain = $properties->terrain ?? 'inconnu';

        $typeFr = [
            'terrestrial' => 'tellurique',
            'gaseous' => 'gazeuse',
            'icy' => 'glacée',
            'desert' => 'désertique',
            'oceanic' => 'océanique',
            'volcanic' => 'volcanique',
        ][$type] ?? $type;

        $sizeFr = [
            'small' => 'petite',
            'medium' => 'moyenne',
            'large' => 'grande',
        ][$size] ?? $size;

        $tempFr = [
            'cold' => 'froide',
            'temperate' => 'tempérée',
            'hot' => 'chaude',
        ][$temperature] ?? $temperature;

        $atmoFr = [
            'breathable' => 'respirable',
            'toxic' => 'toxique',
            'nonexistent' => 'inexistante',
        ][$atmosphere] ?? $atmosphere;

        $terrainFr = [
            'rocky' => 'rocheux',
            'oceanic' => 'océanique',
            'desert' => 'désertique',
            'forested' => 'forestier',
            'urban' => 'urbain',
            'mixed' => 'mixte',
            'icy' => 'glacé',
        ][$terrain] ?? $terrain;

        return "Cette planète {$typeFr} est classifiée comme {$sizeFr} avec un climat {$tempFr}. "
            ."L'atmosphère est {$atmoFr}, et le terrain de surface consiste principalement en formations {$terrainFr}. "
            .'Une exploration approfondie est nécessaire pour comprendre pleinement les caractéristiques uniques de ce corps céleste et son potentiel pour la découverte scientifique et l\'exploration spatiale.';
    }

    /**
     * Weighted random selection.
     */
    private function weightedRandom(array $items, array $weights): string
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        $currentWeight = 0;

        foreach ($items as $index => $item) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $item;
            }
        }

        return $items[0];
    }

    /**
     * Map planet characteristics to tags for resource matching.
     *
     * @param  Planet  $planet  The planet to map characteristics from
     * @return array<string> Array of tags for resource matching
     */
    private function mapPlanetCharacteristicsToTags(Planet $planet): array
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
}
