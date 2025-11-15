<?php

use App\Events\PlanetCreated;
use App\Listeners\SendHomePlanetMessage;
use App\Models\Message;
use App\Models\Planet;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->messageService = new MessageService;
    $this->listener = new SendHomePlanetMessage($this->messageService);
});

it('sends home planet message when planet is created for home planet', function () {
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $user->update(['home_planet_id' => $planet->id]);

    $event = new PlanetCreated($planet);
    $this->listener->handle($event);

    $message = Message::where('recipient_id', $user->id)
        ->where('type', 'discovery')
        ->first();

    expect($message)->not->toBeNull()
        ->and($message->subject)->toBe('Votre planète d\'origine')
        ->and($message->content)->toContain($planet->name);
});

it('does not send message when planet is not a home planet', function () {
    $planet = Planet::factory()->create();
    // No users have this as home planet

    $event = new PlanetCreated($planet);
    $this->listener->handle($event);

    $messageCount = Message::count();
    expect($messageCount)->toBe(0);
});

it('sends message to all users with planet as home planet', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $planet = Planet::factory()->create();
    $user1->update(['home_planet_id' => $planet->id]);
    $user2->update(['home_planet_id' => $planet->id]);

    $event = new PlanetCreated($planet);
    $this->listener->handle($event);

    $messages = Message::whereIn('recipient_id', [$user1->id, $user2->id])->get();
    expect($messages)->toHaveCount(2);
});

it('logs success when home planet message is sent', function () {
    Log::shouldReceive('info')->once()->with(
        'Home planet message sent to users',
        Mockery::on(function ($context) {
            return isset($context['planet_id']) && isset($context['user_count']);
        })
    );
    Log::shouldReceive('error')->zeroOrMoreTimes();

    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $user->update(['home_planet_id' => $planet->id]);

    $event = new PlanetCreated($planet);
    $this->listener->handle($event);
});

it('handles errors gracefully without blocking planet creation', function () {
    Log::shouldReceive('error')->once();

    // Create a mock service that throws an exception
    $mockService = \Mockery::mock(MessageService::class);
    $mockService->shouldReceive('createDiscoveryMessage')
        ->once()
        ->andThrow(new \Exception('Test error'));

    $listener = new SendHomePlanetMessage($mockService);
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $user->update(['home_planet_id' => $planet->id]);

    $event = new PlanetCreated($planet);

    // Should not throw exception
    expect(fn () => $listener->handle($event))->not->toThrow(\Exception::class);
});

afterEach(function () {
    \Mockery::close();
});

