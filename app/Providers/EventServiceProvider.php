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
use App\Listeners\CreateCodexEntryOnPlanetCreated;
use App\Listeners\CreateCodexEntryOnPlanetExplored;
use App\Listeners\GenerateAvatar;
use App\Listeners\GenerateHomePlanet;
use App\Listeners\GeneratePlanetImage;
use App\Listeners\GeneratePlanetVideo;
use App\Listeners\SendHomePlanetMessage;
use App\Listeners\SendPlanetDiscoveryMessage;
use App\Listeners\SendSpecialDiscoveryMessage;
use App\Listeners\SendWelcomeMessage;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

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
            SendWelcomeMessage::class,
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
            SendHomePlanetMessage::class,
            CreateCodexEntryOnPlanetCreated::class,
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

        // Exploration events
        PlanetExplored::class => [
            SendPlanetDiscoveryMessage::class,
            CreateCodexEntryOnPlanetExplored::class,
            // Future listeners: TrackExploration, AwardExplorationPoints, etc.
        ],
        DiscoveryMade::class => [
            SendSpecialDiscoveryMessage::class,
            // Future listeners: TrackDiscovery, AwardDiscoveryAchievement, etc.
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Register LogEvent listener for all application events BEFORE parent::boot()
        // We add it to each event in $listen array
        $allEvents = [
            \App\Events\AvatarChanged::class,
            \App\Events\AvatarGenerated::class,
            \App\Events\DashboardAccessed::class,
            \App\Events\DiscoveryMade::class,
            \App\Events\EmailChanged::class,
            \App\Events\EmailVerified::class,
            \App\Events\FailedLoginAttempt::class,
            \App\Events\FirstLogin::class,
            \App\Events\InboxAccessed::class,
            \App\Events\MessageDeleted::class,
            \App\Events\MessagePermanentlyDeleted::class,
            \App\Events\MessageRead::class,
            \App\Events\MessageReceived::class,
            \App\Events\MessageRestored::class,
            \App\Events\PasswordChanged::class,
            \App\Events\PasswordResetCompleted::class,
            \App\Events\PasswordResetRequested::class,
            \App\Events\PlanetCreated::class,
            \App\Events\PlanetExplored::class,
            \App\Events\PlanetImageGenerated::class,
            \App\Events\PlanetVideoGenerated::class,
            \App\Events\ProfileAccessed::class,
            \App\Events\ResourceApproved::class,
            \App\Events\ResourceGenerated::class,
            \App\Events\ResourceRejected::class,
            \App\Events\SessionExpired::class,
            \App\Events\UserDeleted::class,
            \App\Events\UserDeleting::class,
            \App\Events\UserLoggedIn::class,
            \App\Events\UserLoggedOut::class,
            \App\Events\UserProfileUpdated::class,
            \App\Events\UserRegistered::class,
        ];

        $logEventClass = \App\Listeners\LogEvent::class;
        foreach ($allEvents as $eventClass) {
            if (! isset($this->listen[$eventClass])) {
                $this->listen[$eventClass] = [];
            }
            // Add LogEvent if not already present
            if (! in_array($logEventClass, $this->listen[$eventClass])) {
                $this->listen[$eventClass][] = $logEventClass;
            }
        }

        parent::boot();

        Log::info('EventServiceProvider boot completed - LogEvent registered for all events');
    }
}
