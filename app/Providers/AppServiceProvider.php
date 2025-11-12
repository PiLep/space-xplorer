<?php

namespace App\Providers;

use App\Models\Resource;
use App\Models\User;
use App\Observers\ResourceObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Resource::observe(ResourceObserver::class);
        User::observe(UserObserver::class);
    }
}
