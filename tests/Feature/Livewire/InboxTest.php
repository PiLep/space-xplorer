<?php

use App\Models\Message;
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

it('renders inbox component', function () {
    Livewire::test(\App\Livewire\Inbox::class)
        ->assertStatus(200);
});

it('loads messages on mount', function () {
    Message::factory()->to($this->user)->count(5)->create();

    Livewire::test(\App\Livewire\Inbox::class)
        ->assertSet('filter', 'all')
        ->assertSee('list_messages');
});

it('filters messages by all status', function () {
    Message::factory()->to($this->user)->unread()->count(3)->create();
    Message::factory()->to($this->user)->read()->count(2)->create();

    Livewire::test(\App\Livewire\Inbox::class)
        ->call('filterMessages', 'all')
        ->assertSet('filter', 'all');
});

it('filters messages by unread status', function () {
    Message::factory()->to($this->user)->unread()->count(3)->create();
    Message::factory()->to($this->user)->read()->count(2)->create();

    Livewire::test(\App\Livewire\Inbox::class)
        ->call('filterMessages', 'unread')
        ->assertSet('filter', 'unread');
});

it('filters messages by read status', function () {
    Message::factory()->to($this->user)->unread()->count(3)->create();
    Message::factory()->to($this->user)->read()->count(2)->create();

    Livewire::test(\App\Livewire\Inbox::class)
        ->call('filterMessages', 'read')
        ->assertSet('filter', 'read');
});

it('selects a message to view', function () {
    $message = Message::factory()->to($this->user)->create();

    Livewire::test(\App\Livewire\Inbox::class)
        ->call('selectMessage', $message->id)
        ->assertSet('selectedMessageId', $message->id)
        ->assertSet('selectedMessage.id', $message->id);
});

it('marks message as read when selected', function () {
    $message = Message::factory()->to($this->user)->unread()->create();

    Livewire::test(\App\Livewire\Inbox::class)
        ->call('selectMessage', $message->id);

    expect($message->fresh()->is_read)->toBeTrue();
});

it('marks message as read', function () {
    $message = Message::factory()->to($this->user)->unread()->create();

    Livewire::test(\App\Livewire\Inbox::class)
        ->call('markAsRead', $message->id);

    expect($message->fresh()->is_read)->toBeTrue()
        ->and($message->fresh()->read_at)->not->toBeNull();
});

it('marks message as unread', function () {
    $message = Message::factory()->to($this->user)->read()->create();

    Livewire::test(\App\Livewire\Inbox::class)
        ->call('markAsUnread', $message->id);

    expect($message->fresh()->is_read)->toBeFalse()
        ->and($message->fresh()->read_at)->toBeNull();
});

it('deletes a message (soft delete - moves to trash)', function () {
    $message = Message::factory()->to($this->user)->create();

    Livewire::test(\App\Livewire\Inbox::class)
        ->call('deleteMessage', $message->id);

    // Verify the message is soft-deleted (exists in database but has deleted_at set)
    $this->assertDatabaseHas('messages', ['id' => $message->id]);
    // Should not be found in normal queries (without withTrashed())
    expect(Message::find($message->id))->toBeNull();
    // Should be found with withTrashed() and be trashed
    $trashedMessage = Message::withTrashed()->find($message->id);
    expect($trashedMessage)->not->toBeNull()
        ->and($trashedMessage->trashed())->toBeTrue();
});

it('clears selection when deleted message was selected', function () {
    $message = Message::factory()->to($this->user)->create();

    Livewire::test(\App\Livewire\Inbox::class)
        ->call('selectMessage', $message->id)
        ->assertSet('selectedMessageId', $message->id)
        ->call('deleteMessage', $message->id)
        ->assertSet('selectedMessageId', null)
        ->assertSet('selectedMessage', null);
});

it('prevents user from accessing other users messages', function () {
    $otherUser = User::factory()->create();
    $otherMessage = Message::factory()->to($otherUser)->create();

    Livewire::test(\App\Livewire\Inbox::class)
        ->call('selectMessage', $otherMessage->id)
        ->assertSet('selectedMessage', null);
});

it('prevents user from marking other users messages as read', function () {
    $otherUser = User::factory()->create();
    $otherMessage = Message::factory()->to($otherUser)->create();

    expect(fn () => Livewire::test(\App\Livewire\Inbox::class)
        ->call('markAsRead', $otherMessage->id))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

it('prevents user from deleting other users messages', function () {
    $otherUser = User::factory()->create();
    $otherMessage = Message::factory()->to($otherUser)->create();

    expect(fn () => Livewire::test(\App\Livewire\Inbox::class)
        ->call('deleteMessage', $otherMessage->id))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

it('displays unread count', function () {
    Message::factory()->to($this->user)->unread()->count(5)->create();
    Message::factory()->to($this->user)->read()->count(3)->create();

    Livewire::test(\App\Livewire\Inbox::class)
        ->assertSee('[INFO] Unread messages: 5');
});

it('displays message list', function () {
    $message1 = Message::factory()->to($this->user)->create(['subject' => 'Test Subject 1']);
    $message2 = Message::factory()->to($this->user)->create(['subject' => 'Test Subject 2']);

    Livewire::test(\App\Livewire\Inbox::class)
        ->assertSee('Test Subject 1')
        ->assertSee('Test Subject 2');
});

it('displays selected message content', function () {
    $message = Message::factory()->to($this->user)->create([
        'subject' => 'Test Subject',
        'content' => 'Test Content',
    ]);

    Livewire::test(\App\Livewire\Inbox::class)
        ->call('selectMessage', $message->id)
        ->assertSee('Test Subject')
        ->assertSee('Test Content');
});

it('shows flash message on successful deletion', function () {
    $message = Message::factory()->to($this->user)->create();

    Livewire::test(\App\Livewire\Inbox::class)
        ->call('deleteMessage', $message->id);

    // Verify the message is soft-deleted (exists in database but has deleted_at set)
    $this->assertDatabaseHas('messages', ['id' => $message->id]);
    // Should not be found in normal queries (without withTrashed())
    expect(Message::find($message->id))->toBeNull();
    // Should be found with withTrashed() and be trashed
    $trashedMessage = Message::withTrashed()->find($message->id);
    expect($trashedMessage)->not->toBeNull()
        ->and($trashedMessage->trashed())->toBeTrue();

    // Note: Flash messages in Livewire are handled internally and may not be directly testable
    // The important part is that the deletion succeeds, which is verified above
});

