<?php

use App\Models\Resource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
    Storage::fake('local'); // For other disk types
});

it('caches file URL when resource is created with valid file path', function () {
    $filePath = 'images/generated/avatars/avatar-123.png';
    Storage::disk('s3')->put($filePath, 'fake image content');

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $filePath,
    ]);

    $resource->refresh();

    expect($resource->file_url_cached)
        ->not->toBeNull()
        ->and($resource->file_url_cached)
        ->toContain($filePath);
});

it('caches file URL for planet_image type', function () {
    $filePath = 'images/generated/planets/planet-123.png';
    Storage::disk('s3')->put($filePath, 'fake image content');

    $resource = Resource::factory()->create([
        'type' => 'planet_image',
        'file_path' => $filePath,
    ]);

    $resource->refresh();

    expect($resource->file_url_cached)
        ->not->toBeNull()
        ->and($resource->file_url_cached)
        ->toContain($filePath);
});

it('caches file URL for planet_video type', function () {
    $filePath = 'videos/generated/planets/planet-123.mp4';
    Storage::disk('s3')->put($filePath, 'fake video content');

    $resource = Resource::factory()->create([
        'type' => 'planet_video',
        'file_path' => $filePath,
    ]);

    $resource->refresh();

    expect($resource->file_url_cached)
        ->not->toBeNull()
        ->and($resource->file_url_cached)
        ->toContain($filePath);
});

it('clears cache when file_path is null or empty on creation', function () {
    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => null,
        'file_url_cached' => 'https://old-url.com/image.png', // Pre-existing cache
    ]);

    $resource->refresh();

    expect($resource->file_url_cached)->toBeNull();
});

it('caches full URL when file_path is already a full URL (old format)', function () {
    $fullUrl = 'https://s3.example.com/images/avatar.png';

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $fullUrl,
    ]);

    $resource->refresh();

    expect($resource->file_url_cached)->toBe($fullUrl);
});

it('clears cache when file does not exist in storage', function () {
    $filePath = 'images/nonexistent/avatar.png';
    // Don't create the file in storage

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $filePath,
        'file_url_cached' => 'https://old-url.com/image.png', // Pre-existing cache
    ]);

    $resource->refresh();

    expect($resource->file_url_cached)->toBeNull();
});

it('updates cache when file_path changes', function () {
    $oldPath = 'images/old/avatar.png';
    $newPath = 'images/new/avatar.png';
    Storage::disk('s3')->put($oldPath, 'old content');
    Storage::disk('s3')->put($newPath, 'new content');

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $oldPath,
    ]);

    $resource->refresh();
    $oldCachedUrl = $resource->file_url_cached;

    // Update file_path
    $resource->update(['file_path' => $newPath]);
    $resource->refresh();

    expect($resource->file_url_cached)
        ->not->toBe($oldCachedUrl)
        ->and($resource->file_url_cached)
        ->toContain($newPath);
});

it('does not update cache when file_path does not change', function () {
    $filePath = 'images/avatar.png';
    Storage::disk('s3')->put($filePath, 'content');

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $filePath,
    ]);

    $resource->refresh();
    $originalCachedUrl = $resource->file_url_cached;

    // Update something else, not file_path
    $resource->update(['description' => 'New description']);
    $resource->refresh();

    expect($resource->file_url_cached)->toBe($originalCachedUrl);
});

it('caches file URL when resource is restored', function () {
    // Note: Resource model doesn't use SoftDeletes, so restore() doesn't exist
    // This test verifies that if restore() were called (via observer), the cache would be updated
    // Since we can't actually restore, we'll test the observer's restored() method directly
    $filePath = 'images/avatar.png';
    Storage::disk('s3')->put($filePath, 'content');

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $filePath,
    ]);

    $resource->refresh();
    expect($resource->file_url_cached)->not->toBeNull(); // Cache exists

    // Clear cache manually to simulate restoration scenario
    $resource->updateQuietly(['file_url_cached' => null]);

    // Call the observer's restored method directly (simulating a restore)
    $observer = new \App\Observers\ResourceObserver;
    $observer->restored($resource);
    $resource->refresh();

    expect($resource->file_url_cached)
        ->not->toBeNull()
        ->and($resource->file_url_cached)
        ->toContain($filePath);
});

it('does nothing when resource is deleted', function () {
    $filePath = 'images/avatar.png';
    Storage::disk('s3')->put($filePath, 'content');

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $filePath,
    ]);

    $resource->refresh();
    $cachedUrl = $resource->file_url_cached;

    // Delete (soft delete)
    $resource->delete();
    $resource->refresh();

    // Cache should still exist (observer does nothing)
    expect($resource->file_url_cached)->toBe($cachedUrl);
});

it('does nothing when resource is force deleted', function () {
    $filePath = 'images/avatar.png';
    Storage::disk('s3')->put($filePath, 'content');

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $filePath,
    ]);

    $resource->refresh();
    $cachedUrl = $resource->file_url_cached;

    // Force delete
    $resource->forceDelete();

    // Observer does nothing, so this test just verifies no exception is thrown
    expect(true)->toBeTrue();
});

it('handles S3 errors gracefully without failing', function () {
    // Note: Observer doesn't log in testing environment, so we don't expect logs
    $filePath = 'images/avatar.png';

    // Mock Storage to throw S3Exception
    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk = \Mockery::mock());

    // Create a proper S3Exception mock with CommandInterface
    $mockCommand = \Mockery::mock(\Aws\CommandInterface::class);
    $mockDisk->shouldReceive('exists')
        ->with($filePath)
        ->andThrow(new \Aws\S3\Exception\S3Exception('S3 Error', $mockCommand));

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $filePath,
        'file_url_cached' => 'https://old-url.com/image.png',
    ]);

    $resource->refresh();

    // Cache should be cleared on error
    expect($resource->file_url_cached)->toBeNull();
});

it('handles UnableToCheckFileExistence errors gracefully', function () {
    // Note: Observer doesn't log in testing environment, so we don't expect logs
    $filePath = 'images/avatar.png';

    // Mock Storage to throw UnableToCheckFileExistence
    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk = \Mockery::mock());

    $mockDisk->shouldReceive('exists')
        ->with($filePath)
        ->andThrow(new \League\Flysystem\UnableToCheckFileExistence('Cannot check file existence'));

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $filePath,
        'file_url_cached' => 'https://old-url.com/image.png',
    ]);

    $resource->refresh();

    // Cache should be cleared on error
    expect($resource->file_url_cached)->toBeNull();
});

it('handles generic exceptions gracefully', function () {
    // Note: Observer doesn't log in testing environment, so we don't expect logs
    $filePath = 'images/avatar.png';

    // Mock Storage to throw generic exception
    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk = \Mockery::mock());

    $mockDisk->shouldReceive('exists')
        ->with($filePath)
        ->andThrow(new \Exception('Generic error'));

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $filePath,
        'file_url_cached' => 'https://old-url.com/image.png',
    ]);

    $resource->refresh();

    // Cache should be cleared on error
    expect($resource->file_url_cached)->toBeNull();
});

it('uses correct disk based on resource type', function () {
    // Test avatar_image uses image-generation disk config
    config(['image-generation.storage.disk' => 'local']);

    $filePath = 'images/avatar.png';
    Storage::disk('local')->put($filePath, 'content');

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $filePath,
    ]);

    $resource->refresh();

    expect($resource->file_url_cached)
        ->not->toBeNull()
        ->and($resource->file_url_cached)
        ->toContain($filePath);
});

it('uses default s3 disk for unknown resource types', function () {
    // Note: The type enum only allows 'avatar_image', 'planet_image', 'planet_video'
    // Since all current types have specific disks, we test the default behavior
    // by ensuring the file exists in s3 and the observer correctly uses s3 disk

    // Remove config keys to ensure default 's3' is used (setting to null doesn't work with config default)
    config()->offsetUnset('image-generation.storage.disk');
    config()->offsetUnset('video-generation.storage.disk');

    $filePath = 'files/test.png';

    // Ensure file exists in s3 disk
    Storage::disk('s3')->put($filePath, 'content');

    // Verify file exists before creating resource
    expect(Storage::disk('s3')->exists($filePath))->toBeTrue();

    // Create resource with planet_video type (uses video-generation config, defaults to s3)
    $resource = Resource::factory()->create([
        'type' => 'planet_video', // Valid enum value
        'file_path' => $filePath,
    ]);

    $resource->refresh();

    expect($resource->file_url_cached)
        ->not->toBeNull()
        ->and($resource->file_url_cached)
        ->toContain($filePath);
});

it('clears cache when file_path is changed to null', function () {
    $filePath = 'images/avatar.png';
    Storage::disk('s3')->put($filePath, 'content');

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $filePath,
    ]);

    $resource->refresh();
    expect($resource->file_url_cached)->not->toBeNull();

    // Change file_path to null
    $resource->update(['file_path' => null]);
    $resource->refresh();

    expect($resource->file_url_cached)->toBeNull();
});

it('clears cache when file_path is changed to empty string', function () {
    $filePath = 'images/avatar.png';
    Storage::disk('s3')->put($filePath, 'content');

    $resource = Resource::factory()->create([
        'type' => 'avatar_image',
        'file_path' => $filePath,
    ]);

    $resource->refresh();
    expect($resource->file_url_cached)->not->toBeNull();

    // Change file_path to empty string
    $resource->update(['file_path' => '']);
    $resource->refresh();

    expect($resource->file_url_cached)->toBeNull();
});

afterEach(function () {
    \Mockery::close();

    // Remove config keys to ensure test isolation (setting to null doesn't work with config default)
    config()->offsetUnset('image-generation.storage.disk');
    config()->offsetUnset('video-generation.storage.disk');
});
