<?php

use App\Events\UserDeleted;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('dispatches UserDeleted event when user is deleted', function () {
    Event::fake([UserDeleted::class]);

    $user = User::factory()->create();
    $userId = $user->id;

    $user->delete();

    Event::assertDispatched(UserDeleted::class, function ($event) use ($userId) {
        return $event->user->id === $userId;
    });
});

it('dispatches event with correct user instance', function () {
    Event::fake([UserDeleted::class]);

    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $user->delete();

    Event::assertDispatched(UserDeleted::class, function ($event) use ($user) {
        return $event->user->id === $user->id
            && $event->user->name === 'Test User'
            && $event->user->email === 'test@example.com';
    });
});

it('dispatches event only once per deletion', function () {
    Event::fake([UserDeleted::class]);

    $user = User::factory()->create();

    $user->delete();

    Event::assertDispatched(UserDeleted::class, 1);
});

