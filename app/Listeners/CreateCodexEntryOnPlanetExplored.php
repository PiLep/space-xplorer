<?php

namespace App\Listeners;

use App\Events\PlanetExplored;
use App\Services\CodexService;
use Illuminate\Support\Facades\Log;

class CreateCodexEntryOnPlanetExplored
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private CodexService $codexService
    ) {
        //
    }

    /**
     * Handle the event.
     *
     * Creates a codex entry for a planet when it's explored (if one doesn't exist).
     * Assigns the exploring user as the discoverer.
     * If codex entry creation fails, logs the error but does not block the event.
     */
    public function handle(PlanetExplored $event): void
    {
        try {
            // Create codex entry (service will check if it already exists)
            $entry = $this->codexService->createEntryForPlanet($event->planet, $event->user);

            Log::info('Codex entry created/verified for planet on PlanetExplored event', [
                'planet_id' => $event->planet->id,
                'codex_entry_id' => $entry->id,
                'discoverer_id' => $event->user->id,
            ]);
        } catch (\Exception $e) {
            // Log the error but don't block the planet exploration event
            Log::error('Failed to create codex entry for planet on PlanetExplored event', [
                'planet_id' => $event->planet->id,
                'user_id' => $event->user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

