<?php

namespace App\Console\Commands;

use App\Jobs\GenerateResourceJob;
use App\Models\Resource;
use App\Services\ResourceGenerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateDailyAvatarResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resources:generate-daily-avatars 
                            {--count=20 : Number of avatar image resources to generate per day}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily batch of avatar image resources for admin approval (scheduled task)';

    /**
     * Execute the console command.
     */
    public function handle(ResourceGenerationService $resourceGenerator): int
    {
        $count = (int) $this->option('count');

        if ($count < 1 || $count > 50) {
            $this->error('Count must be between 1 and 50.');

            return Command::FAILURE;
        }

        $this->info("Generating {$count} avatar image resources for daily batch...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $generated = 0;
        $failed = 0;

        // Generate varied prompts to cover different genders and professions
        $prompts = $this->generateVariedAvatarPrompts($count);

        foreach ($prompts as $index => $prompt) {
            try {
                // Extract tags from prompt
                $extractedTags = $resourceGenerator->extractAvatarTagsFromPrompt($prompt);

                // Create resource with 'generating' status
                $resource = Resource::create([
                    'type' => 'avatar_image',
                    'status' => 'generating',
                    'file_path' => null, // Will be set when generation completes
                    'prompt' => $prompt,
                    'tags' => $extractedTags,
                    'description' => "Daily auto-generated avatar #{$index} - Scheduled generation",
                    'metadata' => [
                        'auto_generated' => true,
                        'scheduled_generation' => true,
                        'generated_at' => now()->toIso8601String(),
                    ],
                    'created_by' => null, // System-generated
                ]);

                // Dispatch job for async generation
                GenerateResourceJob::dispatch($resource);

                $generated++;
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->warn("Failed to create resource #{$index}: {$e->getMessage()}");
                Log::error('Failed to create daily avatar resource', [
                    'index' => $index,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Daily generation complete! Created: {$generated}, Failed: {$failed}");
        $this->info('Resources are being generated asynchronously and will be available for admin approval once complete.');

        Log::info('Daily avatar resources generation completed', [
            'generated' => $generated,
            'failed' => $failed,
            'total_requested' => $count,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Generate varied avatar prompts covering different genders and professions.
     *
     * @param  int  $count  Number of prompts to generate
     * @return array<string> Array of avatar image prompts
     */
    private function generateVariedAvatarPrompts(int $count): array
    {
        $prompts = [];

        // Base prompts for different professions and styles
        // 50% men, 50% women for balanced distribution
        $maleProfessions = [
            'Close-up professional portrait headshot of a space technician man, weathered and experienced, in the style of Alien (1979) movie aesthetic. Industrial sci-fi setting, realistic lighting, cinematic composition.',
            'Professional headshot of a ship captain man, determined expression, wearing a technical jumpsuit with patches and insignia. Atmospheric lighting with blue and orange tones, photorealistic style.',
            'Portrait of a space explorer man, focused eyes, weathered skin with subtle scars, professional haircut. Dark, muted background, highly detailed facial features, moody and atmospheric.',
            'Close-up of a space engineer man, holding a data pad, wearing a weathered jumpsuit. Industrial setting, realistic shadows and highlights, square format (1:1 aspect ratio).',
            'Professional portrait of a space pilot man, determined expression, technical uniform visible. Cinematic quality, high resolution, detailed skin texture, atmospheric lighting.',
            'Headshot of a space scientist man, wearing a lab coat over a jumpsuit, intelligent expression, glasses visible. Clean, professional background, realistic lighting, detailed features.',
            'Portrait of a space mechanic man, grease-stained hands visible, wearing a rugged jumpsuit, confident expression. Industrial background, dramatic lighting, highly detailed.',
            'Close-up of a space medic man, compassionate expression, medical insignia visible on uniform. Soft, professional lighting, realistic skin texture, atmospheric composition.',
            'Professional portrait of a space communications officer man, headset visible, focused expression. Technical background, cinematic lighting, highly detailed facial features.',
            'Headshot of a space security officer man, serious expression, tactical gear visible, determined look. Dark, moody background, dramatic lighting, photorealistic style.',
        ];

        $femaleProfessions = [
            'Close-up professional portrait headshot of a space technician woman, weathered and experienced, in the style of Alien (1979) movie aesthetic. Industrial sci-fi setting, realistic lighting, cinematic composition.',
            'Professional headshot of a ship captain woman, determined expression, wearing a technical jumpsuit with patches and insignia. Atmospheric lighting with blue and orange tones, photorealistic style.',
            'Portrait of a space explorer woman, focused eyes, weathered skin with subtle scars, professional haircut. Dark, muted background, highly detailed facial features, moody and atmospheric.',
            'Close-up of a space engineer woman, holding a data pad, wearing a weathered jumpsuit. Industrial setting, realistic shadows and highlights, square format (1:1 aspect ratio).',
            'Professional portrait of a space pilot woman, determined expression, technical uniform visible. Cinematic quality, high resolution, detailed skin texture, atmospheric lighting.',
            'Headshot of a space scientist woman, wearing a lab coat over a jumpsuit, intelligent expression, glasses visible. Clean, professional background, realistic lighting, detailed features.',
            'Portrait of a space mechanic woman, grease-stained hands visible, wearing a rugged jumpsuit, confident expression. Industrial background, dramatic lighting, highly detailed.',
            'Close-up of a space medic woman, compassionate expression, medical insignia visible on uniform. Soft, professional lighting, realistic skin texture, atmospheric composition.',
            'Professional portrait of a space communications officer woman, headset visible, focused expression. Technical background, cinematic lighting, highly detailed facial features.',
            'Headshot of a space security officer woman, serious expression, tactical gear visible, determined look. Dark, moody background, dramatic lighting, photorealistic style.',
        ];

        // Generate prompts with balanced gender distribution
        for ($i = 0; $i < $count; $i++) {
            // Alternate between male and female, or use random distribution
            $isMale = ($i % 2 === 0) || (mt_rand(1, 100) <= 50);

            if ($isMale) {
                $basePrompt = $maleProfessions[array_rand($maleProfessions)];
            } else {
                $basePrompt = $femaleProfessions[array_rand($femaleProfessions)];
            }

            // Add standard avatar requirements
            $prompt = $basePrompt.' Single person only, no other people in frame, square format (1:1 aspect ratio), highly detailed, photorealistic style with sharp focus on the face.';

            $prompts[] = $prompt;
        }

        // Shuffle to randomize order
        shuffle($prompts);

        return $prompts;
    }
}
