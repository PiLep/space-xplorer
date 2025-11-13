<?php

namespace App\Listeners;

use App\Events\PlanetExplored;
use App\Services\MessageService;
use Illuminate\Support\Facades\Log;

class SendPlanetDiscoveryMessage
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
     * Generates a discovery message when a user explores a planet.
     * If message generation fails, logs the error but does not block the event.
     */
    public function handle(PlanetExplored $event): void
    {
        try {
            $this->messageService->createDiscoveryMessage(
                $event->user,
                $event->planet
            );

            Log::info('Planet discovery message sent to user', [
                'user_id' => $event->user->id,
                'planet_id' => $event->planet->id,
            ]);
        } catch (\Exception $e) {
            // Log the error but don't block the planet exploration event
            Log::error('Failed to send planet discovery message to user', [
                'user_id' => $event->user->id,
                'planet_id' => $event->planet->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
