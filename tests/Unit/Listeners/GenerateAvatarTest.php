<?php

use App\Events\UserRegistered;
use App\Exceptions\ImageGenerationException;
use App\Listeners\GenerateAvatar;
use App\Models\User;
use App\Services\ImageGenerationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
    Queue::fake(); // Fake queues for testing
    $this->imageGenerator = Mockery::mock(ImageGenerationService::class);
    $this->listener = new GenerateAvatar($this->imageGenerator);
    $this->user = User::factory()->create();
});

it('queues avatar generation job when user is registered', function () {
    Queue::fake();

    $event = new UserRegistered($this->user);
    event($event);

    // Verify that the listener job was queued
    Queue::assertPushed(\Illuminate\Events\CallQueuedListener::class, function ($job) {
        return $job->class === GenerateAvatar::class;
    });
});

it('generates avatar successfully when job is processed', function () {
    $avatarPath = 'images/generated/avatars/avatar-123.png';
    $avatarUrl = 'https://s3.example.com/'.$avatarPath;

    $this->imageGenerator
        ->shouldReceive('generate')
        ->once()
        ->with(
            Mockery::on(function ($prompt) {
                return is_string($prompt) && str_contains($prompt, 'space technician') && str_contains($prompt, 'ship captain');
            }),
            null,
            'avatars'
        )
        ->andReturn([
            'url' => $avatarUrl,
            'path' => $avatarPath,
            'disk' => 's3',
            'provider' => 'openai',
        ]);

    // Create the file in fake storage to simulate actual file creation
    Storage::disk('s3')->put($avatarPath, 'fake image content');

    $event = new UserRegistered($this->user);
    $this->listener->handle($event);

    $this->user->refresh();

    // The path should be stored in the database
    expect($this->user->getRawOriginal('avatar_url'))->toBe($avatarPath);

    // The accessor should reconstruct the URL (and verify file exists)
    expect($this->user->avatar_url)->toBeString()->toContain($avatarPath);
});

it('includes user name in avatar prompt', function () {
    $user = User::factory()->create(['name' => 'John Doe']);

    $this->imageGenerator
        ->shouldReceive('generate')
        ->once()
        ->with(
            Mockery::on(function ($prompt) use ($user) {
                return str_contains($prompt, $user->name);
            }),
            null,
            'avatars'
        )
        ->andReturn([
            'url' => 'https://s3.example.com/avatar.png',
            'path' => 'images/generated/avatars/avatar.png',
            'disk' => 's3',
            'provider' => 'openai',
        ]);

    $event = new UserRegistered($user);
    $this->listener->handle($event);
});

it('throws exception when avatar generation fails (job will be retried)', function () {
    Log::shouldReceive('error')
        ->once()
        ->with('Failed to generate avatar for user', Mockery::type('array'));

    $this->imageGenerator
        ->shouldReceive('generate')
        ->once()
        ->with(Mockery::any(), null, 'avatars')
        ->andThrow(new ImageGenerationException('API error'));

    $event = new UserRegistered($this->user);

    // The exception will be thrown, marking the job as failed
    // In a real queue, this would trigger a retry
    expect(fn () => $this->listener->handle($event))
        ->toThrow(ImageGenerationException::class, 'API error');

    $this->user->refresh();
    expect($this->user->avatar_url)->toBeNull();
});

it('generates prompt with Alien movie aesthetic', function () {
    $this->imageGenerator
        ->shouldReceive('generate')
        ->once()
        ->with(
            Mockery::on(function ($prompt) {
                return str_contains(strtolower($prompt), 'alien')
                    && str_contains(strtolower($prompt), '1979')
                    && str_contains(strtolower($prompt), 'industrial')
                    && str_contains(strtolower($prompt), 'sci-fi');
            }),
            null,
            'avatars'
        )
        ->andReturn([
            'url' => 'https://s3.example.com/avatar.png',
            'path' => 'images/generated/avatars/avatar.png',
            'disk' => 's3',
            'provider' => 'openai',
        ]);

    $event = new UserRegistered($this->user);
    $this->listener->handle($event);
});

it('logs success when avatar is generated', function () {
    $avatarUrl = 'https://s3.example.com/avatar.png';

    Log::shouldReceive('info')
        ->once()
        ->with('Avatar generated successfully', Mockery::type('array'));

    $this->imageGenerator
        ->shouldReceive('generate')
        ->once()
        ->with(Mockery::any(), null, 'avatars')
        ->andReturn([
            'url' => $avatarUrl,
            'path' => 'images/generated/avatars/avatar.png',
            'disk' => 's3',
            'provider' => 'openai',
        ]);

    $event = new UserRegistered($this->user);
    $this->listener->handle($event);
});
