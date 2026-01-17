<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CodexController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PlanetController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::prefix('auth')->group(function () {
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

// Public Codex routes (with rate limiting)
Route::prefix('codex')->middleware('throttle:60,1')->group(function () {
    Route::get('/planets', [CodexController::class, 'index']);
    Route::get('/planets/{id}', [CodexController::class, 'show']);
    Route::get('/search', [CodexController::class, 'search']);
});

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::prefix('users/{id}')->group(function () {
        Route::get('/', [UserController::class, 'show']);
        Route::put('/', [UserController::class, 'update']);
        Route::put('/avatar', [UserController::class, 'updateAvatar']);
        Route::get('/home-planet', [UserController::class, 'getHomePlanet']);
    });

    // Planet routes
    Route::get('/planets/{id}', [PlanetController::class, 'show']);

    // Resource routes
    Route::get('/resources/avatars', [ResourceController::class, 'getAvatars']);

    // Message routes
    Route::prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'index']);
        Route::get('/{id}', [MessageController::class, 'show']);
        Route::patch('/{id}/read', [MessageController::class, 'markAsRead']);
        Route::patch('/{id}/unread', [MessageController::class, 'markAsUnread']);
        Route::delete('/{id}', [MessageController::class, 'destroy']);
    });

    // Protected Codex routes (with rate limiting)
    Route::prefix('codex/planets/{id}')->middleware('throttle:5,1')->group(function () {
        Route::post('/name', [CodexController::class, 'namePlanet']);
        Route::post('/contribute', [CodexController::class, 'contribute']);
    });
});
