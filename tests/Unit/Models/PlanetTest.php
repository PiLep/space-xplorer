<?php

use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('has users relationship', function () {
    $planet = Planet::factory()->create();
    $user1 = User::factory()->create(['home_planet_id' => $planet->id]);
    $user2 = User::factory()->create(['home_planet_id' => $planet->id]);

    expect($planet->users)->toHaveCount(2)
        ->and($planet->users->pluck('id'))->toContain($user1->id, $user2->id);
});

it('returns null for image_url when path is empty', function () {
    $planet = Planet::factory()->create(['image_url' => null]);

    expect($planet->image_url)->toBeNull();
});

it('returns full URL when image_url is already a URL', function () {
    $fullUrl = 'https://example.com/image.jpg';
    $planet = Planet::factory()->create(['image_url' => $fullUrl]);

    expect($planet->image_url)->toBe($fullUrl);
});

it('returns storage URL when image_url is a path and file exists', function () {
    Storage::fake('s3');
    $path = 'planets/image.jpg';
    Storage::disk('s3')->put($path, 'fake content');

    $planet = Planet::factory()->create(['image_url' => $path]);

    expect($planet->image_url)->toBe(Storage::disk('s3')->url($path));
});

it('returns null when image_url is a path but file does not exist', function () {
    Storage::fake('s3');
    $path = 'planets/nonexistent.jpg';

    $planet = Planet::factory()->create(['image_url' => $path]);

    expect($planet->image_url)->toBeNull();
});

it('returns null for video_url when path is empty', function () {
    $planet = Planet::factory()->create(['video_url' => null]);

    expect($planet->video_url)->toBeNull();
});

it('returns full URL when video_url is already a URL', function () {
    $fullUrl = 'https://example.com/video.mp4';
    $planet = Planet::factory()->create(['video_url' => $fullUrl]);

    expect($planet->video_url)->toBe($fullUrl);
});

it('returns storage URL when video_url is a path and file exists', function () {
    Storage::fake('s3');
    $path = 'planets/video.mp4';
    Storage::disk('s3')->put($path, 'fake content');

    $planet = Planet::factory()->create(['video_url' => $path]);

    expect($planet->video_url)->toBe(Storage::disk('s3')->url($path));
});

it('returns null when video_url is a path but file does not exist', function () {
    Storage::fake('s3');
    $path = 'planets/nonexistent.mp4';

    $planet = Planet::factory()->create(['video_url' => $path]);

    expect($planet->video_url)->toBeNull();
});

it('hasImage returns true when image exists and not generating', function () {
    Storage::fake('s3');
    $path = 'planets/image.jpg';
    Storage::disk('s3')->put($path, 'fake content');

    $planet = Planet::factory()->create([
        'image_url' => $path,
        'image_generating' => false,
    ]);

    expect($planet->hasImage())->toBeTrue();
});

it('hasImage returns false when image is generating', function () {
    Storage::fake('s3');
    $path = 'planets/image.jpg';
    Storage::disk('s3')->put($path, 'fake content');

    $planet = Planet::factory()->create([
        'image_url' => $path,
        'image_generating' => true,
    ]);

    expect($planet->hasImage())->toBeFalse();
});

it('hasImage returns false when image_url is null', function () {
    $planet = Planet::factory()->create([
        'image_url' => null,
        'image_generating' => false,
    ]);

    expect($planet->hasImage())->toBeFalse();
});

it('hasVideo returns true when video exists and not generating', function () {
    Storage::fake('s3');
    $path = 'planets/video.mp4';
    Storage::disk('s3')->put($path, 'fake content');

    $planet = Planet::factory()->create([
        'video_url' => $path,
        'video_generating' => false,
    ]);

    expect($planet->hasVideo())->toBeTrue();
});

it('hasVideo returns false when video is generating', function () {
    Storage::fake('s3');
    $path = 'planets/video.mp4';
    Storage::disk('s3')->put($path, 'fake content');

    $planet = Planet::factory()->create([
        'video_url' => $path,
        'video_generating' => true,
    ]);

    expect($planet->hasVideo())->toBeFalse();
});

it('hasVideo returns false when video_url is null', function () {
    $planet = Planet::factory()->create([
        'video_url' => null,
        'video_generating' => false,
    ]);

    expect($planet->hasVideo())->toBeFalse();
});

it('isImageGenerating returns true when image_generating is true', function () {
    $planet = Planet::factory()->create(['image_generating' => true]);

    expect($planet->isImageGenerating())->toBeTrue();
});

it('isImageGenerating returns false when image_generating is false', function () {
    $planet = Planet::factory()->create(['image_generating' => false]);

    expect($planet->isImageGenerating())->toBeFalse();
});

it('isVideoGenerating returns true when video_generating is true', function () {
    $planet = Planet::factory()->create(['video_generating' => true]);

    expect($planet->isVideoGenerating())->toBeTrue();
});

it('isVideoGenerating returns false when video_generating is false', function () {
    $planet = Planet::factory()->create(['video_generating' => false]);

    expect($planet->isVideoGenerating())->toBeFalse();
});
