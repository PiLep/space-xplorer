<?php

use App\Models\Message;
use App\Models\User;

it('has sender relationship', function () {
    $sender = User::factory()->create();
    $recipient = User::factory()->create();
    $message = Message::factory()->create([
        'sender_id' => $sender->id,
        'recipient_id' => $recipient->id,
    ]);

    expect($message->sender)->not->toBeNull()
        ->and($message->sender->id)->toBe($sender->id);
});

it('has recipient relationship', function () {
    $recipient = User::factory()->create();
    $message = Message::factory()->create(['recipient_id' => $recipient->id]);

    expect($message->recipient)->not->toBeNull()
        ->and($message->recipient->id)->toBe($recipient->id);
});

it('has null sender for system messages', function () {
    $recipient = User::factory()->create();
    $message = Message::factory()->create([
        'sender_id' => null,
        'recipient_id' => $recipient->id,
    ]);

    expect($message->sender)->toBeNull();
});

it('scopeUnread filters unread messages', function () {
    Message::factory()->unread()->create();
    Message::factory()->read()->create();
    Message::factory()->unread()->create();

    $unread = Message::unread()->get();

    expect($unread)->toHaveCount(2)
        ->and($unread->every(fn ($msg) => $msg->is_read === false))->toBeTrue();
});

it('scopeRead filters read messages', function () {
    Message::factory()->read()->create();
    Message::factory()->unread()->create();
    Message::factory()->read()->create();

    $read = Message::read()->get();

    expect($read)->toHaveCount(2)
        ->and($read->every(fn ($msg) => $msg->is_read === true))->toBeTrue();
});

it('scopeImportant filters important messages', function () {
    Message::factory()->important()->create();
    Message::factory()->create();
    Message::factory()->important()->create();

    $important = Message::important()->get();

    expect($important)->toHaveCount(2)
        ->and($important->every(fn ($msg) => $msg->is_important === true))->toBeTrue();
});

it('scopeByType filters messages by type', function () {
    Message::factory()->ofType('welcome')->create();
    Message::factory()->ofType('discovery')->create();
    Message::factory()->ofType('welcome')->create();

    $welcome = Message::byType('welcome')->get();

    expect($welcome)->toHaveCount(2)
        ->and($welcome->every(fn ($msg) => $msg->type === 'welcome'))->toBeTrue();
});

it('scopeForUser filters messages for specific user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Message::factory()->to($user1)->create();
    Message::factory()->to($user2)->create();
    Message::factory()->to($user1)->create();

    $user1Messages = Message::forUser($user1)->get();

    expect($user1Messages)->toHaveCount(2)
        ->and($user1Messages->every(fn ($msg) => $msg->recipient_id === $user1->id))->toBeTrue();
});

it('markAsRead marks message as read', function () {
    $message = Message::factory()->unread()->create();

    $result = $message->markAsRead();

    expect($result)->toBeTrue()
        ->and($message->fresh()->is_read)->toBeTrue()
        ->and($message->fresh()->read_at)->not->toBeNull();
});

it('markAsUnread marks message as unread', function () {
    $message = Message::factory()->read()->create();

    $result = $message->markAsUnread();

    expect($result)->toBeTrue()
        ->and($message->fresh()->is_read)->toBeFalse()
        ->and($message->fresh()->read_at)->toBeNull();
});

it('casts metadata to array', function () {
    $metadata = ['planet_id' => '123', 'type' => 'discovery'];
    $message = Message::factory()->create(['metadata' => $metadata]);

    expect($message->metadata)->toBeArray()
        ->and($message->metadata)->toBe($metadata);
});

it('casts is_read to boolean', function () {
    $message = Message::factory()->create(['is_read' => true]);

    expect($message->is_read)->toBeTrue()
        ->and(is_bool($message->is_read))->toBeTrue();
});

it('casts is_important to boolean', function () {
    $message = Message::factory()->create(['is_important' => true]);

    expect($message->is_important)->toBeTrue()
        ->and(is_bool($message->is_important))->toBeTrue();
});

it('casts read_at to datetime', function () {
    $readAt = now();
    $message = Message::factory()->create(['read_at' => $readAt]);

    expect($message->read_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and($message->read_at->format('Y-m-d H:i:s'))->toBe($readAt->format('Y-m-d H:i:s'));
});

