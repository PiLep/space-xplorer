<?php

namespace App\Listeners;

use App\Listeners\CreateCodexEntryOnPlanetExplored as BaseListener;

/**
 * CreateWikiEntryOnPlanetExplored is an alias for CreateCodexEntryOnPlanetExplored.
 * This class exists for backward compatibility with tests that use "Wiki" terminology.
 *
 * @deprecated Use CreateCodexEntryOnPlanetExplored instead. This class will be removed in a future version.
 */
class CreateWikiEntryOnPlanetExplored extends BaseListener
{
    // All methods are inherited from CreateCodexEntryOnPlanetExplored
}

