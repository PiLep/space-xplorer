<?php

use App\Http\Controllers\Api\AuthController;
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
});
