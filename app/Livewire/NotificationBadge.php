<?php

namespace App\Livewire;

use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NotificationBadge extends Component
{
    /**
     * Get the notification service instance.
     */
    private function notificationService(): NotificationService
    {
        return app(NotificationService::class);
    }

    /**
     * Get the unread notifications count for the authenticated user.
     */
    #[Computed]
    public function unreadCount(): int
    {
        if (! auth()->check()) {
            return 0;
        }

        return $this->notificationService()->getUnreadCount(auth()->user());
    }

    /**
     * Refresh notifications (for polling).
     */
    public function refresh(): void
    {
        // This method is called by Livewire polling
        // The computed properties will be recalculated automatically
    }

    public function render(): View
    {
        return view('livewire.notification-badge');
    }
}
