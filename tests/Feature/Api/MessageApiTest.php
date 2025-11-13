<?php

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);
    $this->otherUser = User::factory()->create();
});

it('returns paginated list of messages for authenticated user', function () {
    Sanctum::actingAs($this->user);
    Message::factory()->to($this->user)->count(25)->create();

    $response = $this->getJson('/api/messages');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'messages' => [],
                'pagination' => ['current_page', 'last_page', 'per_page', 'total'],
            ],
            'status',
        ])
        ->assertJsonCount(20, 'data.messages')
        ->assertJsonPath('data.pagination.total', 25)
        ->assertJsonPath('data.pagination.per_page', 20);
});

it('filters messages by unread status', function () {
    Sanctum::actingAs($this->user);
    Message::factory()->to($this->user)->unread()->count(5)->create();
    Message::factory()->to($this->user)->read()->count(3)->create();

    $response = $this->getJson('/api/messages?filter=unread');

    $response->assertStatus(200)
        ->assertJsonPath('data.pagination.total', 5)
        ->assertJsonCount(5, 'data.messages');
});

it('filters messages by read status', function () {
    Sanctum::actingAs($this->user);
    Message::factory()->to($this->user)->unread()->count(5)->create();
    Message::factory()->to($this->user)->read()->count(3)->create();

    $response = $this->getJson('/api/messages?filter=read');

    $response->assertStatus(200)
        ->assertJsonPath('data.pagination.total', 3)
        ->assertJsonCount(3, 'data.messages');
});

it('filters messages by type', function () {
    Sanctum::actingAs($this->user);
    Message::factory()->to($this->user)->ofType('welcome')->count(3)->create();
    Message::factory()->to($this->user)->ofType('discovery')->count(2)->create();

    $response = $this->getJson('/api/messages?type=welcome');

    $response->assertStatus(200)
        ->assertJsonPath('data.pagination.total', 3)
        ->assertJsonCount(3, 'data.messages');
});

it('returns 401 when not authenticated', function () {
    $response = $this->getJson('/api/messages');

    $response->assertStatus(401);
});

it('only returns messages for authenticated user', function () {
    Sanctum::actingAs($this->user);
    Message::factory()->to($this->user)->count(3)->create();
    Message::factory()->to($this->otherUser)->count(2)->create();

    $response = $this->getJson('/api/messages');

    $response->assertStatus(200)
        ->assertJsonPath('data.pagination.total', 3);
});

it('returns message details and marks as read', function () {
    Sanctum::actingAs($this->user);
    $message = Message::factory()->to($this->user)->unread()->create();

    $response = $this->getJson("/api/messages/{$message->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'message' => [
                    'id',
                    'sender_id',
                    'recipient_id',
                    'type',
                    'subject',
                    'content',
                    'is_read',
                    'read_at',
                    'is_important',
                    'metadata',
                    'created_at',
                ],
            ],
            'status',
        ])
        ->assertJsonPath('data.message.id', $message->id)
        ->assertJsonPath('data.message.is_read', true);

    expect($message->fresh()->is_read)->toBeTrue()
        ->and($message->fresh()->read_at)->not->toBeNull();
});

it('returns 404 when message does not exist', function () {
    Sanctum::actingAs($this->user);

    $response = $this->getJson('/api/messages/non-existent-id');

    $response->assertStatus(404);
});

it('prevents user from accessing other users messages', function () {
    Sanctum::actingAs($this->user);
    $otherMessage = Message::factory()->to($this->otherUser)->create();

    $response = $this->getJson("/api/messages/{$otherMessage->id}");

    $response->assertStatus(404);
});

it('marks message as read', function () {
    Sanctum::actingAs($this->user);
    $message = Message::factory()->to($this->user)->unread()->create();

    $response = $this->patchJson("/api/messages/{$message->id}/read");

    $response->assertStatus(200)
        ->assertJsonPath('data.message.is_read', true)
        ->assertJsonPath('message', 'Message marked as read');

    expect($message->fresh()->is_read)->toBeTrue()
        ->and($message->fresh()->read_at)->not->toBeNull();
});

it('marks message as unread', function () {
    Sanctum::actingAs($this->user);
    $message = Message::factory()->to($this->user)->read()->create();

    $response = $this->patchJson("/api/messages/{$message->id}/unread");

    $response->assertStatus(200)
        ->assertJsonPath('data.message.is_read', false)
        ->assertJsonPath('message', 'Message marked as unread');

    expect($message->fresh()->is_read)->toBeFalse()
        ->and($message->fresh()->read_at)->toBeNull();
});

it('returns read_at as ISO8601 string when marking as read', function () {
    Sanctum::actingAs($this->user);
    $message = Message::factory()->to($this->user)->unread()->create();

    $response = $this->patchJson("/api/messages/{$message->id}/read");

    $response->assertStatus(200)
        ->assertJsonPath('data.message.is_read', true);

    $readAt = $response->json('data.message.read_at');
    expect($readAt)->not->toBeNull()
        ->and($readAt)->toBeString()
        ->and(preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[\+\-]\d{2}:\d{2}$/', $readAt))->toBe(1);
});

it('returns read_at as null when marking as unread', function () {
    Sanctum::actingAs($this->user);
    $message = Message::factory()->to($this->user)->read()->create();

    $response = $this->patchJson("/api/messages/{$message->id}/unread");

    $response->assertStatus(200)
        ->assertJsonPath('data.message.is_read', false)
        ->assertJsonPath('data.message.read_at', null);
});

it('prevents user from marking other users messages as read', function () {
    Sanctum::actingAs($this->user);
    $otherMessage = Message::factory()->to($this->otherUser)->create();

    $response = $this->patchJson("/api/messages/{$otherMessage->id}/read");

    $response->assertStatus(404);
});

it('deletes message', function () {
    Sanctum::actingAs($this->user);
    $message = Message::factory()->to($this->user)->create();

    $response = $this->deleteJson("/api/messages/{$message->id}");

    $response->assertStatus(200)
        ->assertJsonPath('message', 'Message deleted successfully');

    $this->assertDatabaseMissing('messages', ['id' => $message->id]);
});

it('prevents user from deleting other users messages', function () {
    Sanctum::actingAs($this->user);
    $otherMessage = Message::factory()->to($this->otherUser)->create();

    $response = $this->deleteJson("/api/messages/{$otherMessage->id}");

    $response->assertStatus(404);

    $this->assertDatabaseHas('messages', ['id' => $otherMessage->id]);
});

it('supports pagination', function () {
    Sanctum::actingAs($this->user);
    Message::factory()->to($this->user)->count(25)->create();

    $response = $this->getJson('/api/messages?page=2');

    $response->assertStatus(200)
        ->assertJsonPath('data.pagination.current_page', 2)
        ->assertJsonPath('data.pagination.last_page', 2)
        ->assertJsonCount(5, 'data.messages');
});

it('orders messages by created_at descending', function () {
    Sanctum::actingAs($this->user);
    $oldMessage = Message::factory()->to($this->user)->create(['created_at' => now()->subDay()]);
    $newMessage = Message::factory()->to($this->user)->create(['created_at' => now()]);

    $response = $this->getJson('/api/messages');

    $messages = $response->json('data.messages');
    expect($messages[0]['id'])->toBe($newMessage->id)
        ->and($messages[1]['id'])->toBe($oldMessage->id);
});

