<?php

use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\Log;
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

it('handles UnableToCheckFileExistence exception when checking image existence', function () {
    $path = 'planets/image.jpg';
    $planet = Planet::factory()->create(['image_url' => $path]);

    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('exists')
        ->with($path)
        ->andThrow(new \League\Flysystem\UnableToCheckFileExistence('Cannot check file'));

    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk);

    Log::shouldReceive('warning')->once();

    expect($planet->image_url)->toBeNull();
});

it('handles UnableToCheckFileExistence exception when checking video existence', function () {
    $path = 'planets/video.mp4';
    $planet = Planet::factory()->create(['video_url' => $path]);

    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('exists')
        ->with($path)
        ->andThrow(new \League\Flysystem\UnableToCheckFileExistence('Cannot check file'));

    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk);

    Log::shouldReceive('warning')->once();

    expect($planet->video_url)->toBeNull();
});

it('handles generic exception when checking image existence', function () {
    $path = 'planets/image.jpg';
    $planet = Planet::factory()->create(['image_url' => $path]);

    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('exists')
        ->with($path)
        ->andThrow(new \Exception('Generic error'));

    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk);

    Log::shouldReceive('warning')->once();

    expect($planet->image_url)->toBeNull();
});

it('handles generic exception when checking video existence', function () {
    $path = 'planets/video.mp4';
    $planet = Planet::factory()->create(['video_url' => $path]);

    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('exists')
        ->with($path)
        ->andThrow(new \Exception('Generic error'));

    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk);

    Log::shouldReceive('warning')->once();

    expect($planet->video_url)->toBeNull();
});

it('handles UnableToCheckFileExistence with S3Exception as previous when checking image existence', function () {
    $path = 'planets/image.jpg';
    $planet = Planet::factory()->create(['image_url' => $path]);

    $s3Exception = Mockery::mock(\Aws\S3\Exception\S3Exception::class);
    $s3Exception->shouldReceive('getAwsErrorCode')->andReturn('AccessDenied');
    $s3Exception->shouldReceive('getAwsErrorMessage')->andReturn('Access Denied');
    $s3Exception->shouldReceive('getAwsRequestId')->andReturn('request-123');
    $s3Exception->shouldReceive('getStatusCode')->andReturn(403);
    $s3Exception->shouldReceive('getMessage')->andReturn('S3 Error');

    // Create UnableToCheckFileExistence with S3Exception as previous
    // Constructor: (message, code, previous)
    $flysystemException = new \League\Flysystem\UnableToCheckFileExistence('Cannot check file', 0, $s3Exception);

    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('exists')
        ->with($path)
        ->andThrow($flysystemException);

    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk);

    Log::shouldReceive('warning')->once()->with(
        'S3 error checking image existence in storage',
        Mockery::on(function ($context) {
            return isset($context['s3_error_code']) && $context['s3_error_code'] === 'AccessDenied';
        })
    );

    expect($planet->image_url)->toBeNull();
});

it('handles UnableToCheckFileExistence without S3Exception as previous when checking image existence', function () {
    $path = 'planets/image.jpg';
    $planet = Planet::factory()->create(['image_url' => $path]);

    $flysystemException = new \League\Flysystem\UnableToCheckFileExistence('Cannot check file');

    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('exists')
        ->with($path)
        ->andThrow($flysystemException);

    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk);

    Log::shouldReceive('warning')->once()->with(
        'Flysystem error checking image existence in storage',
        Mockery::on(function ($context) {
            return isset($context['error']) && ! isset($context['s3_error_code']);
        })
    );

    expect($planet->image_url)->toBeNull();
});

it('handles direct S3Exception when checking image existence', function () {
    $path = 'planets/image.jpg';
    $planet = Planet::factory()->create(['image_url' => $path]);

    $s3Exception = Mockery::mock(\Aws\S3\Exception\S3Exception::class);
    $s3Exception->shouldReceive('getAwsErrorCode')->andReturn('NoSuchKey');
    $s3Exception->shouldReceive('getAwsErrorMessage')->andReturn('The specified key does not exist');
    $s3Exception->shouldReceive('getAwsRequestId')->andReturn('request-456');
    $s3Exception->shouldReceive('getStatusCode')->andReturn(404);
    $s3Exception->shouldReceive('getMessage')->andReturn('S3 Error');

    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('exists')
        ->with($path)
        ->andThrow($s3Exception);

    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk);

    Log::shouldReceive('warning')->once()->with(
        'S3 error checking image existence in storage',
        Mockery::on(function ($context) {
            return isset($context['s3_error_code']) && $context['s3_error_code'] === 'NoSuchKey';
        })
    );

    expect($planet->image_url)->toBeNull();
});

it('handles UnableToCheckFileExistence with S3Exception as previous when checking video existence', function () {
    $path = 'planets/video.mp4';
    $planet = Planet::factory()->create(['video_url' => $path]);

    $s3Exception = Mockery::mock(\Aws\S3\Exception\S3Exception::class);
    $s3Exception->shouldReceive('getAwsErrorCode')->andReturn('AccessDenied');
    $s3Exception->shouldReceive('getAwsErrorMessage')->andReturn('Access Denied');
    $s3Exception->shouldReceive('getAwsRequestId')->andReturn('request-123');
    $s3Exception->shouldReceive('getStatusCode')->andReturn(403);
    $s3Exception->shouldReceive('getMessage')->andReturn('S3 Error');

    // Create UnableToCheckFileExistence with S3Exception as previous
    $flysystemException = new \League\Flysystem\UnableToCheckFileExistence('Cannot check file', 0, $s3Exception);

    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('exists')
        ->with($path)
        ->andThrow($flysystemException);

    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk);

    Log::shouldReceive('warning')->once()->with(
        'S3 error checking video existence in storage',
        Mockery::on(function ($context) {
            return isset($context['s3_error_code']) && $context['s3_error_code'] === 'AccessDenied';
        })
    );

    expect($planet->video_url)->toBeNull();
});

it('handles UnableToCheckFileExistence without S3Exception as previous when checking video existence', function () {
    $path = 'planets/video.mp4';
    $planet = Planet::factory()->create(['video_url' => $path]);

    $flysystemException = new \League\Flysystem\UnableToCheckFileExistence('Cannot check file');

    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('exists')
        ->with($path)
        ->andThrow($flysystemException);

    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk);

    Log::shouldReceive('warning')->once()->with(
        'Flysystem error checking video existence in storage',
        Mockery::on(function ($context) {
            return isset($context['error']) && ! isset($context['s3_error_code']);
        })
    );

    expect($planet->video_url)->toBeNull();
});

it('handles direct S3Exception when checking video existence', function () {
    $path = 'planets/video.mp4';
    $planet = Planet::factory()->create(['video_url' => $path]);

    $s3Exception = Mockery::mock(\Aws\S3\Exception\S3Exception::class);
    $s3Exception->shouldReceive('getAwsErrorCode')->andReturn('NoSuchKey');
    $s3Exception->shouldReceive('getAwsErrorMessage')->andReturn('The specified key does not exist');
    $s3Exception->shouldReceive('getAwsRequestId')->andReturn('request-456');
    $s3Exception->shouldReceive('getStatusCode')->andReturn(404);
    $s3Exception->shouldReceive('getMessage')->andReturn('S3 Error');

    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('exists')
        ->with($path)
        ->andThrow($s3Exception);

    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk);

    Log::shouldReceive('warning')->once()->with(
        'S3 error checking video existence in storage',
        Mockery::on(function ($context) {
            return isset($context['s3_error_code']) && $context['s3_error_code'] === 'NoSuchKey';
        })
    );

    expect($planet->video_url)->toBeNull();
});

afterEach(function () {
    \Mockery::close();
});
