<?php

namespace App\Providers;

use App\Events\AvatarGenerated;
use App\Events\DiscoveryMade;
use App\Events\PlanetCreated;
use App\Events\PlanetExplored;
use App\Events\PlanetImageGenerated;
use App\Events\PlanetVideoGenerated;
use App\Events\UserDeleted;
use App\Events\UserLoggedIn;
use App\Events\UserProfileUpdated;
use App\Events\UserRegistered;
use App\Listeners\CleanupUserData;
use App\Listeners\GenerateAvatar;
use App\Listeners\GenerateHomePlanet;
use App\Listeners\GeneratePlanetImage;
use App\Listeners\GeneratePlanetVideo;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // User lifecycle events
        UserRegistered::class => [
            GenerateHomePlanet::class,
            GenerateAvatar::class,
        ],
        UserLoggedIn::class => [
            // Future listeners: TrackUserLogin, SendWelcomeNotification, etc.
        ],
        UserProfileUpdated::class => [
            // Future listeners: RegenerateAvatarIfNameChanged, TrackProfileUpdate, etc.
        ],
        UserDeleted::class => [
            CleanupUserData::class,
        ],

        // Planet lifecycle events
        PlanetCreated::class => [
            GeneratePlanetImage::class,
            GeneratePlanetVideo::class,
        ],
        PlanetImageGenerated::class => [
            // Future listeners: NotifyUserPlanetImageReady, TrackPlanetImageGeneration, etc.
        ],
        PlanetVideoGenerated::class => [
            // Future listeners: NotifyUserPlanetVideoReady, TrackPlanetVideoGeneration, etc.
        ],

        // Media generation completion events
        AvatarGenerated::class => [
            // Future listeners: NotifyUserAvatarReady, TrackAvatarGeneration, etc.
        ],

        // Exploration events (future features)
        PlanetExplored::class => [
            // Future listeners: TrackExploration, AwardExplorationPoints, etc.
        ],
        DiscoveryMade::class => [
            // Future listeners: TrackDiscovery, AwardDiscoveryAchievement, etc.
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

        // Disable automatic event discovery to prevent duplicate listener registration
        // Laravel was registering both the class and the @handle method
        $this->disableEventDiscovery();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
