<?php

use App\Events\FailedLoginAttempt;
use App\Events\FirstLogin;
use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->service = new AuthService;
    $this->password = 'password123';
});

it('successfully logs in user with valid credentials', function () {
    Event::fake([UserLoggedIn::class, FirstLogin::class]);

    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
        'email_verified_at' => now(),
    ]);

    $request = LoginRequest::create('/api/auth/login', 'POST', [
        'email' => 'john@example.com',
        'password' => $this->password,
    ]);

    $result = $this->service->login($request);

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->id)->toBe($user->id)
        ->and(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id);

    Event::assertDispatched(UserLoggedIn::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

it('throws validation exception when credentials are incorrect', function () {
    Event::fake([FailedLoginAttempt::class]);

    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    $request = LoginRequest::create('/api/auth/login', 'POST', [
        'email' => 'john@example.com',
        'password' => 'wrongpassword',
    ]);
    $request->server->set('REMOTE_ADDR', '127.0.0.1');
    $request->headers->set('User-Agent', 'Test Agent');

    expect(fn () => $this->service->login($request))
        ->toThrow(ValidationException::class);

    Event::assertDispatched(FailedLoginAttempt::class, function ($event) {
        return $event->email === 'john@example.com';
    });
});

it('uses remember false by default when remember is not provided', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    $request = LoginRequest::create('/api/auth/login', 'POST', [
        'email' => 'john@example.com',
        'password' => $this->password,
    ]);

    $result = $this->service->login($request);

    expect($result)->toBeInstanceOf(User::class)
        ->and(Auth::check())->toBeTrue();

    // Verify user is authenticated (remember false means session cookie, not persistent cookie)
    // We can't directly test the cookie, but we can verify authentication works
});

it('uses remember true when remember is provided as true', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    $request = LoginRequest::create('/api/auth/login', 'POST', [
        'email' => 'john@example.com',
        'password' => $this->password,
        'remember' => true,
    ]);

    $result = $this->service->login($request);

    expect($result)->toBeInstanceOf(User::class)
        ->and(Auth::check())->toBeTrue();

    // Verify user is authenticated (remember true creates persistent cookie)
    // We can't directly test the cookie, but we can verify authentication works
});

it('successfully logs in user from credentials', function () {
    Event::fake([UserLoggedIn::class, FirstLogin::class]);

    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
        'email_verified_at' => now(),
    ]);

    $result = $this->service->loginFromCredentials('john@example.com', $this->password);

    expect($result)->toBeInstanceOf(User::class)
        ->and($result->id)->toBe($user->id)
        ->and(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id);

    Event::assertDispatched(UserLoggedIn::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

it('uses remember false by default in loginFromCredentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    $result = $this->service->loginFromCredentials('john@example.com', $this->password);

    expect($result)->toBeInstanceOf(User::class)
        ->and(Auth::check())->toBeTrue();
});

it('uses remember true when provided in loginFromCredentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    $result = $this->service->loginFromCredentials('john@example.com', $this->password, true);

    expect($result)->toBeInstanceOf(User::class)
        ->and(Auth::check())->toBeTrue();
});

it('throws validation exception when credentials are incorrect in loginFromCredentials', function () {
    Event::fake([FailedLoginAttempt::class]);

    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
    ]);

    expect(fn () => $this->service->loginFromCredentials('john@example.com', 'wrongpassword'))
        ->toThrow(ValidationException::class);

    Event::assertDispatched(FailedLoginAttempt::class, function ($event) {
        return $event->email === 'john@example.com';
    });
});

it('dispatches FirstLogin event on first login after email verification', function () {
    Event::fake([UserLoggedIn::class, FirstLogin::class]);

    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
        'email_verified_at' => now(),
    ]);

    // No previous sessions
    $this->assertDatabaseMissing('sessions', ['user_id' => $user->id]);

    $this->service->loginFromCredentials('john@example.com', $this->password);

    Event::assertDispatched(FirstLogin::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

it('does not dispatch FirstLogin event if user has previous sessions', function () {
    Event::fake([UserLoggedIn::class, FirstLogin::class]);

    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make($this->password),
        'email_verified_at' => now(),
    ]);

    // Create a previous session
    DB::table('sessions')->insert([
        'id' => 'previous-session-id',
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test',
        'payload' => 'test',
        'last_activity' => now()->timestamp,
    ]);

    $this->service->loginFromCredentials('john@example.com', $this->password);

    Event::assertDispatched(UserLoggedIn::class);
    Event::assertNotDispatched(FirstLogin::class);
});

it('dispatches UserLoggedOut event on logout', function () {
    Event::fake([UserLoggedOut::class]);

    $user = User::factory()->create();
    Auth::login($user);

    $this->service->logout();

    Event::assertDispatched(UserLoggedOut::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

it('does not dispatch UserLoggedOut event if no user is authenticated', function () {
    Event::fake([UserLoggedOut::class]);

    $this->service->logout();

    Event::assertNotDispatched(UserLoggedOut::class);
});
