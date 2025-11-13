<?php

use App\Events\UserRegistered;
use App\Listeners\SendWelcomeMessage;
use App\Models\Message;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->messageService = new MessageService;
    $this->listener = new SendWelcomeMessage($this->messageService);
});

it('sends welcome message when user registers', function () {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    $this->listener->handle($event);

    $message = Message::where('recipient_id', $user->id)
        ->where('type', 'welcome')
        ->first();

    expect($message)->not->toBeNull()
        ->and($message->subject)->toBe('Bienvenue dans l\'univers Stellar')
        ->and($message->content)->toContain($user->name);
});

it('logs success when welcome message is sent', function () {
    Log::shouldReceive('info')->once()->with(
        'Welcome message sent to user',
        Mockery::on(function ($context) {
            return isset($context['user_id']);
        })
    );

    $user = User::factory()->create();
    $event = new UserRegistered($user);

    $this->listener->handle($event);
});

it('handles errors gracefully without blocking registration', function () {
    Log::shouldReceive('error')->once();

    // Create a mock service that throws an exception
    $mockService = \Mockery::mock(MessageService::class);
    $mockService->shouldReceive('createWelcomeMessage')
        ->once()
        ->andThrow(new \Exception('Test error'));

    $listener = new SendWelcomeMessage($mockService);
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    // Should not throw exception
    expect(fn () => $listener->handle($event))->not->toThrow(\Exception::class);
});

afterEach(function () {
    \Mockery::close();
});

