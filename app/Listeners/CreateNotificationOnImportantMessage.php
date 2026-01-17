<?php

namespace App\Listeners;

use App\Events\MessageReceived;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class CreateNotificationOnImportantMessage
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private NotificationService $notificationService
    ) {
        //
    }

    /**
     * Handle the event.
     *
     * Creates a notification when an important message is received.
     * The notification is a short alert that points to the inbox, not a duplication of the message content.
     */
    public function handle(MessageReceived $event): void
    {
        try {
            // Only create notification for important messages
            if (! $event->message->is_important) {
                return;
            }

            // Create a short summary notification (max 200 characters)
            // The notification points to the inbox, not duplicating the message content
            $title = 'Nouveau message important';
            $message = 'Nouveau message : '.$event->message->subject;

            // Truncate message if too long (max 200 characters)
            $maxLength = config('notifications.max_message_length', 200);
            if (strlen($message) > $maxLength) {
                $message = substr($message, 0, $maxLength - 3).'...';
            }

            // Create notification with link to inbox
            $this->notificationService->create(
                user: $event->recipient,
                type: 'message_important',
                title: $title,
                message: $message,
                data: [
                    'message_id' => $event->message->id,
                    'inbox_url' => '/inbox?message='.$event->message->id,
                ]
            );
        } catch (\Exception $e) {
            // Log error but don't block the event
            // This ensures that message creation is not affected if notification creation fails
            Log::error('Failed to create notification for important message', [
                'message_id' => $event->message->id,
                'recipient_id' => $event->recipient->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
