<?php

use App\Events\PlanetCreated;
use App\Listeners\GeneratePlanetVideo;
use App\Models\Planet;
use App\Services\VideoGenerationService;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
    Queue::fake();

    $this->videoGenerator = Mockery::mock(VideoGenerationService::class);
    // Default: force direct generation (random function returns > 70)
    $this->randomFunction = fn () => 100; // Always > 70, so no template
    $this->listener = new GeneratePlanetVideo($this->videoGenerator, $this->randomFunction);
    $this->planet = Planet::factory()->create();
});

it('skips generation if video generation is disabled', function () {
    Config::set('video-generation.enabled', false);

    Log::shouldReceive('info')->once();
    Log::shouldReceive('warning')->zeroOrMoreTimes();

    $this->videoGenerator->shouldNotReceive('generate');

    $event = new PlanetCreated($this->planet);
    $this->listener->handle($event);

    $this->planet->refresh();
    // The listener should set video_generating to false
    expect($this->planet->getRawOriginal('video_generating'))->toBe(0);
});

it('skips generation if planet already has a video', function () {
    $planet = Planet::factory()->create([
        'video_url' => 'videos/existing-video.mp4',
    ]);

    $this->videoGenerator->shouldNotReceive('generate');

    $event = new PlanetCreated($planet);
    $this->listener->handle($event);

    $planet->refresh();
    // The listener should set video_generating to false
    expect($planet->getRawOriginal('video_generating'))->toBe(0);
});

it('handles job failure and resets generating status', function () {
    $exception = new \Exception('Job failed');
    $event = new PlanetCreated($this->planet);

    Log::shouldReceive('error')->once();
    Log::shouldReceive('warning')->zeroOrMoreTimes();
    Log::shouldReceive('critical')->zeroOrMoreTimes();

    $this->listener->failed($event, $exception);

    $this->planet->refresh();
    // resetGeneratingStatus should have been called
    expect($this->planet->getRawOriginal('video_generating'))->toBe(0);
});

it('logs S3 error details in failed handler', function () {
    $s3Exception = Mockery::mock(S3Exception::class);
    $s3Exception->shouldReceive('getAwsErrorCode')->andReturn('AccessDenied');
    $s3Exception->shouldReceive('getAwsErrorMessage')->andReturn('Access Denied');
    $s3Exception->shouldReceive('getAwsRequestId')->andReturn('request-123');
    $s3Exception->shouldReceive('getStatusCode')->andReturn(403);
    $s3Exception->shouldReceive('getMessage')->andReturn('S3 Error');

    $event = new PlanetCreated($this->planet);

    Log::shouldReceive('error')->once()->with('Planet video generation failed after all retries', Mockery::on(function ($context) {
        return isset($context['s3_error_code']) && $context['s3_error_code'] === 'AccessDenied';
    }));

    $this->listener->failed($event, $s3Exception);
});

afterEach(function () {
    \Mockery::close();
});
