<?php

namespace App\Services;

use App\Events\ResourceGenerated;
use App\Exceptions\ImageGenerationException;
use App\Exceptions\StorageException;
use App\Exceptions\VideoGenerationException;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Service for generating resource templates (avatars, planet images/videos).
 *
 * This service generates AI resources and stores them as templates that require
 * super admin approval before being available for use in the game.
 */
class ResourceGenerationService
{
    public function __construct(
        private ImageGenerationService $imageGenerator,
        private VideoGenerationService $videoGenerator
    ) {
        //
    }

    /**
     * Generate an avatar template resource.
     *
     * @param  string  $prompt  The prompt for image generation
     * @param  array|null  $tags  Tags for categorization
     * @param  string|null  $description  Optional description
     * @param  User|null  $createdBy  The admin user creating this resource (null for automatic generation)
     * @return resource The created resource
     *
     * @throws ImageGenerationException
     * @throws StorageException
     */
    public function generateAvatarTemplate(
        string $prompt,
        ?array $tags = null,
        ?string $description = null,
        ?User $createdBy = null
    ): Resource {
        try {
            // Extract avatar tags from prompt and merge with provided tags
            $extractedTags = $this->extractAvatarTagsFromPrompt($prompt);
            $mergedTags = array_unique(array_merge($extractedTags, $tags ?? []));

            // Generate avatar image in 'avatars' subfolder
            $result = $this->imageGenerator->generate($prompt, null, 'avatars');

            // Create resource record
            $resource = Resource::create([
                'type' => 'avatar_image',
                'status' => 'pending',
                'file_path' => $result['path'],
                'prompt' => $prompt,
                'tags' => $mergedTags,
                'description' => $description,
                'metadata' => [
                    'provider' => $result['provider'] ?? null,
                    'generated_at' => now()->toIso8601String(),
                ],
                'created_by' => $createdBy?->id,
            ]);

            Log::info('Avatar template resource generated', [
                'resource_id' => $resource->id,
                'prompt' => $prompt,
                'file_path' => $result['path'],
            ]);

            // Dispatch event
            event(new ResourceGenerated($resource));

            return $resource;
        } catch (ImageGenerationException|StorageException $e) {
            Log::error('Failed to generate avatar template resource', [
                'prompt' => $prompt,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate a planet image template resource.
     *
     * @param  string  $prompt  The prompt for image generation
     * @param  array|null  $tags  Tags for categorization
     * @param  string|null  $description  Optional description
     * @param  User|null  $createdBy  The admin user creating this resource (null for automatic generation)
     * @return resource The created resource
     *
     * @throws ImageGenerationException
     * @throws StorageException
     */
    public function generatePlanetImageTemplate(
        string $prompt,
        ?array $tags = null,
        ?string $description = null,
        ?User $createdBy = null
    ): Resource {
        try {
            // Extract planet tags from prompt and merge with provided tags
            $extractedTags = $this->extractPlanetTagsFromPrompt($prompt);
            $mergedTags = array_unique(array_merge($extractedTags, $tags ?? []));

            // Generate planet image in 'planets' subfolder
            $result = $this->imageGenerator->generate($prompt, null, 'planets');

            // Create resource record
            $resource = Resource::create([
                'type' => 'planet_image',
                'status' => 'pending',
                'file_path' => $result['path'],
                'prompt' => $prompt,
                'tags' => $mergedTags,
                'description' => $description,
                'metadata' => [
                    'provider' => $result['provider'] ?? null,
                    'generated_at' => now()->toIso8601String(),
                ],
                'created_by' => $createdBy?->id,
            ]);

            Log::info('Planet image template resource generated', [
                'resource_id' => $resource->id,
                'prompt' => $prompt,
                'file_path' => $result['path'],
            ]);

            // Dispatch event
            event(new ResourceGenerated($resource));

            return $resource;
        } catch (ImageGenerationException|StorageException $e) {
            Log::error('Failed to generate planet image template resource', [
                'prompt' => $prompt,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate a planet video template resource.
     *
     * @param  string  $prompt  The prompt for video generation
     * @param  array|null  $tags  Tags for categorization
     * @param  string|null  $description  Optional description
     * @param  User|null  $createdBy  The admin user creating this resource (null for automatic generation)
     * @return resource The created resource
     *
     * @throws VideoGenerationException
     * @throws StorageException
     */
    public function generatePlanetVideoTemplate(
        string $prompt,
        ?array $tags = null,
        ?string $description = null,
        ?User $createdBy = null
    ): Resource {
        try {
            // Extract planet tags from prompt and merge with provided tags
            $extractedTags = $this->extractPlanetTagsFromPrompt($prompt);
            $mergedTags = array_unique(array_merge($extractedTags, $tags ?? []));

            // Generate planet video in 'planets' subfolder
            $result = $this->videoGenerator->generate($prompt, null, 'planets');

            // Create resource record
            $resource = Resource::create([
                'type' => 'planet_video',
                'status' => 'pending',
                'file_path' => $result['path'],
                'prompt' => $prompt,
                'tags' => $mergedTags,
                'description' => $description,
                'metadata' => [
                    'provider' => $result['provider'] ?? null,
                    'generated_at' => now()->toIso8601String(),
                ],
                'created_by' => $createdBy?->id,
            ]);

            Log::info('Planet video template resource generated', [
                'resource_id' => $resource->id,
                'prompt' => $prompt,
                'file_path' => $result['path'],
            ]);

            // Dispatch event
            event(new ResourceGenerated($resource));

            return $resource;
        } catch (VideoGenerationException|StorageException $e) {
            Log::error('Failed to generate planet video template resource', [
                'prompt' => $prompt,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate an avatar template for an existing resource.
     *
     * @param  resource  $resource  The existing resource to update
     * @return resource The updated resource
     *
     * @throws ImageGenerationException
     * @throws StorageException
     */
    public function generateAvatarTemplateForResource(Resource $resource): Resource
    {
        try {
            // Generate avatar image in 'avatars' subfolder
            $result = $this->imageGenerator->generate($resource->prompt, null, 'avatars');

            // Update resource record
            $resource->update([
                'status' => 'pending',
                'file_path' => $result['path'],
                'metadata' => array_merge($resource->metadata ?? [], [
                    'provider' => $result['provider'] ?? null,
                    'generated_at' => now()->toIso8601String(),
                ]),
            ]);

            Log::info('Avatar template resource generated for existing resource', [
                'resource_id' => $resource->id,
                'prompt' => $resource->prompt,
                'file_path' => $result['path'],
            ]);

            // Dispatch event
            event(new ResourceGenerated($resource));

            return $resource;
        } catch (ImageGenerationException|StorageException $e) {
            Log::error('Failed to generate avatar template for existing resource', [
                'resource_id' => $resource->id,
                'prompt' => $resource->prompt,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate a planet image template for an existing resource.
     *
     * @param  resource  $resource  The existing resource to update
     * @return resource The updated resource
     *
     * @throws ImageGenerationException
     * @throws StorageException
     */
    public function generatePlanetImageTemplateForResource(Resource $resource): Resource
    {
        try {
            // Extract planet tags from prompt and merge with existing tags
            $extractedTags = $this->extractPlanetTagsFromPrompt($resource->prompt);
            $mergedTags = array_unique(array_merge($extractedTags, $resource->tags ?? []));

            // Generate planet image in 'planets' subfolder
            $result = $this->imageGenerator->generate($resource->prompt, null, 'planets');

            // Update resource record
            $resource->update([
                'status' => 'pending',
                'file_path' => $result['path'],
                'tags' => $mergedTags,
                'metadata' => array_merge($resource->metadata ?? [], [
                    'provider' => $result['provider'] ?? null,
                    'generated_at' => now()->toIso8601String(),
                ]),
            ]);

            Log::info('Planet image template resource generated for existing resource', [
                'resource_id' => $resource->id,
                'prompt' => $resource->prompt,
                'file_path' => $result['path'],
            ]);

            // Dispatch event
            event(new ResourceGenerated($resource));

            return $resource;
        } catch (ImageGenerationException|StorageException $e) {
            Log::error('Failed to generate planet image template for existing resource', [
                'resource_id' => $resource->id,
                'prompt' => $resource->prompt,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate a planet video template for an existing resource.
     *
     * @param  resource  $resource  The existing resource to update
     * @return resource The updated resource
     *
     * @throws VideoGenerationException
     * @throws StorageException
     */
    public function generatePlanetVideoTemplateForResource(Resource $resource): Resource
    {
        try {
            // Extract planet tags from prompt and merge with existing tags
            $extractedTags = $this->extractPlanetTagsFromPrompt($resource->prompt);
            $mergedTags = array_unique(array_merge($extractedTags, $resource->tags ?? []));

            // Generate planet video in 'planets' subfolder
            $result = $this->videoGenerator->generate($resource->prompt, null, 'planets');

            // Update resource record
            $resource->update([
                'status' => 'pending',
                'file_path' => $result['path'],
                'tags' => $mergedTags,
                'metadata' => array_merge($resource->metadata ?? [], [
                    'provider' => $result['provider'] ?? null,
                    'generated_at' => now()->toIso8601String(),
                ]),
            ]);

            Log::info('Planet video template resource generated for existing resource', [
                'resource_id' => $resource->id,
                'prompt' => $resource->prompt,
                'file_path' => $result['path'],
            ]);

            // Dispatch event
            event(new ResourceGenerated($resource));

            return $resource;
        } catch (VideoGenerationException|StorageException $e) {
            Log::error('Failed to generate planet video template for existing resource', [
                'resource_id' => $resource->id,
                'prompt' => $resource->prompt,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Extract planet characteristic tags from a prompt.
     *
     * Analyzes the prompt text to identify planet characteristics and returns
     * corresponding tags that can be used for resource matching.
     *
     * @param  string  $prompt  The prompt text to analyze
     * @return array<string> Array of normalized tags (lowercase)
     */
    public function extractPlanetTagsFromPrompt(string $prompt): array
    {
        $tags = [];
        $promptLower = strtolower($prompt);

        // Map keywords to planet types
        $typeKeywords = [
            'tellurique' => ['rocky', 'terrestrial', 'earth-like', 'solid surface', 'craters', 'mountains'],
            'gazeuse' => ['gas giant', 'gas giant planet', 'swirling clouds', 'atmospheric bands', 'gaseous'],
            'glacée' => ['ice', 'ice-covered', 'frozen', 'glaciers', 'ice planet', 'frozen oceans', 'icy'],
            'désertique' => ['desert', 'sand dunes', 'dunes', 'barren', 'arid', 'dry'],
            'océanique' => ['ocean', 'ocean planet', 'water', 'deep blue waters', 'aquatic'],
        ];

        // Map keywords to sizes
        $sizeKeywords = [
            'petite' => ['small', 'tiny', 'compact', 'little'],
            'moyenne' => ['medium', 'medium-sized', 'moderate'],
            'grande' => ['massive', 'large', 'huge', 'giant', 'big'],
        ];

        // Map keywords to temperatures
        $temperatureKeywords = [
            'froide' => ['cold', 'icy', 'frozen', 'freezing', 'chilly', 'cool'],
            'tempérée' => ['temperate', 'moderate', 'mild', 'balanced'],
            'chaude' => ['hot', 'scorching', 'burning', 'warm', 'heated', 'fiery'],
        ];

        // Map keywords to atmospheres
        $atmosphereKeywords = [
            'respirable' => ['breathable', 'clear atmosphere', 'blue atmospheric', 'white cloud'],
            'toxique' => ['toxic', 'poisonous', 'hazardous', 'deadly atmosphere'],
            'inexistante' => ['airless', 'no atmosphere', 'void', 'vacuum'],
        ];

        // Map keywords to terrains
        $terrainKeywords = [
            'rocheux' => ['rocky', 'rocks', 'stone', 'craters', 'mountains', 'canyons'],
            'océanique' => ['ocean', 'water', 'sea', 'aquatic', 'marine'],
            'désertique' => ['desert', 'sand', 'dunes', 'arid', 'barren'],
            'forestier' => ['forest', 'trees', 'vegetation', 'green', 'woodland'],
            'urbain' => ['urban', 'city', 'structures', 'buildings', 'industrial'],
            'mixte' => ['mixed', 'diverse', 'varied', 'multiple biomes'],
            'glacé' => ['ice', 'frozen', 'glaciers', 'snow', 'icy'],
        ];

        // Extract planet type
        foreach ($typeKeywords as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($promptLower, $keyword)) {
                    $tags[] = $type;
                    break; // Only add type once
                }
            }
        }

        // Extract size
        foreach ($sizeKeywords as $size => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($promptLower, $keyword)) {
                    $tags[] = $size;
                    break;
                }
            }
        }

        // Extract temperature
        foreach ($temperatureKeywords as $temperature => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($promptLower, $keyword)) {
                    $tags[] = $temperature;
                    break;
                }
            }
        }

        // Extract atmosphere
        foreach ($atmosphereKeywords as $atmosphere => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($promptLower, $keyword)) {
                    $tags[] = $atmosphere;
                    break;
                }
            }
        }

        // Extract terrain
        foreach ($terrainKeywords as $terrain => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($promptLower, $keyword)) {
                    $tags[] = $terrain;
                    break;
                }
            }
        }

        // Normalize and return unique tags
        return array_values(array_unique(array_map('strtolower', $tags)));
    }

    /**
     * Extract avatar gender tags from a prompt.
     *
     * Analyzes the prompt text to identify gender characteristics (man/woman)
     * based on name detection or explicit mentions in the prompt.
     *
     * @param  string  $prompt  The prompt text to analyze
     * @return array<string> Array of normalized tags (lowercase): 'man' or 'woman'
     */
    public function extractAvatarTagsFromPrompt(string $prompt): array
    {
        $tags = [];
        $promptLower = strtolower($prompt);

        // First, check for explicit mentions of "man" or "woman" in the prompt
        if (preg_match('/\b(man|men|male|gentleman)\b/', $promptLower)) {
            $tags[] = 'man';
        } elseif (preg_match('/\b(woman|women|female|lady)\b/', $promptLower)) {
            $tags[] = 'woman';
        } else {
            // Try to extract a name from the prompt and detect gender
            $name = $this->extractNameFromPrompt($prompt);
            if ($name) {
                $gender = $this->detectGenderFromName($name);
                if ($gender === 'male') {
                    $tags[] = 'man';
                } elseif ($gender === 'female') {
                    $tags[] = 'woman';
                }
            }
        }

        // Normalize and return unique tags
        return array_values(array_unique(array_map('strtolower', $tags)));
    }

    /**
     * Extract a name from the prompt.
     *
     * Looks for common name patterns in the prompt.
     *
     * @param  string  $prompt  The prompt text
     * @return string|null The extracted name or null if not found
     */
    private function extractNameFromPrompt(string $prompt): ?string
    {
        // Common patterns: "of a single {character}, {Name}," or "{Name}, a seasoned"
        // Try to find a capitalized word that looks like a name
        if (preg_match('/\b([A-Z][a-z]+(?:\s+[A-Z][a-z]+)?),?\s+(?:a|an|the|is|was|wearing|holding)/', $prompt, $matches)) {
            return trim($matches[1]);
        }

        // Try to find name after "of a single {character},"
        if (preg_match('/of\s+a\s+(?:single\s+)?(?:man|woman|person|character),?\s+([A-Z][a-z]+(?:\s+[A-Z][a-z]+)?)/', $prompt, $matches)) {
            return trim($matches[1]);
        }

        // Try to find capitalized words that might be names
        if (preg_match('/\b([A-Z][a-z]{2,})\b/', $prompt, $matches)) {
            $potentialName = $matches[1];
            // Filter out common words that are capitalized but not names
            $commonWords = ['Close', 'Professional', 'Portrait', 'Headshot', 'Single', 'Space', 'Technical', 'Industrial', 'Cinematic', 'Atmospheric', 'Alien', 'Square', 'Format', 'High', 'Resolution', 'Detailed', 'Photorealistic', 'Style', 'Sharp', 'Focus', 'Face', 'Moody', 'Quality', 'Texture', 'Shadows', 'Highlights'];
            if (! in_array($potentialName, $commonWords)) {
                return $potentialName;
            }
        }

        return null;
    }

    /**
     * Detect gender based on first name (simple heuristic).
     *
     * Uses the same logic as GenerateAvatar listener.
     *
     * @param  string  $name  The name to analyze
     * @return string 'male', 'female', or 'neutral'
     */
    private function detectGenderFromName(string $name): string
    {
        // Extract first name if multiple words
        $parts = explode(' ', trim($name));
        $firstName = $parts[0] ?? $name;

        // Common female name endings/patterns
        $femalePatterns = [
            'a',
            'ia',
            'ella',
            'ette',
            'ine',
            'elle',
            'anna',
            'sophia',
            'emma',
            'olivia',
            'isabella',
            'marie',
            'sophie',
            'julie',
            'anne',
            'claire',
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

        // Check for unisex names first
        $unisexNames = ['alex', 'sam', 'jordan', 'riley', 'taylor', 'morgan', 'casey', 'jamie'];
        if (in_array($firstNameLower, $unisexNames)) {
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
