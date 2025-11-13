<?php

use App\Events\DiscoveryMade;
use App\Listeners\SendSpecialDiscoveryMessage;
use App\Models\Message;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->messageService = new MessageService;
    $this->listener = new SendSpecialDiscoveryMessage($this->messageService);
});

it('sends discovery message when special discovery is made', function () {
    $user = User::factory()->create();
    $event = new DiscoveryMade($user, 'ancient_artifact', ['name' => 'Ancient Artifact']);

    $this->listener->handle($event);

    $message = Message::where('recipient_id', $user->id)
        ->where('type', 'discovery')
        ->first();

    expect($message)->not->toBeNull()
        ->and($message->subject)->toBe('Découverte spéciale')
        ->and($message->metadata['type'])->toBe('discovery')
        ->and($message->metadata['discovery_type'])->toBe('ancient_artifact');
});

it('logs success when special discovery message is sent', function () {
    Log::shouldReceive('info')->once()->with(
        'Special discovery message sent to user',
        Mockery::on(function ($context) {
            return isset($context['user_id']) && isset($context['discovery_type']);
        })
    );

    $user = User::factory()->create();
    $event = new DiscoveryMade($user, 'ancient_artifact', []);

    $this->listener->handle($event);
});

it('handles errors gracefully without blocking discovery', function () {
    Log::shouldReceive('error')->once();

    // Create a mock service that throws an exception
    $mockService = \Mockery::mock(MessageService::class);
    $mockService->shouldReceive('createDiscoveryMessage')
        ->once()
        ->andThrow(new \Exception('Test error'));

    $listener = new SendSpecialDiscoveryMessage($mockService);
    $user = User::factory()->create();
    $event = new DiscoveryMade($user, 'ancient_artifact', []);

    // Should not throw exception
    expect(fn () => $listener->handle($event))->not->toThrow(\Exception::class);
});

afterEach(function () {
    \Mockery::close();
});

