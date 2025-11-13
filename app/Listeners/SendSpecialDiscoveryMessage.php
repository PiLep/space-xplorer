<?php

namespace App\Listeners;

use App\Events\DiscoveryMade;
use App\Services\MessageService;
use Illuminate\Support\Facades\Log;

class SendSpecialDiscoveryMessage
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
     * Generates a discovery message when a user makes a special discovery.
     * If message generation fails, logs the error but does not block the event.
     */
    public function handle(DiscoveryMade $event): void
    {
        try {
            $discoveryData = array_merge([
                'type' => $event->discoveryType,
            ], $event->discoveryData);

            $this->messageService->createDiscoveryMessage(
                $event->user,
                $discoveryData
            );

            Log::info('Special discovery message sent to user', [
                'user_id' => $event->user->id,
                'discovery_type' => $event->discoveryType,
            ]);
        } catch (\Exception $e) {
            // Log the error but don't block the discovery event
            Log::error('Failed to send special discovery message to user', [
                'user_id' => $event->user->id,
                'discovery_type' => $event->discoveryType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
