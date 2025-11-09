<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ImageGenerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateAvatarsForExistingUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:generate-avatars 
                            {--force : Skip confirmation prompt}
                            {--limit= : Limit the number of users to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate avatars for existing users who do not have one';

    /**
     * Execute the console command.
     */
    public function handle(ImageGenerationService $imageGenerator): int
    {
        $this->info('🎨 Avatar Generation Tool');
        $this->newLine();

        // Find users without avatars
        $query = User::whereNull('avatar_url');
        $totalUsers = $query->count();

        if ($totalUsers === 0) {
            $this->info('✅ All users already have avatars!');
            $this->newLine();

            return Command::SUCCESS;
        }

        // Apply limit if specified
        $limit = $this->option('limit');
        if ($limit) {
            $query->limit((int) $limit);
            $this->info("Found {$totalUsers} users without avatars (processing {$limit})");
        } else {
            $this->info("Found {$totalUsers} users without avatars");
        }

        $users = $query->get();

        $this->newLine();
        $this->warn('⚠️  This will generate avatars using AI image generation.');
        $this->warn('⚠️  Each avatar generation takes ~20-30 seconds and has API costs.');
        $this->newLine();

        // Ask for confirmation unless --force is used
        if (! $this->option('force')) {
            if (! $this->confirm("Do you want to generate avatars for {$users->count()} user(s)?", true)) {
                $this->info('Operation cancelled.');

                return Command::SUCCESS;
            }
        }

        $this->newLine();
        $this->info("Generating avatars for {$users->count()} user(s)...");
        $this->newLine();

        // Progress bar
        $bar = $this->output->createProgressBar($users->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');
        $bar->start();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($users as $user) {
            $bar->setMessage("Processing {$user->name}...");

            try {
                // Generate avatar prompt
                $prompt = $this->generateAvatarPrompt($user->name);

                // Generate avatar image
                $result = $imageGenerator->generate($prompt);

                // Store the path instead of full URL for flexibility
                $user->update(['avatar_url' => $result['path']]);

                $successCount++;

                Log::info('Avatar generated via command', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'avatar_url' => $result['url'],
                ]);
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = [
                    'user' => $user->name,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ];

                Log::error('Failed to generate avatar via command', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'error' => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('📊 Summary:');
        $this->line("✅ Successfully generated: {$successCount}");
        $this->line("❌ Failed: {$errorCount}");

        if ($errorCount > 0) {
            $this->newLine();
            $this->warn('⚠️  Errors encountered:');
            foreach ($errors as $error) {
                $this->line("  • {$error['user']} ({$error['email']}): {$error['error']}");
            }
        }

        $this->newLine();

        return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Generate an avatar prompt in Alien style (technician/ship captain).
     *
     * @param  string  $userName  The user's name (for personalization)
     * @return string The prompt for image generation
     */
    private function generateAvatarPrompt(string $userName): string
    {
        // Extract first name and detect gender
        $firstName = $this->extractFirstName($userName);
        $gender = $this->detectGender($firstName);

        // Adapt character description based on gender
        $character = match ($gender) {
            'female' => 'woman',
            'male' => 'man',
            default => 'person',
        };

        // Create a prompt that generates a professional space technician/captain avatar
        // in the style of Alien (1979) - industrial, realistic, sci-fi aesthetic
        return "Professional portrait of a single {$character}, {$userName}, "
            .'a space technician and ship captain, '
            .'in the style of Alien (1979) movie aesthetic. '
            .'Industrial sci-fi setting, realistic lighting, cinematic composition. '
            .'Single person only, wearing a worn technical jumpsuit with patches and insignia, '
            .'holding a data pad or technical tool. '
            .'Atmospheric lighting with blue and orange tones. '
            .'Simple, subtle background: dark, muted tones with minimal detail, '
            .'slightly blurred to emphasize the person. No distracting elements, '
            .'just a clean, professional backdrop that makes the character stand out. '
            .'Professional headshot portrait of one person only, no other people in frame, square format, '
            .'highly detailed, photorealistic style, '
            .'moody and atmospheric, cinematic quality.';
    }

    /**
     * Extract the first name from a full name.
     *
     * @param  string  $fullName  The full name
     * @return string The first name
     */
    private function extractFirstName(string $fullName): string
    {
        $parts = explode(' ', trim($fullName));

        return $parts[0] ?? $fullName;
    }

    /**
     * Detect gender based on first name (simple heuristic).
     *
     * @param  string  $firstName  The first name
     * @return string 'male', 'female', or 'neutral'
     */
    private function detectGender(string $firstName): string
    {
        $firstNameLower = strtolower(trim($firstName));

        // Unisex names (common in the project) - default to neutral
        $unisexNames = ['alex', 'sam', 'jordan', 'riley', 'taylor', 'morgan', 'casey', 'jamie', 'cameron', 'dakota'];
        if (in_array($firstNameLower, $unisexNames)) {
            return 'neutral';
        }

        // Common female first names (exact matches)
        $femaleNames = [
            'sophia',
            'emma',
            'olivia',
            'isabella',
            'ava',
            'mia',
            'charlotte',
            'amelia',
            'harper',
            'evelyn',
            'abigail',
            'emily',
            'elizabeth',
            'ella',
            'sophie',
            'marie',
            'julie',
            'anne',
            'claire',
            'sarah',
            'laura',
            'jessica',
            'jennifer',
            'maria',
            'anna',
            'patricia',
            'linda',
            'barbara',
            'susan',
            'nancy',
        ];

        // Common male first names (exact matches)
        $maleNames = [
            'john',
            'james',
            'michael',
            'david',
            'robert',
            'william',
            'richard',
            'joseph',
            'alexander',
            'daniel',
            'matthew',
            'christopher',
            'thomas',
            'charles',
            'mark',
            'paul',
            'steven',
            'andrew',
            'kenneth',
            'joshua',
            'kevin',
            'brian',
            'george',
        ];

        // Check exact matches first
        if (in_array($firstNameLower, $femaleNames)) {
            return 'female';
        }

        if (in_array($firstNameLower, $maleNames)) {
            return 'male';
        }

        // Check female name endings
        $femaleEndings = ['a', 'ia', 'ella', 'ette', 'ine', 'elle', 'ette', 'ina', 'ana'];
        foreach ($femaleEndings as $ending) {
            if (str_ends_with($firstNameLower, $ending) && strlen($firstNameLower) > 3) {
                return 'female';
            }
        }

        // Check male name endings
        $maleEndings = ['o', 'er', 'on', 'en', 'an', 'el', 'al', 'io', 'us'];
        foreach ($maleEndings as $ending) {
            if (str_ends_with($firstNameLower, $ending) && strlen($firstNameLower) > 3) {
                return 'male';
            }
        }

        // Default to neutral if uncertain
        return 'neutral';
    }
}
