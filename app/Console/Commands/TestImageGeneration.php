<?php

namespace App\Console\Commands;

use App\Services\ImageGenerationService;
use Illuminate\Console\Command;

class TestImageGeneration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:test 
                            {prompt : The text prompt to generate an image from}
                            {--provider= : The provider to use (openai or stability). Defaults to config default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the image generation service with a prompt';

    /**
     * Execute the console command.
     */
    public function handle(ImageGenerationService $service): int
    {
        $prompt = $this->argument('prompt');
        $provider = $this->option('provider');

        $this->info('🎨 Testing image generation...');
        $this->line("Prompt: {$prompt}");
        if ($provider) {
            $this->line("Provider: {$provider}");
        } else {
            $this->line('Provider: '.config('image-generation.default_provider').' (default)');
        }
        $this->newLine();

        // Check if provider is configured
        $providerToUse = $provider ?? config('image-generation.default_provider');
        if (! $service->isProviderConfigured($providerToUse)) {
            $this->error("❌ Provider '{$providerToUse}' is not configured or missing API key.");
            $this->line('Available providers: '.implode(', ', $service->getAvailableProviders()));

            return Command::FAILURE;
        }

        try {
            $this->info('⏳ Generating image...');
            $startTime = microtime(true);

            $result = $service->generate($prompt, $provider);

            $duration = round(microtime(true) - $startTime, 2);

            $this->newLine();
            $this->info('✅ Image generated and saved successfully!');
            $this->line("Duration: {$duration}s");
            $this->line("Provider: {$result['provider']}");
            $this->newLine();

            // Display storage information
            $this->info('💾 Storage Information:');
            $this->line("Disk: {$result['disk']}");
            $this->line("Path: {$result['path']}");
            $this->newLine();

            // Display S3 URL
            $this->info('📷 Image URL (S3):');
            $this->line($result['url']);
            $this->newLine();

            if (isset($result['revised_prompt'])) {
                $this->comment('Revised prompt by DALL-E:');
                $this->line($result['revised_prompt']);
                $this->newLine();
            }

            $this->comment("💡 Tip: The image has been saved to {$result['disk']} and is accessible via the URL above.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Failed to generate image:');
            $this->line($e->getMessage());
            $this->newLine();

            if ($this->getOutput()->isVerbose()) {
                $this->error('Stack trace:');
                $this->line($e->getTraceAsString());
            }

            return Command::FAILURE;
        }
    }
}
