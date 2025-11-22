<?php

namespace App\Listeners;

use App\Events\PlanetCreated;
use App\Services\WikiService;

/**
 * CreateWikiEntryOnPlanetCreated is an alias for CreateCodexEntryOnPlanetCreated.
 * This class exists for backward compatibility with tests that use "Wiki" terminology.
 *
 * @deprecated Use CreateCodexEntryOnPlanetCreated instead. This class will be removed in a future version.
 */
class CreateWikiEntryOnPlanetCreated
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private WikiService $wikiService
    ) {
        //
    }

    /**
     * Handle the event.
     *
     * Creates a wiki entry for a planet when it's created.
     * For home planets, assigns the user who owns the planet as the discoverer.
     * If wiki entry creation fails, logs the error but does not block the event.
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

            // Create wiki entry
            $entry = $this->wikiService->createEntryForPlanet($planet, $discoverer);

            \Illuminate\Support\Facades\Log::info('Wiki entry created for planet on PlanetCreated event', [
                'planet_id' => $planet->id,
                'wiki_entry_id' => $entry->id,
                'discoverer_id' => $discoverer?->id,
            ]);
        } catch (\Exception $e) {
            // Log the error but don't block the planet creation event
            \Illuminate\Support\Facades\Log::error('Failed to create wiki entry for planet on PlanetCreated event', [
                'planet_id' => $event->planet->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

