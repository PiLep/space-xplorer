<?php

namespace App\Listeners;

use App\Events\PlanetCreated;
use App\Models\User;
use App\Services\CodexService;
use Illuminate\Support\Facades\Log;

class CreateCodexEntryOnPlanetCreated
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
     * Creates a codex entry for a planet when it's created.
     * For home planets, assigns the user who owns the planet as the discoverer.
     * If codex entry creation fails, logs the error but does not block the event.
     */
    public function handle(PlanetCreated $event): void
    {
        try {
            $planet = $event->planet->fresh(['users']);

            // Check if this is a home planet (has users with this as home_planet_id)
            // If so, assign the first user as the discoverer
            $discoverer = null;
            if ($planet->users->isNotEmpty()) {
                $discoverer = $planet->users->first();
            }

            // Create codex entry
            $entry = $this->codexService->createEntryForPlanet($planet, $discoverer);

            Log::info('Codex entry created for planet on PlanetCreated event', [
                'planet_id' => $planet->id,
                'codex_entry_id' => $entry->id,
                'discoverer_id' => $discoverer?->id,
            ]);
        } catch (\Exception $e) {
            // Log the error but don't block the planet creation event
            Log::error('Failed to create codex entry for planet on PlanetCreated event', [
                'planet_id' => $event->planet->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

