<?php

use App\Models\Notification;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('belongs to a user', function () {
    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
    ]);

    expect($notification->user)
        ->toBeInstanceOf(User::class)
        ->and($notification->user->id)->toBe($this->user->id);
});

it('has correct fillable attributes', function () {
    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'message_important',
        'title' => 'Test Title',
        'message' => 'Test message',
        'data' => ['key' => 'value'],
        'is_read' => false,
    ]);

    expect($notification->user_id)->toBe($this->user->id)
        ->and($notification->type)->toBe('message_important')
        ->and($notification->title)->toBe('Test Title')
        ->and($notification->message)->toBe('Test message')
        ->and($notification->data)->toBe(['key' => 'value'])
        ->and($notification->is_read)->toBeFalse();
});

it('casts attributes correctly', function () {
    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => true,
        'read_at' => now(),
        'data' => ['key' => 'value'],
    ]);

    expect($notification->is_read)->toBeBool()
        ->and($notification->data)->toBeArray()
        ->and($notification->read_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

it('can filter unread notifications with scope', function () {
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => true,
    ]);

    $unreadCount = Notification::forUser($this->user)->unread()->count();

    expect($unreadCount)->toBe(1);
});

it('can filter read notifications with scope', function () {
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => true,
    ]);

    $readCount = Notification::forUser($this->user)->read()->count();

    expect($readCount)->toBe(1);
});

it('can filter notifications by user with scope', function () {
    $otherUser = User::factory()->create();

    Notification::factory()->create([
        'user_id' => $this->user->id,
    ]);
    Notification::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    $userNotifications = Notification::forUser($this->user)->count();

    expect($userNotifications)->toBe(1);
});

it('can filter notifications by type with scope', function () {
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'message_important',
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'ship_assigned',
    ]);

    $messageNotifications = Notification::forUser($this->user)
        ->byType('message_important')
        ->count();

    expect($messageNotifications)->toBe(1);
});

it('can mark notification as read', function () {
    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
        'read_at' => null,
    ]);

    $result = $notification->markAsRead();

    expect($result)->toBeTrue()
        ->and($notification->fresh()->is_read)->toBeTrue()
        ->and($notification->fresh()->read_at)->not->toBeNull();
});

it('can mark notification as unread', function () {
    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => true,
        'read_at' => now(),
    ]);

    $result = $notification->markAsUnread();

    expect($result)->toBeTrue()
        ->and($notification->fresh()->is_read)->toBeFalse()
        ->and($notification->fresh()->read_at)->toBeNull();
});
