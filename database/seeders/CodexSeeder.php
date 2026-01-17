<?php

namespace Database\Seeders;

use App\Models\CodexContribution;
use App\Models\CodexEntry;
use App\Models\Planet;
use App\Models\User;
use App\Services\CodexService;
use Illuminate\Database\Seeder;

class CodexSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📚 Seeding Codex data...');
        $this->command->newLine();

        $codexService = app(CodexService::class);
        $users = User::all();
        $planets = Planet::with('properties')->get();

        if ($planets->isEmpty()) {
            $this->command->warn('⚠️  No planets found. Please run the main seeder first.');
            $this->command->newLine();

            return;
        }

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  No users found. Please run the main seeder first.');
            $this->command->newLine();

            return;
        }

        // Créer des entrées de codex pour toutes les planètes
        $this->command->info('Creating codex entries for planets...');
        $entriesCreated = 0;
        $namedEntries = 0;

        foreach ($planets as $planet) {
            // Trouver un utilisateur aléatoire comme découvreur
            $discoverer = $users->random();

            // Vérifier si l'entrée existe déjà
            $existingEntry = CodexEntry::where('planet_id', $planet->id)->first();

            if ($existingEntry) {
                $entry = $existingEntry;
            } else {
                // Créer l'entrée de codex sans génération IA (pour éviter les blocages)
                $fallbackName = $codexService->generateFallbackName($planet);

                // Générer une description simple sans appel API
                $description = $this->generateSimpleDescription($planet);

                $entry = CodexEntry::create([
                    'planet_id' => $planet->id,
                    'fallback_name' => $fallbackName,
                    'description' => $description,
                    'discovered_by_user_id' => $discoverer->id,
                    'is_named' => false,
                    'is_public' => true,
                ]);
            }

            $entriesCreated++;

            // Nommer aléatoirement certaines planètes (30% de chance)
            if (rand(1, 100) <= 30 && ! $entry->is_named) {
                try {
                    $names = [
                        'Nova Prime', 'Stellaris', 'Aurora', 'Nebula', 'Cosmos',
                        'Celestia', 'Lumina', 'Astral', 'Vortex', 'Eclipse',
                        'Phoenix', 'Titan', 'Atlas', 'Orion', 'Pegasus',
                        'Andromeda', 'Cassiopeia', 'Sirius', 'Vega', 'Polaris',
                    ];
                    $name = $names[array_rand($names)].' '.rand(1, 9);

                    $codexService->namePlanet($entry, $discoverer, $name);
                    $namedEntries++;
                } catch (\Exception $e) {
                    // Ignore les erreurs de validation (nom déjà utilisé, etc.)
                }
            }
        }

        $this->command->info("✅ Created {$entriesCreated} codex entries ({$namedEntries} named)");
        $this->command->newLine();

        // Créer des contributions pour certaines entrées
        $this->command->info('Creating codex contributions...');
        $entries = CodexEntry::with('planet')->get();
        $contributionsCreated = 0;

        foreach ($entries->random(min(10, $entries->count())) as $entry) {
            $contributor = $users->random();

            // Créer 1-3 contributions par entrée
            $contributionCount = rand(1, 3);

            for ($i = 0; $i < $contributionCount; $i++) {
                $statuses = ['pending', 'approved', 'rejected'];
                $weights = [50, 40, 10]; // 50% pending, 40% approved, 10% rejected

                $status = $this->weightedRandom($statuses, $weights);

                CodexContribution::create([
                    'codex_entry_id' => $entry->id,
                    'contributor_user_id' => $contributor->id,
                    'content_type' => 'description',
                    'content' => $this->generateContributionContent($entry),
                    'status' => $status,
                ]);

                $contributionsCreated++;
            }
        }

        $this->command->info("✅ Created {$contributionsCreated} contributions");
        $this->command->newLine();

        // Résumé
        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📊 Codex Summary:');
        $this->command->line("   • Codex entries: <fg=cyan>{$entriesCreated}</>");
        $this->command->line("   • Named planets: <fg=cyan>{$namedEntries}</>");
        $this->command->line("   • Contributions: <fg=cyan>{$contributionsCreated}</>");
        $this->command->line('   • Pending contributions: <fg=yellow>'.CodexContribution::where('status', 'pending')->count().'</>');
        $this->command->line('   • Approved contributions: <fg=green>'.CodexContribution::where('status', 'approved')->count().'</>');
        $this->command->line('   • Rejected contributions: <fg=red>'.CodexContribution::where('status', 'rejected')->count().'</>');
        $this->command->newLine();
        $this->command->info('✨ Codex seeded successfully!');
    }

    /**
     * Generate contribution content based on the entry.
     */
    private function generateContributionContent(CodexEntry $entry): string
    {
        $contents = [
            "Cette planète présente des caractéristiques uniques dans notre galaxie. Sa composition atmosphérique et son terrain en font un objet d'étude fascinant pour les chercheurs.",
            'Les observations récentes révèlent des formations géologiques intéressantes. Les explorateurs ont noté la présence de structures inhabituelles à la surface.',
            "L'analyse spectrale indique une composition minérale riche. Cette planète pourrait être une source importante de ressources pour les futures missions.",
            'Les conditions climatiques sont remarquables. Les variations de température et les phénomènes météorologiques observés méritent une étude approfondie.',
            "Cette planète a été le théâtre de plusieurs découvertes scientifiques majeures. Son écosystème unique attire l'attention de nombreux chercheurs.",
            "Les données collectées suggèrent un potentiel d'habitabilité intéressant. Des missions d'exploration supplémentaires sont recommandées.",
            "L'histoire géologique de cette planète est complexe. Les strates rocheuses révèlent des périodes d'activité intense.",
            "Les observations astronomiques montrent des interactions intéressantes avec son système stellaire. L'influence gravitationnelle est notable.",
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
            return "Une planète mystérieuse découverte dans les profondeurs de l'espace. Ses caractéristiques restent largement inconnues, nécessitant une exploration et une étude approfondies.";
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
            .'Une exploration approfondie est nécessaire pour comprendre pleinement les caractéristiques uniques de ce corps céleste et son potentiel pour la découverte scientifique.';
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
}
