<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\MessageService;
use Illuminate\Support\Facades\Log;

class SendWelcomeMessage
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private MessageService $messageService
    ) {
        //
    }

    /**
     * Handle the event.
     *
     * Generates a welcome message from Stellar when a user registers.
     * If message generation fails, logs the error but does not block the event.
     */
    public function handle(UserRegistered $event): void
    {
        try {
            $this->messageService->createWelcomeMessage($event->user);

            Log::info('Welcome message sent to user', [
                'user_id' => $event->user->id,
            ]);
        } catch (\Exception $e) {
            // Log the error but don't block the user registration event
            Log::error('Failed to send welcome message to user', [
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
