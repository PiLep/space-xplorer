<?php

namespace App\Livewire;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Notifications extends Component
{
    public string $filter = 'all'; // all, unread, read

    public string $typeFilter = 'all'; // all, message_important, ship_assigned, resource_added

    public int $refreshKey = 0; // Used to force computed property recalculation

    /**
     * Initialize the component with filter values from the request.
     */
    public function mount(): void
    {
        // Initialize filters from URL query parameters with validation
        $filter = request()->query('filter', 'all');
        $typeFilter = request()->query('type', 'all');

        // Validate filter values
        $validFilters = ['all', 'unread', 'read'];
        $this->filter = in_array($filter, $validFilters) ? $filter : 'all';

        $validTypeFilters = ['all', 'message_important', 'ship_assigned', 'resource_added'];
        $this->typeFilter = in_array($typeFilter, $validTypeFilters) ? $typeFilter : 'all';
    }

    /**
     * Get the notification service instance.
     */
    private function notificationService(): NotificationService
    {
        return app(NotificationService::class);
    }

    /**
     * Get paginated notifications for the authenticated user.
     */
    #[Computed]
    public function notifications(): LengthAwarePaginator
    {
        // Use refreshKey to force recalculation when filters change
        $key = $this->refreshKey;

        if (! auth()->check()) {
            return new LengthAwarePaginator(collect(), 0, 10);
        }

        $query = Notification::forUser(auth()->user());

        // Apply filters
        if ($this->filter === 'unread') {
            $query->unread();
        } elseif ($this->filter === 'read') {
            $query->read();
        }

        if ($this->typeFilter !== 'all') {
            $query->byType($this->typeFilter);
        }

        // Get current page from request, default to 1
        $page = (int) request()->get('page', 1);
        $perPage = 10;

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get the unread count for the current filter.
     */
    #[Computed]
    public function unreadCount(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        $query = Notification::forUser(auth()->user())->unread();

        if ($this->typeFilter !== 'all') {
            $query->byType($this->typeFilter);
        }

        return $query->count();
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $notificationId): void
    {
        if (! auth()->check()) {
            return;
        }

        $notification = Notification::forUser(auth()->user())
            ->find($notificationId);

        if ($notification) {
            $this->notificationService()->markAsRead($notification);
            $this->dispatch('notification-read');
            // Clear computed cache to reload notifications
            unset($this->notifications);
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        if (! auth()->check()) {
            return;
        }

        $this->notificationService()->markAllAsRead(auth()->user());
        $this->dispatch('notifications-all-read');
        // Clear computed cache to reload notifications
        unset($this->notifications);
        unset($this->unreadCount);
        unset($this->readCount);
    }

    /**
     * Delete a notification.
     */
    public function delete(string $notificationId): void
    {
        if (! auth()->check()) {
            return;
        }

        $notification = Notification::forUser(auth()->user())
            ->find($notificationId);

        if ($notification) {
            $notification->delete();
            $this->dispatch('notification-deleted');
            // Clear computed cache to reload notifications
            unset($this->notifications);
            unset($this->unreadCount);
            // Increment refreshKey to force recalculation
            $this->refreshKey++;
        }
    }

    /**
     * Delete all read notifications.
     */
    public function deleteAllRead(): void
    {
        if (! auth()->check()) {
            return;
        }

        $notifications = Notification::forUser(auth()->user())
            ->read()
            ->get();

        $deletedCount = $notifications->count();

        foreach ($notifications as $notification) {
            $notification->delete();
        }

        $this->dispatch('notifications-deleted', count: $deletedCount);
        // Clear computed cache to reload notifications
        unset($this->notifications);
        unset($this->unreadCount);
        unset($this->readCount);
        // Increment refreshKey to force recalculation
        $this->refreshKey++;
    }

    /**
     * Get the count of read notifications.
     */
    #[Computed]
    public function readCount(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        $query = Notification::forUser(auth()->user())->read();

        if ($this->typeFilter !== 'all') {
            $query->byType($this->typeFilter);
        }

        return $query->count();
    }

    /**
     * Change the filter.
     */
    public function updateFilter(string $filter): void
    {
        $this->filter = $filter;
        // Increment refreshKey to force computed property recalculation
        $this->refreshKey++;
        // Clear computed cache
        unset($this->notifications);
        unset($this->unreadCount);
        unset($this->readCount);
    }

    /**
     * Change the type filter.
     */
    public function updateTypeFilter(string $type): void
    {
        $this->typeFilter = $type;
        // Increment refreshKey to force computed property recalculation
        $this->refreshKey++;
        // Clear computed cache
        unset($this->notifications);
        unset($this->unreadCount);
        unset($this->readCount);
    }

    /**
     * Get the redirect URL for a notification based on its type.
     *
     * Note: ship_assigned and resource_added types are prepared for future features.
     * Until those features are implemented, they redirect to /notifications.
     */
    public function getNotificationUrl(Notification $notification): string
    {
        $data = $notification->data ?? [];

        return match ($notification->type) {
            'message_important' => $data['inbox_url'] ?? '/inbox',
            // Future features - redirect to notifications until implemented
            // 'ship_assigned' => $data['ship_url'] ?? '/ships',
            // 'resource_added' => $data['inventory_url'] ?? '/inventory',
            default => '/notifications',
        };
    }

    public function render(): View
    {
        return view('livewire.notifications');
    }
}
