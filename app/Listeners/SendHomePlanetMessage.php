<?php

namespace App\Listeners;

use App\Events\PlanetCreated;
use App\Services\MessageService;
use Illuminate\Support\Facades\Log;

class SendHomePlanetMessage
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
     * Generates a message presenting the home planet when it's created.
     * Only sends the message if the planet is a home planet (has users with home_planet_id).
     * If message generation fails, logs the error but does not block the event.
     */
    public function handle(PlanetCreated $event): void
    {
        try {
            $planet = $event->planet->fresh(['users']);

            // Check if this is a home planet (has users with this as home_planet_id)
            if ($planet->users->isEmpty()) {
                return; // Not a home planet, skip
            }

            // Send message to all users who have this as their home planet
            foreach ($planet->users as $user) {
                $this->messageService->createDiscoveryMessage(
                    $user,
                    $planet,
                    'Votre planète d\'origine',
                    null // Use default template
                );
            }

            Log::info('Home planet message sent to users', [
                'planet_id' => $planet->id,
                'user_count' => $planet->users->count(),
            ]);
        } catch (\Exception $e) {
            // Log the error but don't block the planet creation event
            Log::error('Failed to send home planet message to users', [
                'planet_id' => $event->planet->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
