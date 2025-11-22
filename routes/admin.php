<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\Admin\ScheduledTaskController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Public admin routes (login)
Route::middleware('guest:admin')->prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1'); // 5 login attempts per minute
});

// Access admin from web guard (for super admins)
Route::middleware('auth:web')->prefix('admin')->group(function () {
    Route::get('/access', [AuthController::class, 'accessFromWeb'])->name('admin.access');
});

// Protected admin routes
Route::middleware(['auth:admin', 'admin.auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Users management
    Route::resource('users', UserController::class)->only(['index', 'show']);

    // Resources management
    // Define specific routes BEFORE resource routes to avoid route conflicts
    Route::get('/resources/review', [ResourceController::class, 'review'])->name('resources.review');
    Route::post('/resources/{resource}/approve', [ResourceController::class, 'approve'])->name('resources.approve');
    Route::resource('resources', ResourceController::class)->except(['edit', 'update', 'destroy']);

    // Minigame test
    Route::get('/minigame/test', fn () => view('admin.minigame-test'))->name('minigame.test');

    // Map
    Route::get('/map', [MapController::class, 'index'])->name('map');
    Route::get('/systems', [MapController::class, 'list'])->name('systems.index');
    Route::get('/systems/{id}/map', [MapController::class, 'show'])->name('systems.map');

    // Scheduled Tasks management
    Route::get('/scheduled-tasks', [ScheduledTaskController::class, 'index'])->name('scheduled-tasks.index');
    Route::post('/scheduled-tasks/{scheduledTask}/toggle', [ScheduledTaskController::class, 'toggle'])->name('scheduled-tasks.toggle');
    Route::post('/scheduled-tasks/{scheduledTask}/enable', [ScheduledTaskController::class, 'enable'])->name('scheduled-tasks.enable');
    Route::post('/scheduled-tasks/{scheduledTask}/disable', [ScheduledTaskController::class, 'disable'])->name('scheduled-tasks.disable');
});

// Fallback for admin routes - redirect to admin login if route not found
Route::prefix('admin')->group(function () {
    Route::fallback(fn () => redirect()->route('admin.login'));
});
