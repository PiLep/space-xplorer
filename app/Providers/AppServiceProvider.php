<?php

namespace App\Providers;

use App\Models\CodexEntry;
use App\Models\Resource;
use App\Models\User;
use App\Observers\CodexEntryObserver;
use App\Observers\ResourceObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\View;
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
        CodexEntry::observe(CodexEntryObserver::class);
        Resource::observe(ResourceObserver::class);
        User::observe(UserObserver::class);

        // Share unread messages count with the app layout to avoid N+1 queries
        // This ensures the count is calculated once per request and cached
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $view->with('unreadMessagesCount', auth()->user()->unreadMessagesCount());
            } else {
                $view->with('unreadMessagesCount', 0);
            }
        });
    }
}
