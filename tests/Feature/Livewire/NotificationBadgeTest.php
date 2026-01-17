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

it('renders notification badge component', function () {
    Livewire::test(\App\Livewire\NotificationBadge::class)
        ->assertStatus(200);
});

it('displays zero unread count when no notifications', function () {
    Livewire::test(\App\Livewire\NotificationBadge::class)
        ->assertSet('unreadCount', 0);
});

it('displays correct unread count', function () {
    Notification::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->count(2)->create([
        'user_id' => $this->user->id,
        'is_read' => true,
    ]);

    Livewire::test(\App\Livewire\NotificationBadge::class)
        ->assertSet('unreadCount', 3);
});

it('updates unread count when notifications are added', function () {
    $component = Livewire::test(\App\Livewire\NotificationBadge::class)
        ->assertSet('unreadCount', 0);

    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);

    $component->call('refresh')
        ->assertSet('unreadCount', 1);
});

it('only counts notifications for authenticated user', function () {
    $otherUser = User::factory()->create();

    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);
    Notification::factory()->create([
        'user_id' => $otherUser->id,
        'is_read' => false,
    ]);

    Livewire::test(\App\Livewire\NotificationBadge::class)
        ->assertSet('unreadCount', 1);
});

it('returns zero when user is not authenticated', function () {
    Auth::logout();

    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);

    Livewire::test(\App\Livewire\NotificationBadge::class)
        ->assertSet('unreadCount', 0);
});

it('refreshes computed property on refresh call', function () {
    Notification::factory()->create([
        'user_id' => $this->user->id,
        'is_read' => false,
    ]);

    $component = Livewire::test(\App\Livewire\NotificationBadge::class)
        ->assertSet('unreadCount', 1);

    // Mark notification as read
    Notification::where('user_id', $this->user->id)->first()->markAsRead();

    $component->call('refresh')
        ->assertSet('unreadCount', 0);
});
