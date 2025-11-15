<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ResourceController;
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
    Route::resource('resources', ResourceController::class)->except(['edit', 'update', 'destroy']);
    Route::get('/resources/review', [ResourceController::class, 'review'])->name('resources.review');
    Route::post('/resources/{resource}/approve', [ResourceController::class, 'approve'])->name('resources.approve');

    // Minigame test
    Route::get('/minigame/test', fn () => view('admin.minigame-test'))->name('minigame.test');
});

// Fallback for admin routes - redirect to admin login if route not found
Route::prefix('admin')->group(function () {
    Route::fallback(fn () => redirect()->route('admin.login'));
});
