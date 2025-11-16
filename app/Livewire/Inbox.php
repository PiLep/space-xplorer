<?php

namespace App\Livewire;

use App\Events\InboxAccessed;
use App\Events\MessageDeleted;
use App\Events\MessagePermanentlyDeleted;
use App\Events\MessageRead;
use App\Events\MessageRestored;
use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Inbox extends Component
{
    public string $filter = 'all'; // 'all', 'unread', 'read', 'trash'

    public ?string $type = null; // Optional type filter

    public ?string $selectedMessageId = null;

    public ?Message $selectedMessage = null;

    /**
     * Get messages with pagination (computed property - cannot be serialized directly).
     */
    #[Computed]
    public function messages(): LengthAwarePaginator
    {
        return app(MessageService::class)->getMessagesForUser(
            Auth::user(),
            $this->filter,
            $this->type,
            20 // per page
        );
    }

    /**
     * Initialize the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        if ($user) {
            // Dispatch event to track inbox access
            event(new InboxAccessed($user));
        }

        // Component initialized - messages will be loaded via computed property
        // Select first message will be called after render via wire:init
    }

    /**
     * Hook called after component is rendered.
     */
    public function hydrate(): void
    {
        // Select first message if none selected
        if ($this->selectedMessageId === null) {
            $this->selectFirstMessage();
        }
    }

    /**
     * Select the first message if available.
     */
    public function selectFirstMessage(): void
    {
        if ($this->selectedMessageId === null) {
            $messages = $this->messages;
            if ($messages->count() > 0) {
                $firstMessage = $messages->first();
                $this->selectMessage($firstMessage->id);
            }
        }
    }

    /**
     * Filter messages by status.
     */
    public function filterMessages(string $filter): void
    {
        $this->filter = $filter;
        $this->selectedMessageId = null;
        $this->selectedMessage = null;
        // Clear computed cache to reload messages
        unset($this->messages);
        // hydrate() will automatically select the first message after this method completes
    }

    /**
     * Filter messages by type.
     */
    public function filterByType(?string $type): void
    {
        $this->type = $type;
        $this->selectedMessageId = null;
        $this->selectedMessage = null;
        // Clear computed cache to reload messages
        unset($this->messages);
        // hydrate() will automatically select the first message after this method completes
    }

    /**
     * Select a message to view.
     */
    public function selectMessage(string $id): void
    {
        $this->selectedMessageId = $id;

        // Use scope forUser() to ensure security
        // Include trashed messages if we're in trash filter
        $query = Message::forUser(Auth::user());
        if ($this->filter === 'trash') {
            $query->withTrashed();
        }
        $this->selectedMessage = $query->find($id);

        // Mark as read when selected (only if not trashed)
        if ($this->selectedMessage && ! $this->selectedMessage->trashed() && ! $this->selectedMessage->is_read) {
            $this->selectedMessage->markAsRead();
            // Clear computed cache to reload messages
            unset($this->messages);
        }
    }

    /**
     * Mark a message as read.
     *
     * Note: This method does not use withTrashed() because trashed (deleted) messages
     * should not be markable as read. If a trashed message ID is provided, findOrFail
     * will throw a ModelNotFoundException, which is the correct behavior.
     */
    public function markAsRead(string $id): void
    {
        $message = Message::forUser(Auth::user())->findOrFail($id);

        $message->markAsRead();

        // Dispatch event
        event(new MessageRead($message, Auth::user()));

        // Clear computed cache to reload messages
        unset($this->messages);

        if ($this->selectedMessageId === $id) {
            $this->selectedMessage = $message->fresh();
        }
    }

    /**
     * Mark a message as unread.
     *
     * Note: This method does not use withTrashed() because trashed (deleted) messages
     * should not be markable as unread. If a trashed message ID is provided, findOrFail
     * will throw a ModelNotFoundException, which is the correct behavior.
     */
    public function markAsUnread(string $id): void
    {
        $message = Message::forUser(Auth::user())->findOrFail($id);

        $message->markAsUnread();

        // Clear computed cache to reload messages
        unset($this->messages);

        if ($this->selectedMessageId === $id) {
            $this->selectedMessage = $message->fresh();
        }
    }

    /**
     * Delete a message (soft delete - moves to trash).
     */
    public function deleteMessage(string $id): void
    {
        $message = Message::forUser(Auth::user())->findOrFail($id);
        $message->delete(); // Soft delete - moves to trash

        // Dispatch event
        event(new MessageDeleted($message, Auth::user()));

        // Clear selection if deleted message was selected
        if ($this->selectedMessageId === $id) {
            $this->selectedMessageId = null;
            $this->selectedMessage = null;
        }

        // Clear computed cache to reload messages
        unset($this->messages);

        session()->flash('success', 'Message moved to trash');
    }

    /**
     * Restore a message from trash.
     */
    public function restoreMessage(string $id): void
    {
        $message = Message::forUser(Auth::user())->withTrashed()->findOrFail($id);
        $message->restore();

        // Dispatch event
        event(new MessageRestored($message, Auth::user()));

        // Clear computed cache to reload messages
        unset($this->messages);

        // Update selected message if it's the restored one
        if ($this->selectedMessageId === $id) {
            $this->selectedMessage = $message->fresh();
        }

        session()->flash('success', 'Message restored');
    }

    /**
     * Permanently delete a message from trash.
     */
    public function forceDeleteMessage(string $id): void
    {
        $message = Message::forUser(Auth::user())->withTrashed()->findOrFail($id);
        $message->forceDelete();

        // Dispatch event
        event(new MessagePermanentlyDeleted($message, Auth::user()));

        // Clear selection if deleted message was selected
        if ($this->selectedMessageId === $id) {
            $this->selectedMessageId = null;
            $this->selectedMessage = null;
        }

        // Clear computed cache to reload messages
        unset($this->messages);

        session()->flash('success', 'Message permanently deleted');
    }

    /**
     * Get the count of unread messages (computed property with cache).
     */
    #[Computed]
    public function unreadCount(): int
    {
        return Auth::user()->unreadMessagesCount();
    }

    public function render()
    {
        return view('livewire.inbox');
    }
}
