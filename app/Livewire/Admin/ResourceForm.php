<?php

namespace App\Livewire\Admin;

use App\Services\ResourceGenerationService;
use Livewire\Component;

class ResourceForm extends Component
{
    public $type = '';

    public $prompt = '';

    public $tags = '';

    public $description = '';

    public $autoExtractedTags = '';

    public $promptSuggestions = [
        'avatar_image' => [
            'Close-up professional portrait headshot of a space technician, weathered and experienced, in the style of Alien (1979) movie aesthetic. Industrial sci-fi setting, realistic lighting, cinematic composition. Single person only, no other people in frame, square format (1:1 aspect ratio), highly detailed, photorealistic style with sharp focus on the face.',
            'Professional headshot of a ship captain, determined expression, wearing a technical jumpsuit with patches and insignia. Atmospheric lighting with blue and orange tones, photorealistic style. Single person only, square format (1:1 aspect ratio), highly detailed facial features.',
            'Portrait of a space explorer, focused eyes, weathered skin with subtle scars, professional haircut. Dark, muted background, highly detailed facial features, moody and atmospheric. Single person only, square format (1:1 aspect ratio), photorealistic style.',
            'Close-up of a space engineer, holding a data pad, wearing a weathered jumpsuit. Industrial setting, realistic shadows and highlights, square format (1:1 aspect ratio). Single person only, highly detailed, photorealistic style.',
            'Professional portrait of a space pilot, determined expression, technical uniform visible. Cinematic quality, high resolution, detailed skin texture, atmospheric lighting. Single person only, square format (1:1 aspect ratio), photorealistic style.',
        ],
        'planet_image' => [
            'Cinematic space view of a massive gas giant planet with swirling clouds of orange and red, visible from space, detailed atmospheric bands, dramatic lighting, cinematic composition. High resolution, photorealistic style, detailed surface features, realistic space environment, cinematic quality, wide format (16:9 aspect ratio).',
            'A rocky terrestrial planet with craters and mountains, seen from orbit, Earth-like but alien, detailed surface features, realistic space environment. High resolution, photorealistic style, detailed terrain, dramatic shadows, wide format (16:9 aspect ratio), professional space photography aesthetic.',
            'An ice-covered planet with frozen oceans and glaciers, blue and white tones, seen from space, detailed surface textures, atmospheric perspective. High resolution, photorealistic style, detailed ice formations, realistic space environment, cinematic quality, wide format (16:9 aspect ratio).',
            'A desert planet with vast sand dunes and rocky formations, warm orange and yellow tones, seen from orbit, detailed terrain, dramatic shadows. High resolution, photorealistic style, detailed surface features, realistic space environment, cinematic quality, wide format (16:9 aspect ratio).',
            'An ocean planet with deep blue waters and scattered islands, seen from space, realistic water reflections, detailed cloud formations, atmospheric lighting. High resolution, photorealistic style, detailed surface features, realistic space environment, cinematic quality, wide format (16:9 aspect ratio).',
        ],
        'planet_video' => [
            'Slow cinematic pan around a gas giant planet with swirling clouds, atmospheric bands moving, dramatic lighting, space environment, 10 seconds, smooth camera movement. High resolution, photorealistic style, smooth motion, cinematic quality.',
            'Orbital view of a rocky planet rotating slowly, surface details visible, realistic space environment, dramatic lighting, 10 seconds, cinematic composition. High resolution, photorealistic style, smooth motion, cinematic quality.',
            'Slow zoom into an ice-covered planet, frozen surface details becoming visible, blue and white tones, space environment, 10 seconds, atmospheric perspective. High resolution, photorealistic style, smooth motion, cinematic quality.',
            'Pan across a desert planet surface, sand dunes and rocky formations, warm tones, dramatic shadows moving, 10 seconds, cinematic quality. High resolution, photorealistic style, smooth motion, cinematic quality.',
            'Slow rotation of an ocean planet, water reflections and cloud formations moving, deep blue tones, realistic space environment, 10 seconds, atmospheric lighting. High resolution, photorealistic style, smooth motion, cinematic quality.',
        ],
    ];

    public function mount()
    {
        // Initialize with old input if available
        $this->type = old('type', '');
        $this->prompt = old('prompt', '');
        $this->tags = old('tags', '');
        $this->description = old('description', '');
    }

    public function updatedType()
    {
        // Reset prompt when type changes (optional)
        // $this->prompt = '';
        // Reset auto-extracted tags when type changes
        $this->autoExtractedTags = '';
        $this->updateTagsFromPrompt();
    }

    public function updatedPrompt()
    {
        $this->updateTagsFromPrompt();
    }

    /**
     * Update tags field with auto-extracted tags from prompt.
     */
    private function updateTagsFromPrompt(): void
    {
        if (empty($this->prompt)) {
            $this->autoExtractedTags = '';

            return;
        }

        $resourceGenerator = app(ResourceGenerationService::class);
        $extractedTags = [];

        // Extract tags based on resource type
        if (in_array($this->type, ['planet_image', 'planet_video'])) {
            $extractedTags = $resourceGenerator->extractPlanetTagsFromPrompt($this->prompt);
        } elseif ($this->type === 'avatar_image') {
            $extractedTags = $resourceGenerator->extractAvatarTagsFromPrompt($this->prompt);
        } else {
            $this->autoExtractedTags = '';

            return;
        }

        // Update auto-extracted tags display
        $this->autoExtractedTags = implode(', ', $extractedTags);

        // If user hasn't manually entered tags, auto-fill with extracted tags
        if (empty($this->tags)) {
            $this->tags = $this->autoExtractedTags;
        } else {
            // Merge manual tags with extracted tags
            $manualTags = array_map('trim', explode(',', $this->tags));
            $allTags = array_unique(array_merge($extractedTags, $manualTags));
            $this->tags = implode(', ', $allTags);
        }
    }

    public function useSuggestion($index)
    {
        if (isset($this->promptSuggestions[$this->type][$index])) {
            $this->prompt = $this->promptSuggestions[$this->type][$index];
            // Auto-extract tags when using a suggestion
            $this->updateTagsFromPrompt();
        }
    }

    public function getSuggestionsProperty()
    {
        if (empty($this->type) || ! isset($this->promptSuggestions[$this->type])) {
            return [];
        }

        return $this->promptSuggestions[$this->type];
    }

    public function render()
    {
        return view('livewire.admin.resource-form');
    }
}
