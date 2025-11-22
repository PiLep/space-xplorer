<?php

namespace App\Services;

use App\Models\Planet;
use App\Models\User;
use App\Models\WikiEntry;

/**
 * WikiService is an alias for CodexService.
 * This class exists for backward compatibility with tests that use "Wiki" terminology.
 *
 * @deprecated Use CodexService instead. This class will be removed in a future version.
 */
class WikiService extends CodexService
{
    /**
     * Create a wiki entry for a planet with AI-generated description.
     * Override to return WikiEntry instead of CodexEntry.
     *
     * @param  Planet  $planet  The planet to create an entry for
     * @param  User|null  $discoverer  The user who discovered the planet (optional)
     * @return WikiEntry The created wiki entry
     */
    public function createEntryForPlanet(Planet $planet, ?User $discoverer = null): WikiEntry
    {
        $codexEntry = parent::createEntryForPlanet($planet, $discoverer);

        // Return as WikiEntry (they share the same table)
        return WikiEntry::find($codexEntry->id);
    }

    /**
     * Name a planet with user-provided name.
     * Override to return WikiEntry instead of CodexEntry.
     *
     * @param  WikiEntry  $entry  The wiki entry to update
     * @param  User  $user  The user naming the planet
     * @param  string  $name  The name to assign
     * @return WikiEntry The updated wiki entry
     */
    public function namePlanet($entry, User $user, string $name): WikiEntry
    {
        $codexEntry = parent::namePlanet($entry, $user, $name);

        // Return as WikiEntry
        return WikiEntry::find($codexEntry->id);
    }
}

