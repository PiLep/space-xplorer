<?php

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->service = new NotificationService;
});

it('creates a notification successfully', function () {
    Config::set('notifications.types', [
        'message_important' => ['description' => 'Test'],
    ]);

    $notification = $this->service->create(
        $this->user,
        'message_important',
        'Test Title',
        'Test message',
        ['key' => 'value']
    );

    expect($notification)
        ->toBeInstanceOf(Notification::class)
        ->and($notification->user_id)->toBe($this->user->id)
        ->and($notification->type)->toBe('message_important')
        ->and($notification->title)->toBe('Test Title')
        ->and($notification->message)->toBe('Test message')
        ->and($notification->data)->toBe(['key' => 'value'])
        ->and($notification->is_read)->toBeFalse();
});

it('throws exception for invalid notification type', function () {
    Config::set('notifications.types', [
        'message_important' => ['description' => 'Test'],
    ]);

    expect(fn () => $this->service->create(
        $this->user,
        'invalid_type',
        'Test Title',
        'Test message'
    ))->toThrow(\InvalidArgumentException::class);
});

it('throws exception for message exceeding max length', function () {
    Config::set('notifications.types', [
        'message_important' => ['description' => 'Test'],
    ]);
    Config::set('notifications.max_message_length', 10);

    $longMessage = str_repeat('a', 11);

    expect(fn () => $this->service->create(
        $this->user,
        'message_important',
        'Test Title',
        $longMessage
    ))->toThrow(\InvalidArgumentException::class);
});

it('marks a notification as read', function () {
    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);

    $result = $this->service->markAsRead($notification);

    expect($result)->toBeTrue()
        ->and($notification->fresh()->is_read)->toBeTrue();
});

it('marks all notifications as read for a user', function () {
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => true,
    ]);

    $count = $this->service->markAllAsRead($this->user);

    expect($count)->toBe(2)
        ->and(Notification::forUser($this->user)->unread()->count())->toBe(0);
});

it('gets unread count for a user', function () {
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => true,
    ]);

    $count = $this->service->getUnreadCount($this->user);

    expect($count)->toBe(2);
});

it('gets notifications for a user with limit', function () {
    Notification::factory()->count(5)->create([
        'user_id' => $this->user->id,
    ]);

    $notifications = $this->service->getNotificationsForUser($this->user, 3);

    expect($notifications)->toHaveCount(3)
        ->and($notifications->first()->user_id)->toBe($this->user->id);
});

it('gets unread notifications for a user with limit', function () {
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => true,
    ]);

    $notifications = $this->service->getUnreadNotificationsForUser($this->user, 10);

    expect($notifications)->toHaveCount(2)
        ->and($notifications->every(fn ($n) => $n->is_read === false))->toBeTrue();
});

it('orders notifications by created_at desc', function () {
    $oldNotification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'created_at' => now()->subDay(),
    ]);
    $newNotification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'created_at' => now(),
    ]);

    $notifications = $this->service->getNotificationsForUser($this->user, 10);

    expect($notifications->first()->id)->toBe($newNotification->id)
        ->and($notifications->last()->id)->toBe($oldNotification->id);
});
