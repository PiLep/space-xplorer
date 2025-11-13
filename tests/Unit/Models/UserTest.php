<?php

use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

it('has homePlanet relationship', function () {
    $planet = Planet::factory()->create();
    $user = User::factory()->create(['home_planet_id' => $planet->id]);

    expect($user->homePlanet)->not->toBeNull()
        ->and($user->homePlanet->id)->toBe($planet->id);
});

it('returns null for avatar_url when path is empty', function () {
    $user = User::factory()->create(['avatar_url' => null]);

    expect($user->avatar_url)->toBeNull();
});

it('returns full URL when avatar_url is already a URL', function () {
    $fullUrl = 'https://example.com/avatar.jpg';
    $user = User::factory()->create(['avatar_url' => $fullUrl]);

    expect($user->avatar_url)->toBe($fullUrl);
});

it('returns storage URL when avatar_url is a path and file exists', function () {
    Storage::fake('s3');
    $path = 'avatars/avatar.jpg';
    Storage::disk('s3')->put($path, 'fake content');

    $user = User::factory()->create(['avatar_url' => $path]);

    expect($user->avatar_url)->toBe(Storage::disk('s3')->url($path));
});

it('returns null when avatar_url is a path but file does not exist', function () {
    Storage::fake('s3');
    $path = 'avatars/nonexistent.jpg';

    $user = User::factory()->create(['avatar_url' => $path]);

    expect($user->avatar_url)->toBeNull();
});

it('hasAvatar returns true when avatar exists and not generating', function () {
    Storage::fake('s3');
    $path = 'avatars/avatar.jpg';
    Storage::disk('s3')->put($path, 'fake content');

    $user = User::factory()->create([
        'avatar_url' => $path,
        'avatar_generating' => false,
    ]);

    expect($user->hasAvatar())->toBeTrue();
});

it('hasAvatar returns false when avatar is generating', function () {
    Storage::fake('s3');
    $path = 'avatars/avatar.jpg';
    Storage::disk('s3')->put($path, 'fake content');

    $user = User::factory()->create([
        'avatar_url' => $path,
        'avatar_generating' => true,
    ]);

    expect($user->hasAvatar())->toBeFalse();
});

it('hasAvatar returns false when avatar_url is null', function () {
    $user = User::factory()->create([
        'avatar_url' => null,
        'avatar_generating' => false,
    ]);

    expect($user->hasAvatar())->toBeFalse();
});

it('isAvatarGenerating returns true when avatar_generating is true', function () {
    $user = User::factory()->create(['avatar_generating' => true]);

    expect($user->isAvatarGenerating())->toBeTrue();
});

it('isAvatarGenerating returns false when avatar_generating is false', function () {
    $user = User::factory()->create(['avatar_generating' => false]);

    expect($user->isAvatarGenerating())->toBeFalse();
});

it('casts is_super_admin to boolean', function () {
    $user = User::factory()->create(['is_super_admin' => true]);

    expect($user->is_super_admin)->toBeTrue()
        ->and(is_bool($user->is_super_admin))->toBeTrue();
});

it('casts password to hashed', function () {
    $password = 'plain-password';
    $user = User::factory()->create(['password' => $password]);

    expect($user->password)->not->toBe($password)
        ->and(\Hash::check($password, $user->password))->toBeTrue();
});

it('handles UnableToCheckFileExistence exception when checking avatar existence', function () {
    $path = 'avatars/avatar.jpg';
    $user = User::factory()->create(['avatar_url' => $path]);

    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('exists')
        ->with($path)
        ->andThrow(new \League\Flysystem\UnableToCheckFileExistence('Cannot check file'));

    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk);

    Log::shouldReceive('warning')->once();

    expect($user->avatar_url)->toBeNull();
});

it('handles generic exception when checking avatar existence', function () {
    $path = 'avatars/avatar.jpg';
    $user = User::factory()->create(['avatar_url' => $path]);

    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('exists')
        ->with($path)
        ->andThrow(new \Exception('Generic error'));

    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk);

    Log::shouldReceive('warning')->once();

    expect($user->avatar_url)->toBeNull();
});

it('handles UnableToCheckFileExistence with S3Exception as previous when checking avatar existence', function () {
    $path = 'avatars/avatar.jpg';
    $user = User::factory()->create(['avatar_url' => $path]);

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
        'S3 error checking avatar existence in storage',
        Mockery::on(function ($context) {
            return isset($context['s3_error_code']) && $context['s3_error_code'] === 'AccessDenied';
        })
    );

    expect($user->avatar_url)->toBeNull();
});

it('handles UnableToCheckFileExistence without S3Exception as previous when checking avatar existence', function () {
    $path = 'avatars/avatar.jpg';
    $user = User::factory()->create(['avatar_url' => $path]);

    $flysystemException = new \League\Flysystem\UnableToCheckFileExistence('Cannot check file');

    $mockDisk = Mockery::mock();
    $mockDisk->shouldReceive('exists')
        ->with($path)
        ->andThrow($flysystemException);

    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mockDisk);

    Log::shouldReceive('warning')->once()->with(
        'Flysystem error checking avatar existence in storage',
        Mockery::on(function ($context) {
            return isset($context['error']) && ! isset($context['s3_error_code']);
        })
    );

    expect($user->avatar_url)->toBeNull();
});

it('handles direct S3Exception when checking avatar existence', function () {
    $path = 'avatars/avatar.jpg';
    $user = User::factory()->create(['avatar_url' => $path]);

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
        'S3 error checking avatar existence in storage',
        Mockery::on(function ($context) {
            return isset($context['s3_error_code']) && $context['s3_error_code'] === 'NoSuchKey';
        })
    );

    expect($user->avatar_url)->toBeNull();
});

it('has sentMessages relationship', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();
    $message = \App\Models\Message::factory()->create([
        'sender_id' => $sender->id,
        'recipient_id' => $recipient->id,
    ]);

    expect($sender->sentMessages)->toHaveCount(1)
        ->and($sender->sentMessages->first()->id)->toBe($message->id);
});

it('has receivedMessages relationship', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();
    $message = \App\Models\Message::factory()->create([
        'sender_id' => $sender->id,
        'recipient_id' => $recipient->id,
    ]);

    expect($recipient->receivedMessages)->toHaveCount(1)
        ->and($recipient->receivedMessages->first()->id)->toBe($message->id);
});

it('returns correct unread messages count', function () {
    $user = User::factory()->create();
    \App\Models\Message::factory()->to($user)->unread()->count(5)->create();
    \App\Models\Message::factory()->to($user)->read()->count(3)->create();

    expect($user->unreadMessagesCount())->toBe(5);
});

it('returns zero when user has no unread messages', function () {
    $user = User::factory()->create();
    \App\Models\Message::factory()->to($user)->read()->count(3)->create();

    expect($user->unreadMessagesCount())->toBe(0);
});

afterEach(function () {
    \Mockery::close();
});
