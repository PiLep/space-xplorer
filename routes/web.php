<?php

use App\Livewire\Dashboard;
use App\Livewire\LoginTerminal;
use App\Livewire\Profile;
use App\Livewire\Register;
use Illuminate\Support\Facades\Route;

// Home page
Route::get('/', function () {
    return view('home');
})->name('home');

// Design System pages
Route::prefix('design-system')->name('design-system.')->group(function () {
    Route::get('/', function () {
        return view('design-system.overview');
    })->name('index');

    Route::get('/overview', function () {
        return view('design-system.overview');
    })->name('overview');

    Route::get('/colors', function () {
        return view('design-system.colors');
    })->name('colors');

    Route::get('/typography', function () {
        return view('design-system.typography');
    })->name('typography');

    Route::get('/spacing', function () {
        return view('design-system.spacing');
    })->name('spacing');

    Route::get('/components', function () {
        return view('design-system.components-index');
    })->name('components');

    Route::prefix('components')->name('components.')->group(function () {
        Route::get('/base', function () {
            return view('design-system.components-base');
        })->name('base');

        Route::get('/terminal', function () {
            return view('design-system.components-terminal');
        })->name('terminal');

        Route::get('/specialized', function () {
            return view('design-system.components-specialized');
        })->name('specialized');

        Route::get('/utilities', function () {
            return view('design-system.components-utilities');
        })->name('utilities');
    });

    Route::get('/effects', function () {
        return view('design-system.effects');
    })->name('effects');
});

// Legacy route for backward compatibility
Route::get('/design-system', function () {
    return redirect()->route('design-system.overview');
})->name('design-system');

// Public routes
Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', LoginTerminal::class)->name('login');
});

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', Profile::class)->name('profile');

    // Logout route (web)
    Route::post('/logout', function () {
        // Call API logout endpoint
        $token = session('sanctum_token');

        if ($token) {
            try {
                \Illuminate\Support\Facades\Http::withToken($token)
                    ->post(config('app.url') . '/api/auth/logout');
            } catch (\Exception $e) {
                // Log error but continue with logout
                \Illuminate\Support\Facades\Log::error('Logout API call failed: ' . $e->getMessage());
            }
        }

        // Clear session
        session()->forget('sanctum_token');
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');
});
