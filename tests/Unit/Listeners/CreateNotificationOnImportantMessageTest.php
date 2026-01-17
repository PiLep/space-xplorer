<?php

use App\Events\MessageReceived;
use App\Listeners\CreateNotificationOnImportantMessage;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    Config::set('notifications.types', [
        'message_important' => ['description' => 'Test'],
    ]);
    Config::set('notifications.max_message_length', 200);

    $this->notificationService = new NotificationService;
    $this->listener = new CreateNotificationOnImportantMessage($this->notificationService);
});

it('creates notification when important message is received', function () {
    $user = User::factory()->create();
    $message = Message::factory()->create([
        'recipient_id' => $user->id,
        'is_important' => true,
        'subject' => 'Message important',
    ]);

    $event = new MessageReceived($message, $user);

    $this->listener->handle($event);

    $notification = Notification::where('user_id', $user->id)
        ->where('type', 'message_important')
        ->first();

    expect($notification)->not->toBeNull()
        ->and($notification->title)->toBe('Nouveau message important')
        ->and($notification->message)->toContain('Nouveau message : Message important')
        ->and($notification->type)->toBe('message_important')
        ->and($notification->data['message_id'])->toBe($message->id)
        ->and($notification->data['inbox_url'])->toBe('/inbox?message='.$message->id)
        ->and($notification->is_read)->toBeFalse();
});

it('does not create notification for non-important message', function () {
    $user = User::factory()->create();
    $message = Message::factory()->create([
        'recipient_id' => $user->id,
        'is_important' => false,
    ]);

    $event = new MessageReceived($message, $user);

    $this->listener->handle($event);

    $notification = Notification::where('user_id', $user->id)
        ->where('type', 'message_important')
        ->first();

    expect($notification)->toBeNull();
});

it('truncates notification message if too long', function () {
    Config::set('notifications.max_message_length', 50);

    $user = User::factory()->create();
    $longSubject = str_repeat('A', 100);
    $message = Message::factory()->create([
        'recipient_id' => $user->id,
        'is_important' => true,
        'subject' => $longSubject,
    ]);

    $event = new MessageReceived($message, $user);

    $this->listener->handle($event);

    $notification = Notification::where('user_id', $user->id)
        ->where('type', 'message_important')
        ->first();

    expect($notification)->not->toBeNull()
        ->and(strlen($notification->message))->toBeLessThanOrEqual(50)
        ->and($notification->message)->toEndWith('...');
});

it('includes correct inbox url in notification data', function () {
    $user = User::factory()->create();
    $message = Message::factory()->create([
        'recipient_id' => $user->id,
        'is_important' => true,
        'subject' => 'Test',
    ]);

    $event = new MessageReceived($message, $user);

    $this->listener->handle($event);

    $notification = Notification::where('user_id', $user->id)
        ->where('type', 'message_important')
        ->first();

    expect($notification->data['inbox_url'])->toBe('/inbox?message='.$message->id)
        ->and($notification->data['message_id'])->toBe($message->id);
});

it('does not duplicate message content in notification', function () {
    $user = User::factory()->create();
    $message = Message::factory()->create([
        'recipient_id' => $user->id,
        'is_important' => true,
        'subject' => 'Test Subject',
        'content' => 'This is a very long message content that should not be duplicated in the notification',
    ]);

    $event = new MessageReceived($message, $user);

    $this->listener->handle($event);

    $notification = Notification::where('user_id', $user->id)
        ->where('type', 'message_important')
        ->first();

    // Notification should only contain subject, not full content
    expect($notification->message)->not->toContain('This is a very long message content')
        ->and($notification->message)->toContain('Test Subject');
});

it('handles errors gracefully without blocking message creation', function () {
    Log::shouldReceive('error')->once();

    // Create a mock service that throws an exception
    $mockService = \Mockery::mock(NotificationService::class);
    $mockService->shouldReceive('create')
        ->once()
        ->andThrow(new \Exception('Test error'));

    $listener = new CreateNotificationOnImportantMessage($mockService);
    $user = User::factory()->create();
    $message = Message::factory()->create([
        'recipient_id' => $user->id,
        'is_important' => true,
    ]);
    $event = new MessageReceived($message, $user);

    // Should not throw exception
    expect(fn () => $listener->handle($event))->not->toThrow(\Exception::class);
});

it('logs error details when notification creation fails', function () {
    Log::shouldReceive('error')->once()->with(
        'Failed to create notification for important message',
        \Mockery::on(function ($context) {
            return isset($context['message_id'])
                && isset($context['recipient_id'])
                && isset($context['error']);
        })
    );

    // Create a mock service that throws an exception
    $mockService = \Mockery::mock(NotificationService::class);
    $mockService->shouldReceive('create')
        ->once()
        ->andThrow(new \Exception('Test error'));

    $listener = new CreateNotificationOnImportantMessage($mockService);
    $user = User::factory()->create();
    $message = Message::factory()->create([
        'recipient_id' => $user->id,
        'is_important' => true,
    ]);
    $event = new MessageReceived($message, $user);

    $listener->handle($event);
});

afterEach(function () {
    \Mockery::close();
});
