<?php

use App\Events\MessageReceived;
use App\Events\TestEvent;
use App\Events\UserRegistered;
use App\Jobs\LogEventToDatabase;
use App\Listeners\LogEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();
    $this->listener = new LogEvent;
});

it('dispatches LogEventToDatabase job when App\Events event is handled', function () {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) use ($user) {
        return $job->eventType === UserRegistered::class
            && $job->userId === $user->id
            && isset($job->eventData['user']);
    });
});

it('extracts user_id from event with $user property', function () {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) use ($user) {
        return $job->userId === $user->id;
    });
});

it('extracts user_id from event with $recipient property', function () {
    $recipient = User::factory()->create();
    $message = Message::factory()->create(['recipient_id' => $recipient->id]);
    $event = new MessageReceived($message, $recipient);

    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) use ($recipient) {
        return $job->userId === $recipient->id;
    });
});

it('extracts user_id from authenticated user when event has no user property', function () {
    $user = User::factory()->create();
    Auth::login($user);

    // Create a simple event without user property (but in App\Events namespace)
    $event = new TestEvent;

    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) use ($user) {
        return $job->userId === $user->id; // Should use authenticated user
    });
});

it('ignores events not in App\Events namespace', function () {
    $event = new \Illuminate\Auth\Events\Login('web', User::factory()->create(), false);

    $this->listener->handle($event);

    Queue::assertNothingPushed();
});

it('ignores non-object values', function () {
    $this->listener->handle('not an object');
    $this->listener->handle(null);
    $this->listener->handle(123);
    $this->listener->handle(['array']);

    Queue::assertNothingPushed();
});

it('extracts event data from public properties', function () {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) use ($user) {
        return isset($job->eventData['user'])
            && isset($job->eventData['user']['id'])
            && $job->eventData['user']['id'] === $user->id;
    });
});

it('excludes Laravel internal properties from event data', function () {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) {
        return ! isset($job->eventData['shouldBroadcast'])
            && ! isset($job->eventData['connection'])
            && ! isset($job->eventData['queue']);
    });
});

it('serializes Eloquent models correctly', function () {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) use ($user) {
        $userData = $job->eventData['user'];

        return isset($userData['_type'])
            && isset($userData['id'])
            && $userData['id'] === $user->id
            && isset($userData['class']);
    });
});

it('extracts IP address from request', function () {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    // Mock request
    request()->server->set('REMOTE_ADDR', '192.168.1.1');

    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) {
        return $job->ipAddress === '192.168.1.1';
    });
});

it('handles missing IP address gracefully', function () {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    // No request context (CLI)
    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) {
        return $job->ipAddress === null || is_string($job->ipAddress);
    });
});

it('extracts user agent from request', function () {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    // Mock request
    request()->headers->set('User-Agent', 'Mozilla/5.0 Test Browser');

    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) {
        return $job->userAgent === 'Mozilla/5.0 Test Browser';
    });
});

it('handles missing user agent gracefully', function () {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    // No request context (CLI)
    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) {
        return $job->userAgent === null || is_string($job->userAgent);
    });
});

it('extracts session ID from session', function () {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    // Start session
    session()->start();
    $sessionId = session()->getId();

    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) use ($sessionId) {
        return $job->sessionId === $sessionId;
    });
});

it('handles missing session gracefully', function () {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    // No session context (CLI)
    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) {
        return $job->sessionId === null || is_string($job->sessionId);
    });
});

it('logs error but does not throw exception on failure', function () {
    Log::shouldReceive('error')
        ->once()
        ->with('Failed to process event for logging', \Mockery::type('array'));

    // Create an event that will cause an error during extraction
    // The event will throw an exception when getUser() is called
    $failingEvent = new class extends \App\Events\TestEvent
    {
        public function getUser()
        {
            throw new \Exception('Test error');
        }
    };

    // Should not throw exception, should log error
    expect(fn () => $this->listener->handle($failingEvent))->not->toThrow(\Exception::class);
});

it('logs info when processing App event', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('LogEvent: Processing App event', \Mockery::type('array'));

    Log::shouldReceive('info')
        ->atLeast()
        ->once()
        ->with('LogEvent: Extracted data', \Mockery::type('array'));

    Log::shouldReceive('info')
        ->once()
        ->with('LogEvent: Job dispatched', \Mockery::type('array'));

    $user = User::factory()->create();
    $event = new UserRegistered($user);

    $this->listener->handle($event);
});

it('handles event with getUser method', function () {
    $user = User::factory()->create();

    // Create event with getUser method
    $event = new class($user) extends \App\Events\UserRegistered
    {
        private $internalUser;

        public function __construct($user)
        {
            $this->internalUser = $user;
            // Don't call parent constructor
        }

        public function getUser()
        {
            return $this->internalUser;
        }
    };

    $this->listener->handle($event);

    Queue::assertPushed(LogEventToDatabase::class, function ($job) use ($user) {
        return $job->userId === $user->id;
    });
});

afterEach(function () {
    \Mockery::close();
    Auth::logout();
});

