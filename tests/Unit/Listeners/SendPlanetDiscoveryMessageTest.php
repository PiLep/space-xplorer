<?php

use App\Events\PlanetExplored;
use App\Listeners\SendPlanetDiscoveryMessage;
use App\Models\Message;
use App\Models\Planet;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->messageService = new MessageService;
    $this->listener = new SendPlanetDiscoveryMessage($this->messageService);
});

it('sends discovery message when planet is explored', function () {
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $event = new PlanetExplored($user, $planet);

    $this->listener->handle($event);

    $message = Message::where('recipient_id', $user->id)
        ->where('type', 'discovery')
        ->first();

    expect($message)->not->toBeNull()
        ->and($message->subject)->toBe('Nouvelle planète découverte')
        ->and($message->content)->toContain($planet->name)
        ->and($message->metadata['planet_id'])->toBe($planet->id);
});

it('logs success when discovery message is sent', function () {
    Log::shouldReceive('info')->once()->with(
        'Planet discovery message sent to user',
        Mockery::on(function ($context) {
            return isset($context['user_id']) && isset($context['planet_id']);
        })
    );

    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $event = new PlanetExplored($user, $planet);

    $this->listener->handle($event);
});

it('handles errors gracefully without blocking exploration', function () {
    Log::shouldReceive('error')->once();

    // Create a mock service that throws an exception
    $mockService = \Mockery::mock(MessageService::class);
    $mockService->shouldReceive('createDiscoveryMessage')
        ->once()
        ->andThrow(new \Exception('Test error'));

    $listener = new SendPlanetDiscoveryMessage($mockService);
    $user = User::factory()->create();
    $planet = Planet::factory()->create();
    $event = new PlanetExplored($user, $planet);

    // Should not throw exception
    expect(fn () => $listener->handle($event))->not->toThrow(\Exception::class);
});

afterEach(function () {
    \Mockery::close();
});

