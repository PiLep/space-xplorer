<?php

use App\Events\UserDeleted;
use App\Listeners\CleanupUserData;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->listener = new CleanupUserData;
});

it('deletes sanctum tokens when user is deleted', function () {
    $user = User::factory()->create();

    // Create some Sanctum tokens
    DB::table('personal_access_tokens')->insert([
        'name' => 'Test Token',
        'token' => hash('sha256', 'test-token'),
        'abilities' => json_encode(['*']),
        'tokenable_type' => User::class,
        'tokenable_id' => $user->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('personal_access_tokens')->insert([
        'name' => 'Another Token',
        'token' => hash('sha256', 'another-token'),
        'abilities' => json_encode(['*']),
        'tokenable_type' => User::class,
        'tokenable_id' => $user->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $tokenCount = DB::table('personal_access_tokens')
        ->where('tokenable_type', User::class)
        ->where('tokenable_id', $user->id)
        ->count();

    expect($tokenCount)->toBe(2);

    $event = new UserDeleted($user);
    $this->listener->handle($event);

    $remainingTokens = DB::table('personal_access_tokens')
        ->where('tokenable_type', User::class)
        ->where('tokenable_id', $user->id)
        ->count();

    expect($remainingTokens)->toBe(0);
});

it('deletes user sessions when user is deleted', function () {
    $user = User::factory()->create();

    // Create some sessions
    DB::table('sessions')->insert([
        'id' => 'session-1',
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test',
        'payload' => 'test',
        'last_activity' => now()->timestamp,
    ]);

    DB::table('sessions')->insert([
        'id' => 'session-2',
        'user_id' => $user->id,
        'ip_address' => '192.168.1.1',
        'user_agent' => 'test',
        'payload' => 'test',
        'last_activity' => now()->timestamp,
    ]);

    $sessionCount = DB::table('sessions')
        ->where('user_id', $user->id)
        ->count();

    expect($sessionCount)->toBe(2);

    $event = new UserDeleted($user);
    $this->listener->handle($event);

    $remainingSessions = DB::table('sessions')
        ->where('user_id', $user->id)
        ->count();

    expect($remainingSessions)->toBe(0);
});

it('handles user with no tokens gracefully', function () {
    $user = User::factory()->create();

    $event = new UserDeleted($user);

    // Should not throw exception
    $this->listener->handle($event);

    expect(true)->toBeTrue(); // Test passes if no exception thrown
});

it('handles user with no sessions gracefully', function () {
    $user = User::factory()->create();

    $event = new UserDeleted($user);

    // Should not throw exception
    $this->listener->handle($event);

    expect(true)->toBeTrue(); // Test passes if no exception thrown
});

it('logs cleanup success', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('User data cleaned up successfully', \Mockery::type('array'));

    $user = User::factory()->create();
    $event = new UserDeleted($user);

    $this->listener->handle($event);
});

it('logs token deletion when tokens exist', function () {
    $user = User::factory()->create();

    DB::table('personal_access_tokens')->insert([
        'name' => 'Test Token',
        'token' => hash('sha256', 'test-token'),
        'abilities' => json_encode(['*']),
        'tokenable_type' => User::class,
        'tokenable_id' => $user->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    Log::shouldReceive('info')
        ->once()
        ->with('Sanctum tokens deleted', \Mockery::type('array'));

    Log::shouldReceive('info')
        ->once()
        ->with('User data cleaned up successfully', \Mockery::type('array'));

    $event = new UserDeleted($user);
    $this->listener->handle($event);
});

it('logs session deletion when sessions exist', function () {
    $user = User::factory()->create();

    DB::table('sessions')->insert([
        'id' => 'session-1',
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test',
        'payload' => 'test',
        'last_activity' => now()->timestamp,
    ]);

    Log::shouldReceive('info')
        ->once()
        ->with('User sessions deleted', \Mockery::type('array'));

    Log::shouldReceive('info')
        ->once()
        ->with('User data cleaned up successfully', \Mockery::type('array'));

    $event = new UserDeleted($user);
    $this->listener->handle($event);
});

it('handles errors gracefully without blocking deletion', function () {
    Log::shouldReceive('error')
        ->once()
        ->with('Error during user data cleanup', \Mockery::type('array'));

    // Create a mock user that will cause an error
    $user = User::factory()->make();
    $user->id = 'invalid-id'; // This will cause an error in DB queries

    $event = new UserDeleted($user);

    // Should not throw exception, should log error instead
    $this->listener->handle($event);

    expect(true)->toBeTrue(); // Test passes if no exception thrown
});

afterEach(function () {
    \Mockery::close();
});

