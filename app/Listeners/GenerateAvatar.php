<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\ImageGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class GenerateAvatar implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 30;

    /**
     * Create the event listener.
     */
    public function __construct(
        private ImageGenerationService $imageGenerator
    ) {
        //
    }

    /**
     * Handle the event.
     *
     * Generates an avatar for the newly registered user.
     * This runs asynchronously in a queue job, so it doesn't block user registration.
     * If generation fails, logs the error but does not block user registration.
     */
    public function handle(UserRegistered $event): void
    {
        try {
            // Reload user to ensure we have the latest data
            $user = $event->user->fresh();

            // Prevent duplicate avatar generation - if user already has an avatar, skip
            $existingAvatar = $user->getAttributes()['avatar_url'] ?? null;
            if ($existingAvatar) {
                return;
            }

            // Generate avatar prompt in Alien style (technician/ship captain)
            $prompt = $this->generateAvatarPrompt($user->name);

            // Generate avatar image
            $result = $this->imageGenerator->generate($prompt);

            // Store the path instead of full URL for flexibility
            // The URL will be reconstructed dynamically via the model accessor
            $user->update(['avatar_url' => $result['path']]);

            Log::info('Avatar generated successfully', [
                'user_id' => $user->id,
                'avatar_path' => $result['path'],
                'avatar_url' => $result['url'], // Full URL for logging, but path is stored
            ]);
        } catch (\Exception $e) {
            // Log the error but don't block user registration
            Log::error('Failed to generate avatar for user', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to mark job as failed (will be retried according to queue config)
            throw $e;
        }
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
        return "Close-up professional portrait headshot of a single {$character}, {$userName}, "
            . 'a seasoned space technician and ship captain, '
            . 'in the style of Alien (1979) movie aesthetic. '
            . 'Tight framing, head and shoulders only, zoomed in for maximum detail. '
            . 'Industrial sci-fi setting with realistic lighting and cinematic composition. '
            . 'Single person only, wearing a weathered technical jumpsuit with visible patches, '
            . 'insignia, and worn fabric details. '
            . 'Holding a data pad or technical tool in hand, visible in foreground. '
            . 'Facial features: determined expression, weathered skin with subtle scars or marks, '
            . 'professional haircut, focused eyes with slight bags from long shifts. '
            . 'Atmospheric lighting with blue and orange tones creating depth and dimension. '
            . 'Simple, subtle background: dark, muted tones with minimal detail, '
            . 'slightly blurred to emphasize the person. No distracting elements, '
            . 'just a clean, professional backdrop that makes the character stand out. '
            . 'Professional headshot portrait, one person only, no other people in frame, '
            . 'square format (1:1 aspect ratio), highly detailed facial features, '
            . 'photorealistic style with sharp focus on the face, '
            . 'moody and atmospheric, cinematic quality, high resolution, '
            . 'detailed skin texture, realistic shadows and highlights.';
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
        // Common female name endings/patterns
        $femalePatterns = [
            'a',
            'ia',
            'ella',
            'ette',
            'ine',
            'elle',
            'ette',
            'anna',
            'ella',
            'sophia',
            'emma',
            'olivia',
            'isabella',
            'marie',
            'sophie',
            'julie',
            'marie',
            'anne',
            'claire',
            'alex',
            'sam',
            'jordan',
            'riley',
            'taylor',
            'morgan', // Unisex names
        ];

        // Common male name endings/patterns
        $malePatterns = [
            'o',
            'er',
            'on',
            'en',
            'an',
            'el',
            'al',
            'john',
            'james',
            'michael',
            'david',
            'robert',
            'william',
            'alexander',
            'daniel',
            'matthew',
            'christopher',
        ];

        $firstNameLower = strtolower($firstName);

        // Check for unisex names first (these are common in the project)
        $unisexNames = ['alex', 'sam', 'jordan', 'riley', 'taylor', 'morgan', 'casey', 'jamie'];
        if (in_array($firstNameLower, $unisexNames)) {
            // For unisex names, default to neutral/masculine for technician role
            return 'neutral';
        }

        // Check female patterns
        foreach ($femalePatterns as $pattern) {
            if (str_ends_with($firstNameLower, $pattern) || $firstNameLower === $pattern) {
                return 'female';
            }
        }

        // Check male patterns
        foreach ($malePatterns as $pattern) {
            if (str_ends_with($firstNameLower, $pattern) || $firstNameLower === $pattern) {
                return 'male';
            }
        }

        // Default to neutral if uncertain
        return 'neutral';
    }
}
