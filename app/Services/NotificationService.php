<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    /**
     * Create a new notification for a user.
     *
     * @param  User  $user  The user to create the notification for
     * @param  string  $type  The type of notification (must be in config('notifications.types'))
     * @param  string  $title  The title of the notification
     * @param  string  $message  The message of the notification (max 200 characters)
     * @param  array  $data  Additional data for the notification (JSON)
     * @return Notification The created notification
     *
     * @throws \InvalidArgumentException If the notification type is not valid
     */
    public function create(User $user, string $type, string $title, string $message, array $data = []): Notification
    {
        // Validate notification type
        $allowedTypes = array_keys(config('notifications.types', []));
        if (! in_array($type, $allowedTypes)) {
            throw new \InvalidArgumentException("Invalid notification type: {$type}. Allowed types: ".implode(', ', $allowedTypes));
        }

        // Validate message length
        $maxLength = config('notifications.max_message_length', 200);
        if (strlen($message) > $maxLength) {
            throw new \InvalidArgumentException("Notification message exceeds maximum length of {$maxLength} characters.");
        }

        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    /**
     * Mark a notification as read.
     *
     * @param  Notification  $notification  The notification to mark as read
     * @return bool True if the notification was marked as read
     */
    public function markAsRead(Notification $notification): bool
    {
        return $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param  User  $user  The user whose notifications should be marked as read
     * @return int The number of notifications marked as read
     */
    public function markAllAsRead(User $user): int
    {
        return Notification::forUser($user)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get the count of unread notifications for a user.
     *
     * @param  User  $user  The user to count unread notifications for
     * @return int The count of unread notifications
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::forUser($user)
            ->unread()
            ->count();
    }

    /**
     * Get notifications for a user (all notifications, ordered by created_at DESC).
     *
     * @param  User  $user  The user to get notifications for
     * @param  int  $limit  The maximum number of notifications to return
     * @return Collection The notifications
     */
    public function getNotificationsForUser(User $user, int $limit = 20): Collection
    {
        return Notification::forUser($user)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread notifications for a user (ordered by created_at DESC).
     *
     * @param  User  $user  The user to get unread notifications for
     * @param  int  $limit  The maximum number of notifications to return
     * @return Collection The unread notifications
     */
    public function getUnreadNotificationsForUser(User $user, int $limit = 10): Collection
    {
        return Notification::forUser($user)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
