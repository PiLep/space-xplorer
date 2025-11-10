<?php

use App\Console\Commands\GenerateDailyPlanetResources;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily planet resources generation
// Runs every day at 2:00 AM
Schedule::command(GenerateDailyPlanetResources::class)
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Daily planet resources generation failed');
    })
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Daily planet resources generation completed successfully');
    });
