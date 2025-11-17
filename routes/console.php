<?php

use App\Console\Commands\GenerateDailyAvatarResources;
use App\Console\Commands\GenerateDailyPlanetResources;
use App\Models\ScheduledTask;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily planet resources generation
// Runs every day at 2:00 AM (only if enabled in database)
Schedule::command(GenerateDailyPlanetResources::class)
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->when(function () {
        $task = ScheduledTask::findByName('daily_planet_resources');

        return $task && $task->is_enabled;
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Daily planet resources generation failed');
        $task = ScheduledTask::findByName('daily_planet_resources');
        if ($task) {
            $task->markAsRun();
        }
    })
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Daily planet resources generation completed successfully');
        $task = ScheduledTask::findByName('daily_planet_resources');
        if ($task) {
            $task->markAsRun();
        }
    })
    ->thenPing(env('FORGE_HEARTBEAT_PLANET_RESOURCES'));

// Schedule daily avatar resources generation
// Runs every day at 2:30 AM (30 minutes after planets, only if enabled in database)
Schedule::command(GenerateDailyAvatarResources::class)
    ->dailyAt('02:30')
    ->withoutOverlapping()
    ->when(function () {
        $task = ScheduledTask::findByName('daily_avatar_resources');

        return $task && $task->is_enabled;
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('Daily avatar resources generation failed');
        $task = ScheduledTask::findByName('daily_avatar_resources');
        if ($task) {
            $task->markAsRun();
        }
    })
    ->onSuccess(function () {
        \Illuminate\Support\Facades\Log::info('Daily avatar resources generation completed successfully');
        $task = ScheduledTask::findByName('daily_avatar_resources');
        if ($task) {
            $task->markAsRun();
        }
    })
    ->thenPing(env('FORGE_HEARTBEAT_AVATAR_RESOURCES'));
