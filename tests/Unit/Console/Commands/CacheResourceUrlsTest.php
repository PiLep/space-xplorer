<?php

use App\Models\Resource;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

it('caches URLs for resources without cached URLs', function () {
    $path1 = 'images/generated/avatar1.png';
    $path2 = 'images/generated/avatar2.png';
    Storage::disk('s3')->put($path1, 'fake content');
    Storage::disk('s3')->put($path2, 'fake content');

    $resource1 = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $path1,
        'file_url_cached' => null,
    ]);

    $resource2 = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $path2,
        'file_url_cached' => null,
    ]);

    // Resource with null file_path should be skipped
    Resource::factory()->create([
        'file_path' => null,
        'file_url_cached' => null,
    ]);

    $exitCode = Artisan::call('resources:cache-urls');

    expect($exitCode)->toBe(0);

    $resource1->refresh();
    $resource2->refresh();

    expect($resource1->file_url_cached)->toBe(Storage::disk('s3')->url($path1))
        ->and($resource2->file_url_cached)->toBe(Storage::disk('s3')->url($path2));
});

it('skips resources that already have cached URLs', function () {
    // Create resource with a full URL as file_path (old format)
    // This will be cached automatically by the observer
    $fullUrl = 'https://example.com/image.png';
    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $fullUrl,
    ]);

    // Observer should have cached it, but ensure it's cached
    $resource->refresh();
    if (! $resource->file_url_cached) {
        $resource->updateQuietly(['file_url_cached' => $fullUrl]);
    }

    $exitCode = Artisan::call('resources:cache-urls');

    expect($exitCode)->toBe(0)
        ->and(Artisan::output())->toContain('No resources need URL caching');

    $resource->refresh();
    // The cached URL should remain unchanged (resource was not processed)
    expect($resource->file_url_cached)->toBe($fullUrl);
});

it('forces update of all resources with --force option', function () {
    $path = 'images/generated/avatar.png';
    Storage::disk('s3')->put($path, 'fake content');

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $path,
        'file_url_cached' => 'https://old-cached-url.com/image.png',
    ]);

    $exitCode = Artisan::call('resources:cache-urls', ['--force' => true]);

    expect($exitCode)->toBe(0);

    $resource->refresh();
    expect($resource->file_url_cached)->toBe(Storage::disk('s3')->url($path));
});

it('caches URLs for avatar_image resources', function () {
    $path = 'images/generated/avatars/avatar1.png';
    Storage::disk('s3')->put($path, 'fake content');

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $path,
        'file_url_cached' => null,
    ]);

    Artisan::call('resources:cache-urls');

    $resource->refresh();
    expect($resource->file_url_cached)->toBe(Storage::disk('s3')->url($path));
});

it('caches URLs for planet_image resources', function () {
    $path = 'images/generated/planets/planet1.png';
    Storage::disk('s3')->put($path, 'fake content');

    $resource = Resource::factory()->create([
        'type' => 'planet_image',
        'file_path' => $path,
        'file_url_cached' => null,
    ]);

    Artisan::call('resources:cache-urls');

    $resource->refresh();
    expect($resource->file_url_cached)->toBe(Storage::disk('s3')->url($path));
});

it('caches URLs for planet_video resources', function () {
    Storage::fake('s3');
    $path = 'videos/generated/planets/planet1.mp4';
    Storage::disk('s3')->put($path, 'fake content');

    $resource = Resource::factory()->create([
        'type' => 'planet_video',
        'file_path' => $path,
        'file_url_cached' => null,
    ]);

    Artisan::call('resources:cache-urls');

    $resource->refresh();
    expect($resource->file_url_cached)->toBe(Storage::disk('s3')->url($path));
});

it('handles resources with full URL in file_path', function () {
    $fullUrl = 'https://example.com/image.png';

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $fullUrl,
        'file_url_cached' => null,
    ]);

    Artisan::call('resources:cache-urls');

    $resource->refresh();
    expect($resource->file_url_cached)->toBe($fullUrl);
});

it('clears cache when file does not exist', function () {
    $path = 'images/generated/nonexistent.png';
    // Don't create the file in storage

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $path,
        'file_url_cached' => 'https://old-url.com/image.png',
    ]);

    Artisan::call('resources:cache-urls');

    $resource->refresh();
    expect($resource->file_url_cached)->toBeNull();
});

it('skips resources with null file_path', function () {
    $resource = Resource::factory()->create([
        'file_path' => null,
        'file_url_cached' => null,
    ]);

    $exitCode = Artisan::call('resources:cache-urls');

    expect($exitCode)->toBe(0);

    $resource->refresh();
    expect($resource->file_url_cached)->toBeNull();
});

it('displays success message when no resources need caching', function () {
    // Create resources with full URLs as file_path (old format)
    // These will be cached automatically by the observer, so command won't process them
    $fullUrl = 'https://example.com/image.png';
    Resource::factory()->count(3)->create([
        'file_path' => $fullUrl,
    ]);

    // Ensure all resources have cached URLs
    Resource::whereNotNull('file_path')->update(['file_url_cached' => $fullUrl]);

    $exitCode = Artisan::call('resources:cache-urls');

    $output = Artisan::output();
    expect($exitCode)->toBe(0)
        ->and($output)->toContain('No resources need URL caching')
        ->and($output)->toContain('Use --force to update all resources');
});

it('displays summary table with counts', function () {
    $path1 = 'images/generated/avatar1.png';
    $path2 = 'images/generated/avatar2.png';
    Storage::disk('s3')->put($path1, 'fake content');
    // Don't create path2 to simulate missing file

    Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $path1,
        'file_url_cached' => null,
    ]);

    Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $path2,
        'file_url_cached' => null,
    ]);

    $exitCode = Artisan::call('resources:cache-urls');

    $output = Artisan::output();
    expect($exitCode)->toBe(0)
        ->and($output)->toContain('✅ Success')
        ->and($output)->toContain('⏭️  Skipped')
        ->and($output)->toContain('📊 Total');
});

it('processes resources in chunks', function () {
    // Create more than 100 resources to test chunking
    $paths = [];
    for ($i = 0; $i < 150; $i++) {
        $path = "images/generated/avatar{$i}.png";
        Storage::disk('s3')->put($path, 'fake content');
        $paths[] = $path;
    }

    foreach ($paths as $path) {
        Resource::factory()->create([
            'type' => 'avatar_image',
            'file_path' => $path,
            'file_url_cached' => null,
        ]);
    }

    $exitCode = Artisan::call('resources:cache-urls');

    expect($exitCode)->toBe(0);

    $cachedCount = Resource::whereNotNull('file_url_cached')->count();
    expect($cachedCount)->toBe(150);
});

it('handles storage exceptions gracefully', function () {
    Log::shouldReceive('warning')->atLeast()->once();

    $path = 'images/generated/avatar.png';
    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $path,
        'file_url_cached' => null,
    ]);

    // Mock Storage to throw an exception
    Storage::shouldReceive('disk')
        ->with('s3')
        ->andThrow(new \League\Flysystem\UnableToCheckFileExistence('Storage error'));

    $exitCode = Artisan::call('resources:cache-urls');

    expect($exitCode)->toBe(0);
});

it('handles S3 exceptions gracefully', function () {
    Log::shouldReceive('warning')->atLeast()->once();

    $path = 'images/generated/avatar.png';
    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $path,
        'file_url_cached' => null,
    ]);

    // Mock Storage to throw S3Exception
    $s3Exception = Mockery::mock(\Aws\S3\Exception\S3Exception::class);
    Storage::shouldReceive('disk')
        ->with('s3')
        ->andThrow($s3Exception);

    $exitCode = Artisan::call('resources:cache-urls');

    expect($exitCode)->toBe(0);
});

it('handles generic exceptions gracefully', function () {
    Log::shouldReceive('error')->atLeast()->once();

    $path = 'images/generated/avatar.png';
    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $path,
        'file_url_cached' => null,
    ]);

    // Mock Storage to throw a generic exception
    Storage::shouldReceive('disk')
        ->with('s3')
        ->andThrow(new \Exception('Generic error'));

    $exitCode = Artisan::call('resources:cache-urls');

    expect($exitCode)->toBe(0);
});

it('displays progress bar during processing', function () {
    // Clear any existing resources first
    Resource::query()->delete();

    $paths = [];
    for ($i = 0; $i < 5; $i++) {
        $path = "images/generated/avatar{$i}.png";
        Storage::disk('s3')->put($path, 'fake content');
        $paths[] = $path;
    }

    // Create resources without triggering observer (which would auto-cache URLs)
    // We need resources with file_path but no file_url_cached to test the command
    foreach ($paths as $path) {
        $resource = Resource::factory()->make([
            'type' => 'avatar_image',
            'file_path' => $path,
            'file_url_cached' => null,
        ]);
        // Use updateQuietly to avoid triggering observer
        $resource->saveQuietly();
    }

    // Verify resources were created without cached URLs
    // Note: The observer may have tried to cache, but if files don't exist it sets to null
    $resourcesToProcess = Resource::whereNotNull('file_path')->whereNull('file_url_cached')->count();

    // If observer cached them, clear the cache to test the command
    if ($resourcesToProcess === 0) {
        Resource::whereNotNull('file_path')->update(['file_url_cached' => null]);
        $resourcesToProcess = Resource::whereNotNull('file_path')->whereNull('file_url_cached')->count();
    }

    expect($resourcesToProcess)->toBeGreaterThan(0);

    $exitCode = Artisan::call('resources:cache-urls');

    $output = Artisan::output();
    expect($exitCode)->toBe(0)
        ->and($output)->toContain('Found')
        ->and($output)->toContain('resource(s) to process')
        ->and($output)->toContain('Caching complete!');
});

it('uses correct storage disk for each resource type', function () {
    config(['image-generation.storage.disk' => 's3']);
    config(['video-generation.storage.disk' => 's3']);

    $avatarPath = 'images/generated/avatar.png';
    $planetImagePath = 'images/generated/planet.png';
    $planetVideoPath = 'videos/generated/planet.mp4';

    Storage::disk('s3')->put($avatarPath, 'fake content');
    Storage::disk('s3')->put($planetImagePath, 'fake content');
    Storage::disk('s3')->put($planetVideoPath, 'fake content');

    $avatarResource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $avatarPath,
        'file_url_cached' => null,
    ]);

    $planetImageResource = Resource::factory()->create([
        'type' => 'planet_image',
        'file_path' => $planetImagePath,
        'file_url_cached' => null,
    ]);

    $planetVideoResource = Resource::factory()->create([
        'type' => 'planet_video',
        'file_path' => $planetVideoPath,
        'file_url_cached' => null,
    ]);

    Artisan::call('resources:cache-urls');

    $avatarResource->refresh();
    $planetImageResource->refresh();
    $planetVideoResource->refresh();

    expect($avatarResource->file_url_cached)->toBe(Storage::disk('s3')->url($avatarPath))
        ->and($planetImageResource->file_url_cached)->toBe(Storage::disk('s3')->url($planetImagePath))
        ->and($planetVideoResource->file_url_cached)->toBe(Storage::disk('s3')->url($planetVideoPath));
});

afterEach(function () {
    \Mockery::close();
});

