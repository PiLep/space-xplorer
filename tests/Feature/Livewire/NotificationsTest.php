<?php

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);
    Auth::login($this->user);
});

it('renders notifications component', function () {
    Livewire::test(\App\Livewire\Notifications::class)
        ->assertStatus(200);
});

it('initializes filters from query parameters', function () {
    Livewire::withQueryParams(['filter' => 'unread', 'type' => 'message_important'])
        ->test(\App\Livewire\Notifications::class)
        ->assertSet('filter', 'unread')
        ->assertSet('typeFilter', 'message_important');
});

it('defaults to all filters when invalid query parameters provided', function () {
    Livewire::withQueryParams(['filter' => 'invalid', 'type' => 'invalid'])
        ->test(\App\Livewire\Notifications::class)
        ->assertSet('filter', 'all')
        ->assertSet('typeFilter', 'all');
});

it('displays paginated notifications', function () {
    Notification::factory()->count(15)->create([
        'user_id' => $this->user->id,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->assertSee('list_notifications')
        ->assertSee('access_notifications');
});

it('filters notifications by all status', function () {
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => true,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->call('updateFilter', 'all')
        ->assertSet('filter', 'all');
});

it('filters notifications by unread status', function () {
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => true,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->call('updateFilter', 'unread')
        ->assertSet('filter', 'unread');
});

it('filters notifications by read status', function () {
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => true,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->call('updateFilter', 'read')
        ->assertSet('filter', 'read');
});

it('filters notifications by type', function () {
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'message_important',
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'ship_assigned',
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->call('updateTypeFilter', 'message_important')
        ->assertSet('typeFilter', 'message_important');
});

it('displays unread count correctly', function () {
    Notification::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->count(2)->create([
        'user_id' => $this->user->id,
        'is_read' => true,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->assertSet('unreadCount', 3);
});

it('displays read count correctly', function () {
    Notification::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->count(2)->create([
        'user_id' => $this->user->id,
        'is_read' => true,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->assertSet('readCount', 2);
});

it('marks a notification as read', function () {
    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->call('markAsRead', $notification->id)
        ->assertDispatched('notification-read');

    expect($notification->fresh()->is_read)->toBeTrue();
});

it('does not mark notification as read if user is not authenticated', function () {
    Auth::logout();

    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->call('markAsRead', $notification->id);

    expect($notification->fresh()->is_read)->toBeFalse();
});

it('does not mark another users notification as read', function () {
    $otherUser = User::factory()->create();
    $notification = Notification::factory()->create([
        'user_id' => $otherUser->id,
        'is_read' => false,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->call('markAsRead', $notification->id);

    expect($notification->fresh()->is_read)->toBeFalse();
});

it('marks all notifications as read', function () {
    Notification::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->call('markAllAsRead')
        ->assertDispatched('notifications-all-read');

    expect(Notification::forUser($this->user)->unread()->count())->toBe(0);
});

it('deletes a notification', function () {
    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->call('delete', $notification->id)
        ->assertDispatched('notification-deleted');

    expect(Notification::find($notification->id))->toBeNull();
});

it('deletes all read notifications', function () {
    Notification::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'is_read' => true,
    ]);
    Notification::factory()->count(2)->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->call('deleteAllRead')
        ->assertDispatched('notifications-deleted', count: 3);

    expect(Notification::forUser($this->user)->read()->count())->toBe(0)
        ->and(Notification::forUser($this->user)->unread()->count())->toBe(2);
});

it('returns correct notification url for message_important type', function () {
    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'message_important',
        'data' => ['inbox_url' => '/inbox'],
    ]);

    $component = Livewire::test(\App\Livewire\Notifications::class);
    $url = $component->instance()->getNotificationUrl($notification);

    expect($url)->toBe('/inbox');
});

it('returns default url for unknown notification type', function () {
    $notification = Notification::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'unknown_type',
    ]);

    $component = Livewire::test(\App\Livewire\Notifications::class);
    $url = $component->instance()->getNotificationUrl($notification);

    expect($url)->toBe('/notifications');
});

it('returns empty paginator when user is not authenticated', function () {
    Auth::logout();

    Livewire::test(\App\Livewire\Notifications::class)
        ->assertSet('unreadCount', 0)
        ->assertSet('readCount', 0);
});

it('applies type filter to unread count', function () {
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'message_important',
        'is_read' => false,
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'ship_assigned',
        'is_read' => false,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->call('updateTypeFilter', 'message_important')
        ->assertSet('unreadCount', 1);
});

it('applies type filter to read count', function () {
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'message_important',
        'is_read' => true,
    ]);
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'type' => 'ship_assigned',
        'is_read' => true,
    ]);

    Livewire::test(\App\Livewire\Notifications::class)
        ->call('updateTypeFilter', 'message_important')
        ->assertSet('readCount', 1);
});
