<?php

namespace App\Providers;

use App\Events\PlanetCreated;
use App\Events\UserRegistered;
use App\Listeners\GenerateAvatar;
use App\Listeners\GenerateHomePlanet;
use App\Listeners\GeneratePlanetImage;
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
        UserRegistered::class => [
            GenerateHomePlanet::class,
            GenerateAvatar::class,
        ],
        PlanetCreated::class => [
            GeneratePlanetImage::class,
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
