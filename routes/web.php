<?php

use App\Livewire\Dashboard;
use App\Livewire\Inbox;
use App\Livewire\LoginTerminal;
use App\Livewire\Profile;
use App\Livewire\Register;
use App\Livewire\VerifyEmail;
use App\Services\AuthService;
use Illuminate\Support\Facades\Route;

// Home page - Landing page
Route::get('/', function () {
    return view('landing');
})->name('home');

// Legacy landing route (redirect to home)
Route::get('/landing', fn () => redirect()->route('home'))->name('landing');

// Design System pages
Route::prefix('design-system')->name('design-system.')->group(function () {
    // Overview (index and overview point to same view)
    Route::get('/', fn () => view('design-system.overview'))->name('index');
    Route::get('/overview', fn () => view('design-system.overview'))->name('overview');

    // Documentation pages
    $designSystemPages = [
        'colors' => 'design-system.colors',
        'typography' => 'design-system.typography',
        'spacing' => 'design-system.spacing',
        'effects' => 'design-system.effects',
    ];

    foreach ($designSystemPages as $route => $view) {
        Route::get("/{$route}", fn () => view($view))->name($route);
    }

    // Components section
    Route::get('/components', fn () => view('design-system.components-index'))->name('components');

    Route::prefix('components')->name('components.')->group(function () {
        $componentPages = [
            'base' => 'design-system.components-base',
            'terminal' => 'design-system.components-terminal',
            'specialized' => 'design-system.components-specialized',
            'utilities' => 'design-system.components-utilities',
            'emails' => 'design-system.components-emails',
            'logo' => 'design-system.logo-preview',
        ];

        foreach ($componentPages as $route => $view) {
            Route::get("/{$route}", fn () => view($view))->name($route);
        }
    });
});

// Legacy route for backward compatibility
Route::get('/design-system', fn () => redirect()->route('design-system.overview'))->name('design-system');

// Public routes
Route::middleware('guest')->group(function () {
    Route::get('/register', Register::class)->name('register');
    Route::get('/login', LoginTerminal::class)->name('login');

    // Password Reset Routes
    Route::get('/forgot-password', \App\Livewire\ForgotPassword::class)->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLink'])
        ->middleware('throttle:3,60') // 3 requests per hour
        ->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])
        ->middleware('throttle:5,60') // 5 requests per hour
        ->name('password.update');
});

// Public Codex routes
Route::prefix('codex')->name('codex.')->group(function () {
    Route::get('/', \App\Livewire\CodexIndex::class)->name('index');
    Route::get('/planets', \App\Livewire\CodexPlanets::class)->name('planets');
    Route::get('/planets/{id}', \App\Livewire\CodexPlanet::class)->name('planet');

    Route::get('/star-systems', \App\Livewire\CodexStarSystems::class)->name('star-systems');
    Route::get('/star-systems/{id}', \App\Livewire\CodexStarSystem::class)->name('star-system');

    Route::get('/contributors', \App\Livewire\CodexContributors::class)->name('contributors');
    Route::get('/contributors/{id}', \App\Livewire\CodexContributor::class)->name('contributor');

    Route::get('/hall-of-fame', \App\Livewire\CodexHallOfFame::class)->name('hall-of-fame');
});

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/inbox', Inbox::class)->name('inbox');

    // Email Verification Route
    Route::get('/email/verify', VerifyEmail::class)->name('email.verify');

    // Logout route (web)
    Route::post('/logout', function (AuthService $authService) {
        $authService->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});

// Fallback for web routes - redirect to landing page if route not found
Route::fallback(fn () => redirect()->route('home'));
