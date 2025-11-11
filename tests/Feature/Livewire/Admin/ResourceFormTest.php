<?php

use App\Models\User;
use App\Services\ResourceGenerationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'email' => 'admin@example.com',
        'is_super_admin' => true,
    ]);

    // Set empty whitelist for tests
    config(['admin.email_whitelist' => '']);

    Auth::guard('admin')->login($this->admin);
});

it('renders resource form component successfully', function () {
    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->assertStatus(200);
});

it('initializes with empty fields on mount', function () {
    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->assertSet('type', '')
        ->assertSet('prompt', '')
        ->assertSet('tags', '')
        ->assertSet('description', '')
        ->assertSet('autoExtractedTags', '');
});

it('can set and retrieve form values', function () {
    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'avatar_image')
        ->set('prompt', 'Test prompt')
        ->set('tags', 'man, portrait')
        ->set('description', 'Test description')
        ->assertSet('type', 'avatar_image')
        ->assertSet('prompt', 'Test prompt')
        ->assertSet('tags', 'man, portrait')
        ->assertSet('description', 'Test description');
});

it('resets auto-extracted tags when type changes', function () {
    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'avatar_image')
        ->set('prompt', 'A portrait of a man')
        ->set('autoExtractedTags', 'man')
        ->set('type', 'planet_image')
        ->assertSet('autoExtractedTags', '');
});

it('extracts avatar tags from prompt', function () {
    $mockService = \Mockery::mock(ResourceGenerationService::class);
    $mockService->shouldReceive('extractAvatarTagsFromPrompt')
        ->with('A portrait of a man')
        ->andReturn(['man']);

    $this->app->instance(ResourceGenerationService::class, $mockService);

    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'avatar_image')
        ->set('prompt', 'A portrait of a man')
        ->assertSet('autoExtractedTags', 'man');
});

it('extracts planet tags from prompt for planet_image type', function () {
    $mockService = \Mockery::mock(ResourceGenerationService::class);
    $mockService->shouldReceive('extractPlanetTagsFromPrompt')
        ->with('A rocky planet with craters')
        ->andReturn(['tellurique', 'rocheux']);

    $this->app->instance(ResourceGenerationService::class, $mockService);

    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'planet_image')
        ->set('prompt', 'A rocky planet with craters')
        ->assertSet('autoExtractedTags', 'tellurique, rocheux');
});

it('extracts planet tags from prompt for planet_video type', function () {
    $mockService = \Mockery::mock(ResourceGenerationService::class);
    $mockService->shouldReceive('extractPlanetTagsFromPrompt')
        ->with('A gas giant with swirling clouds')
        ->andReturn(['gazeuse']);

    $this->app->instance(ResourceGenerationService::class, $mockService);

    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'planet_video')
        ->set('prompt', 'A gas giant with swirling clouds')
        ->assertSet('autoExtractedTags', 'gazeuse');
});

it('auto-fills tags when user has not entered any', function () {
    $mockService = \Mockery::mock(ResourceGenerationService::class);
    $mockService->shouldReceive('extractAvatarTagsFromPrompt')
        ->with('A portrait of a man')
        ->andReturn(['man']);

    $this->app->instance(ResourceGenerationService::class, $mockService);

    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'avatar_image')
        ->set('prompt', 'A portrait of a man')
        ->assertSet('tags', 'man');
});

it('merges extracted tags with manual tags', function () {
    $mockService = \Mockery::mock(ResourceGenerationService::class);
    $mockService->shouldReceive('extractAvatarTagsFromPrompt')
        ->with('A portrait of a man')
        ->andReturn(['man']);

    $this->app->instance(ResourceGenerationService::class, $mockService);

    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'avatar_image')
        ->set('tags', 'portrait, professional')
        ->set('prompt', 'A portrait of a man')
        ->assertSet('tags', function ($tags) {
            $tagArray = array_map('trim', explode(',', $tags));

            return in_array('man', $tagArray)
                && in_array('portrait', $tagArray)
                && in_array('professional', $tagArray);
        });
});

it('clears auto-extracted tags when prompt is empty', function () {
    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'avatar_image')
        ->set('prompt', 'A portrait of a man')
        ->set('prompt', '')
        ->assertSet('autoExtractedTags', '');
});

it('does not extract tags for unknown resource type', function () {
    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'unknown_type')
        ->set('prompt', 'Some prompt')
        ->assertSet('autoExtractedTags', '');
});

it('provides suggestions for avatar_image type', function () {
    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'avatar_image')
        ->assertSet('suggestions', function ($suggestions) {
            return is_array($suggestions) && count($suggestions) === 5;
        });
});

it('provides suggestions for planet_image type', function () {
    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'planet_image')
        ->assertSet('suggestions', function ($suggestions) {
            return is_array($suggestions) && count($suggestions) === 5;
        });
});

it('provides suggestions for planet_video type', function () {
    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'planet_video')
        ->assertSet('suggestions', function ($suggestions) {
            return is_array($suggestions) && count($suggestions) === 5;
        });
});

it('returns empty suggestions for unknown type', function () {
    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'unknown_type')
        ->assertSet('suggestions', []);
});

it('uses suggestion when provided', function () {
    $mockService = \Mockery::mock(ResourceGenerationService::class);
    $mockService->shouldReceive('extractAvatarTagsFromPrompt')
        ->andReturn(['man']);

    $this->app->instance(ResourceGenerationService::class, $mockService);

    $component = Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'avatar_image');

    $suggestions = $component->get('suggestions');
    $firstSuggestion = $suggestions[0] ?? null;

    if ($firstSuggestion) {
        $component->call('useSuggestion', 0)
            ->assertSet('prompt', $firstSuggestion);
    }
});

it('does not use suggestion for invalid index', function () {
    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'avatar_image')
        ->set('prompt', 'Original prompt')
        ->call('useSuggestion', 999)
        ->assertSet('prompt', 'Original prompt');
});

it('does not use suggestion when type is not set', function () {
    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('prompt', 'Original prompt')
        ->call('useSuggestion', 0)
        ->assertSet('prompt', 'Original prompt');
});

it('updates tags when prompt changes', function () {
    $mockService = \Mockery::mock(ResourceGenerationService::class);
    $mockService->shouldReceive('extractAvatarTagsFromPrompt')
        ->with('A portrait of a man')
        ->andReturn(['man']);

    $this->app->instance(ResourceGenerationService::class, $mockService);

    Livewire::test(\App\Livewire\Admin\ResourceForm::class)
        ->set('type', 'avatar_image')
        ->set('prompt', 'A portrait of a man')
        ->assertSet('tags', 'man');
});
