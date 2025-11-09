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

// Design System page
Route::get('/design-system', function () {
    return view('design-system');
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
                    ->post(config('app.url').'/api/auth/logout');
            } catch (\Exception $e) {
                // Log error but continue with logout
                \Illuminate\Support\Facades\Log::error('Logout API call failed: '.$e->getMessage());
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
