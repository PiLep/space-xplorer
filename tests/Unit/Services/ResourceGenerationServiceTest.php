<?php

use App\Events\ResourceGenerated;
use App\Exceptions\ImageGenerationException;
use App\Exceptions\StorageException;
use App\Exceptions\VideoGenerationException;
use App\Models\Resource;
use App\Models\User;
use App\Services\ImageGenerationService;
use App\Services\ResourceGenerationService;
use App\Services\VideoGenerationService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
    Event::fake();

    $this->imageGenerator = Mockery::mock(ImageGenerationService::class);
    $this->videoGenerator = Mockery::mock(VideoGenerationService::class);
    $this->service = new ResourceGenerationService(
        $this->imageGenerator,
        $this->videoGenerator
    );
});

describe('generateAvatarTemplate', function () {
    it('generates avatar template successfully', function () {
        $prompt = 'A portrait of a man named John';
        $tags = ['casual', 'test'];
        $description = 'Test avatar';
        $user = User::factory()->create();

        $result = [
            'url' => 'https://s3.example.com/avatars/avatar.png',
            'path' => 'avatars/avatar.png',
            'disk' => 's3',
            'provider' => 'openai',
        ];

        $this->imageGenerator
            ->shouldReceive('generate')
            ->once()
            ->with($prompt, null, 'avatars')
            ->andReturn($result);

        Log::shouldReceive('info')->once();

        $resource = $this->service->generateAvatarTemplate(
            $prompt,
            $tags,
            $description,
            $user
        );

        expect($resource)
            ->toBeInstanceOf(Resource::class)
            ->and($resource->type)->toBe('avatar_image')
            ->and($resource->status)->toBe('pending')
            ->and($resource->file_path)->toBe($result['path'])
            ->and($resource->prompt)->toBe($prompt)
            ->and($resource->description)->toBe($description)
            ->and($resource->created_by)->toBe($user->id)
            ->and($resource->tags)->toBeArray()
            ->and($resource->tags)->toContain('man', 'casual', 'test')
            ->and($resource->metadata)->toHaveKey('provider')
            ->and($resource->metadata)->toHaveKey('generated_at');

        Event::assertDispatched(ResourceGenerated::class, function ($event) use ($resource) {
            return $event->resource->id === $resource->id;
        });
    });

    it('generates avatar template without optional parameters', function () {
        $prompt = 'A portrait of a woman';

        $result = [
            'url' => 'https://s3.example.com/avatars/avatar.png',
            'path' => 'avatars/avatar.png',
            'disk' => 's3',
            'provider' => 'openai',
        ];

        $this->imageGenerator
            ->shouldReceive('generate')
            ->once()
            ->with($prompt, null, 'avatars')
            ->andReturn($result);

        Log::shouldReceive('info')->once();

        $resource = $this->service->generateAvatarTemplate($prompt);

        expect($resource)
            ->toBeInstanceOf(Resource::class)
            ->and($resource->type)->toBe('avatar_image')
            ->and($resource->status)->toBe('pending')
            ->and($resource->prompt)->toBe($prompt)
            ->and($resource->description)->toBeNull()
            ->and($resource->created_by)->toBeNull()
            ->and($resource->tags)->toBeArray()
            ->and($resource->tags)->toContain('woman');
    });

    it('merges extracted tags with provided tags', function () {
        $prompt = 'A portrait of a man named John';
        $providedTags = ['casual', 'man']; // 'man' should be deduplicated

        $result = [
            'url' => 'https://s3.example.com/avatars/avatar.png',
            'path' => 'avatars/avatar.png',
            'disk' => 's3',
            'provider' => 'openai',
        ];

        $this->imageGenerator
            ->shouldReceive('generate')
            ->once()
            ->andReturn($result);

        Log::shouldReceive('info')->once();

        $resource = $this->service->generateAvatarTemplate($prompt, $providedTags);

        // Should contain 'man' only once (from extraction and provided)
        expect($resource->tags)->toContain('man', 'casual')
            ->and(count($resource->tags))->toBe(2); // No duplicates
    });

    it('throws ImageGenerationException on image generation failure', function () {
        $prompt = 'A portrait';

        $this->imageGenerator
            ->shouldReceive('generate')
            ->once()
            ->andThrow(new ImageGenerationException('API error'));

        Log::shouldReceive('error')->once();

        expect(fn () => $this->service->generateAvatarTemplate($prompt))
            ->toThrow(ImageGenerationException::class, 'API error');

        Event::assertNothingDispatched();
    });

    it('throws StorageException on storage failure', function () {
        $prompt = 'A portrait';

        $this->imageGenerator
            ->shouldReceive('generate')
            ->once()
            ->andThrow(new StorageException('Storage error'));

        Log::shouldReceive('error')->once();

        expect(fn () => $this->service->generateAvatarTemplate($prompt))
            ->toThrow(StorageException::class, 'Storage error');

        Event::assertNothingDispatched();
    });
});

describe('generatePlanetImageTemplate', function () {
    it('generates planet image template successfully', function () {
        $prompt = 'A rocky terrestrial planet with cold temperature';
        $tags = ['test'];
        $description = 'Test planet';
        $user = User::factory()->create();

        $result = [
            'url' => 'https://s3.example.com/planets/planet.png',
            'path' => 'planets/planet.png',
            'disk' => 's3',
            'provider' => 'openai',
        ];

        $this->imageGenerator
            ->shouldReceive('generate')
            ->once()
            ->with($prompt, null, 'planets')
            ->andReturn($result);

        Log::shouldReceive('info')->once();

        $resource = $this->service->generatePlanetImageTemplate(
            $prompt,
            $tags,
            $description,
            $user
        );

        expect($resource)
            ->toBeInstanceOf(Resource::class)
            ->and($resource->type)->toBe('planet_image')
            ->and($resource->status)->toBe('pending')
            ->and($resource->file_path)->toBe($result['path'])
            ->and($resource->prompt)->toBe($prompt)
            ->and($resource->description)->toBe($description)
            ->and($resource->created_by)->toBe($user->id)
            ->and($resource->tags)->toBeArray()
            ->and($resource->tags)->toContain('tellurique', 'froide', 'test');

        Event::assertDispatched(ResourceGenerated::class);
    });

    it('extracts planet tags from prompt', function () {
        $prompt = 'A massive gas giant planet with hot temperature and toxic atmosphere';

        $result = [
            'url' => 'https://s3.example.com/planets/planet.png',
            'path' => 'planets/planet.png',
            'disk' => 's3',
            'provider' => 'openai',
        ];

        $this->imageGenerator
            ->shouldReceive('generate')
            ->once()
            ->andReturn($result);

        Log::shouldReceive('info')->once();

        $resource = $this->service->generatePlanetImageTemplate($prompt);

        expect($resource->tags)->toContain('gazeuse', 'grande', 'chaude', 'toxique');
    });

    it('throws ImageGenerationException on failure', function () {
        $prompt = 'A planet';

        $this->imageGenerator
            ->shouldReceive('generate')
            ->once()
            ->andThrow(new ImageGenerationException('API error'));

        Log::shouldReceive('error')->once();

        expect(fn () => $this->service->generatePlanetImageTemplate($prompt))
            ->toThrow(ImageGenerationException::class);
    });
});

describe('generatePlanetVideoTemplate', function () {
    it('generates planet video template successfully', function () {
        $prompt = 'A rocky planet with temperate climate';
        $tags = ['test'];
        $description = 'Test video';
        $user = User::factory()->create();

        $result = [
            'url' => 'https://s3.example.com/planets/video.mp4',
            'path' => 'planets/video.mp4',
            'disk' => 's3',
            'provider' => 'openai',
        ];

        $this->videoGenerator
            ->shouldReceive('generate')
            ->once()
            ->with($prompt, null, 'planets')
            ->andReturn($result);

        Log::shouldReceive('info')->once();

        $resource = $this->service->generatePlanetVideoTemplate(
            $prompt,
            $tags,
            $description,
            $user
        );

        expect($resource)
            ->toBeInstanceOf(Resource::class)
            ->and($resource->type)->toBe('planet_video')
            ->and($resource->status)->toBe('pending')
            ->and($resource->file_path)->toBe($result['path'])
            ->and($resource->prompt)->toBe($prompt)
            ->and($resource->description)->toBe($description)
            ->and($resource->created_by)->toBe($user->id)
            ->and($resource->tags)->toBeArray()
            ->and($resource->tags)->toContain('tellurique', 'tempérée', 'test');

        Event::assertDispatched(ResourceGenerated::class);
    });

    it('throws VideoGenerationException on failure', function () {
        $prompt = 'A planet';

        $this->videoGenerator
            ->shouldReceive('generate')
            ->once()
            ->andThrow(new VideoGenerationException('Video API error'));

        Log::shouldReceive('error')->once();

        expect(fn () => $this->service->generatePlanetVideoTemplate($prompt))
            ->toThrow(VideoGenerationException::class, 'Video API error');
    });

    it('throws StorageException on storage failure', function () {
        $prompt = 'A planet';

        $this->videoGenerator
            ->shouldReceive('generate')
            ->once()
            ->andThrow(new StorageException('Storage error'));

        Log::shouldReceive('error')->once();

        expect(fn () => $this->service->generatePlanetVideoTemplate($prompt))
            ->toThrow(StorageException::class);
    });
});

describe('generateAvatarTemplateForResource', function () {
    it('generates avatar template for existing resource', function () {
        $resource = Resource::factory()->create([
            'type' => 'avatar_image',
            'prompt' => 'A portrait of a man',
            'status' => 'approved',
            'metadata' => ['existing' => 'data'],
        ]);

        $result = [
            'url' => 'https://s3.example.com/avatars/new-avatar.png',
            'path' => 'avatars/new-avatar.png',
            'disk' => 's3',
            'provider' => 'openai',
        ];

        $this->imageGenerator
            ->shouldReceive('generate')
            ->once()
            ->with($resource->prompt, null, 'avatars')
            ->andReturn($result);

        Log::shouldReceive('info')->once();

        $updatedResource = $this->service->generateAvatarTemplateForResource($resource);

        expect($updatedResource->id)->toBe($resource->id)
            ->and($updatedResource->status)->toBe('pending')
            ->and($updatedResource->file_path)->toBe($result['path'])
            ->and($updatedResource->metadata)->toHaveKey('existing')
            ->and($updatedResource->metadata)->toHaveKey('provider')
            ->and($updatedResource->metadata)->toHaveKey('generated_at');

        Event::assertDispatched(ResourceGenerated::class);
    });

    it('throws ImageGenerationException on failure', function () {
        $resource = Resource::factory()->create([
            'type' => 'avatar_image',
            'prompt' => 'A portrait',
        ]);

        $this->imageGenerator
            ->shouldReceive('generate')
            ->once()
            ->andThrow(new ImageGenerationException('API error'));

        Log::shouldReceive('error')->once();

        expect(fn () => $this->service->generateAvatarTemplateForResource($resource))
            ->toThrow(ImageGenerationException::class);
    });
});

describe('generatePlanetImageTemplateForResource', function () {
    it('generates planet image template for existing resource', function () {
        $resource = Resource::factory()->create([
            'type' => 'planet_image',
            'prompt' => 'A rocky planet with cold temperature',
            'tags' => ['existing'],
            'status' => 'approved',
            'metadata' => ['existing' => 'data'],
        ]);

        $result = [
            'url' => 'https://s3.example.com/planets/new-planet.png',
            'path' => 'planets/new-planet.png',
            'disk' => 's3',
            'provider' => 'openai',
        ];

        $this->imageGenerator
            ->shouldReceive('generate')
            ->once()
            ->with($resource->prompt, null, 'planets')
            ->andReturn($result);

        Log::shouldReceive('info')->once();

        $updatedResource = $this->service->generatePlanetImageTemplateForResource($resource);

        expect($updatedResource->id)->toBe($resource->id)
            ->and($updatedResource->status)->toBe('pending')
            ->and($updatedResource->file_path)->toBe($result['path'])
            ->and($updatedResource->tags)->toContain('tellurique', 'froide', 'existing')
            ->and($updatedResource->metadata)->toHaveKey('existing')
            ->and($updatedResource->metadata)->toHaveKey('provider');

        Event::assertDispatched(ResourceGenerated::class);
    });

    it('merges extracted tags with existing tags', function () {
        $resource = Resource::factory()->create([
            'type' => 'planet_image',
            'prompt' => 'A gas giant planet',
            'tags' => ['tellurique'], // Will be merged with extracted 'gazeuse'
        ]);

        $result = [
            'url' => 'https://s3.example.com/planets/planet.png',
            'path' => 'planets/planet.png',
            'disk' => 's3',
            'provider' => 'openai',
        ];

        $this->imageGenerator
            ->shouldReceive('generate')
            ->once()
            ->andReturn($result);

        Log::shouldReceive('info')->once();

        $updatedResource = $this->service->generatePlanetImageTemplateForResource($resource);

        expect($updatedResource->tags)->toContain('gazeuse', 'tellurique');
    });

    it('throws ImageGenerationException on failure', function () {
        $resource = Resource::factory()->create([
            'type' => 'planet_image',
            'prompt' => 'A planet',
        ]);

        $this->imageGenerator
            ->shouldReceive('generate')
            ->once()
            ->andThrow(new ImageGenerationException('API error'));

        Log::shouldReceive('error')->once();

        expect(fn () => $this->service->generatePlanetImageTemplateForResource($resource))
            ->toThrow(ImageGenerationException::class);
    });
});

describe('generatePlanetVideoTemplateForResource', function () {
    it('generates planet video template for existing resource', function () {
        $resource = Resource::factory()->create([
            'type' => 'planet_video',
            'prompt' => 'A desert planet with hot temperature',
            'tags' => ['existing'],
            'status' => 'approved',
            'metadata' => ['existing' => 'data'],
        ]);

        $result = [
            'url' => 'https://s3.example.com/planets/new-video.mp4',
            'path' => 'planets/new-video.mp4',
            'disk' => 's3',
            'provider' => 'openai',
        ];

        $this->videoGenerator
            ->shouldReceive('generate')
            ->once()
            ->with($resource->prompt, null, 'planets')
            ->andReturn($result);

        Log::shouldReceive('info')->once();

        $updatedResource = $this->service->generatePlanetVideoTemplateForResource($resource);

        expect($updatedResource->id)->toBe($resource->id)
            ->and($updatedResource->status)->toBe('pending')
            ->and($updatedResource->file_path)->toBe($result['path'])
            ->and($updatedResource->tags)->toContain('désertique', 'chaude', 'existing')
            ->and($updatedResource->metadata)->toHaveKey('existing')
            ->and($updatedResource->metadata)->toHaveKey('provider');

        Event::assertDispatched(ResourceGenerated::class);
    });

    it('throws VideoGenerationException on failure', function () {
        $resource = Resource::factory()->create([
            'type' => 'planet_video',
            'prompt' => 'A planet',
        ]);

        $this->videoGenerator
            ->shouldReceive('generate')
            ->once()
            ->andThrow(new VideoGenerationException('Video API error'));

        Log::shouldReceive('error')->once();

        expect(fn () => $this->service->generatePlanetVideoTemplateForResource($resource))
            ->toThrow(VideoGenerationException::class);
    });
});

describe('extractPlanetTagsFromPrompt', function () {
    it('extracts planet type tags', function () {
        expect($this->service->extractPlanetTagsFromPrompt('A rocky terrestrial planet'))
            ->toContain('tellurique');

        expect($this->service->extractPlanetTagsFromPrompt('A gas giant planet'))
            ->toContain('gazeuse');

        expect($this->service->extractPlanetTagsFromPrompt('An ice-covered frozen planet'))
            ->toContain('glacée');

        expect($this->service->extractPlanetTagsFromPrompt('A desert planet with sand dunes'))
            ->toContain('désertique');

        expect($this->service->extractPlanetTagsFromPrompt('An ocean planet with deep blue waters'))
            ->toContain('océanique');
    });

    it('extracts size tags', function () {
        expect($this->service->extractPlanetTagsFromPrompt('A small compact planet'))
            ->toContain('petite');

        expect($this->service->extractPlanetTagsFromPrompt('A medium-sized planet'))
            ->toContain('moyenne');

        expect($this->service->extractPlanetTagsFromPrompt('A massive huge planet'))
            ->toContain('grande');
    });

    it('extracts temperature tags', function () {
        expect($this->service->extractPlanetTagsFromPrompt('A cold icy planet'))
            ->toContain('froide');

        expect($this->service->extractPlanetTagsFromPrompt('A temperate moderate planet'))
            ->toContain('tempérée');

        expect($this->service->extractPlanetTagsFromPrompt('A hot scorching planet'))
            ->toContain('chaude');
    });

    it('extracts atmosphere tags', function () {
        expect($this->service->extractPlanetTagsFromPrompt('A planet with breathable clear atmosphere'))
            ->toContain('respirable');

        expect($this->service->extractPlanetTagsFromPrompt('A planet with toxic poisonous atmosphere'))
            ->toContain('toxique');

        expect($this->service->extractPlanetTagsFromPrompt('An airless void planet'))
            ->toContain('inexistante');
    });

    it('extracts terrain tags', function () {
        expect($this->service->extractPlanetTagsFromPrompt('A rocky planet with mountains'))
            ->toContain('rocheux');

        expect($this->service->extractPlanetTagsFromPrompt('An ocean planet with water'))
            ->toContain('océanique');

        expect($this->service->extractPlanetTagsFromPrompt('A desert planet with sand dunes'))
            ->toContain('désertique');

        expect($this->service->extractPlanetTagsFromPrompt('A forest planet with trees'))
            ->toContain('forestier');

        expect($this->service->extractPlanetTagsFromPrompt('An urban planet with city structures'))
            ->toContain('urbain');

        expect($this->service->extractPlanetTagsFromPrompt('A mixed diverse planet'))
            ->toContain('mixte');

        expect($this->service->extractPlanetTagsFromPrompt('A frozen planet with ice glaciers'))
            ->toContain('glacé');
    });

    it('extracts multiple tags from complex prompt', function () {
        $tags = $this->service->extractPlanetTagsFromPrompt(
            'A massive gas giant planet with hot temperature, toxic atmosphere, and rocky terrain'
        );

        expect($tags)->toContain('gazeuse', 'grande', 'chaude', 'toxique', 'rocheux');
    });

    it('returns normalized lowercase tags', function () {
        $tags = $this->service->extractPlanetTagsFromPrompt('A ROCKY PLANET');

        expect($tags)->toContain('tellurique')
            ->and($tags[0])->toBe('tellurique'); // Should be lowercase
    });

    it('returns unique tags only', function () {
        $tags = $this->service->extractPlanetTagsFromPrompt(
            'A rocky rocky rocky planet with mountains and craters'
        );

        // Should only contain 'tellurique' and 'rocheux' once each
        expect(count($tags))->toBe(2)
            ->and(array_count_values($tags)['tellurique'])->toBe(1)
            ->and(array_count_values($tags)['rocheux'])->toBe(1);
    });

    it('returns empty array for prompt with no matching keywords', function () {
        $tags = $this->service->extractPlanetTagsFromPrompt('Just some random text');

        expect($tags)->toBeArray()
            ->and($tags)->toBeEmpty();
    });
});

describe('extractAvatarTagsFromPrompt', function () {
    it('extracts man tag from explicit mention', function () {
        expect($this->service->extractAvatarTagsFromPrompt('A portrait of a man'))
            ->toContain('man');

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of men'))
            ->toContain('man');

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of a male'))
            ->toContain('man');

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of a gentleman'))
            ->toContain('man');
    });

    it('extracts woman tag from explicit mention', function () {
        expect($this->service->extractAvatarTagsFromPrompt('A portrait of a woman'))
            ->toContain('woman');

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of women'))
            ->toContain('woman');

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of a female'))
            ->toContain('woman');

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of a lady'))
            ->toContain('woman');
    });

    it('extracts man tag from male name', function () {
        expect($this->service->extractAvatarTagsFromPrompt('A portrait of John, a seasoned'))
            ->toContain('man');

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of James'))
            ->toContain('man');

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of Michael'))
            ->toContain('man');

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of David'))
            ->toContain('man');
    });

    it('extracts woman tag from female name', function () {
        expect($this->service->extractAvatarTagsFromPrompt('A portrait of Marie, a seasoned'))
            ->toContain('woman');

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of Sophie'))
            ->toContain('woman');

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of Julie'))
            ->toContain('woman');

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of Anne'))
            ->toContain('woman');
    });

    it('extracts name from pattern "of a single character, Name"', function () {
        $tags = $this->service->extractAvatarTagsFromPrompt(
            'A portrait of a single man, John, wearing'
        );

        expect($tags)->toContain('man');
    });

    it('returns empty array for unisex names', function () {
        expect($this->service->extractAvatarTagsFromPrompt('A portrait of Alex'))
            ->toBeEmpty();

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of Sam'))
            ->toBeEmpty();

        expect($this->service->extractAvatarTagsFromPrompt('A portrait of Jordan'))
            ->toBeEmpty();
    });

    it('returns empty array when no gender indicators found', function () {
        expect($this->service->extractAvatarTagsFromPrompt('A portrait of a character'))
            ->toBeEmpty();

        expect($this->service->extractAvatarTagsFromPrompt('A beautiful landscape'))
            ->toBeEmpty();
    });

    it('prioritizes explicit mentions over name detection', function () {
        // If "woman" is explicitly mentioned, should use that even if name suggests male
        $tags = $this->service->extractAvatarTagsFromPrompt(
            'A portrait of a woman named John'
        );

        expect($tags)->toContain('woman')
            ->and($tags)->not->toContain('man');
    });

    it('returns normalized lowercase tags', function () {
        $tags = $this->service->extractAvatarTagsFromPrompt('A portrait of a MAN');

        expect($tags)->toContain('man')
            ->and($tags[0])->toBe('man'); // Should be lowercase
    });
});

afterEach(function () {
    Mockery::close();
});
