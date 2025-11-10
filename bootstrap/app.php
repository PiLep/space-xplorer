<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\EnsureAdminIsAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Report exceptions (logging)
        $exceptions->reportable(function (\Throwable $e): void {
            // Custom exceptions are already logged by Laravel
            // Add custom reporting logic here if needed
        });

        // Render exceptions (HTTP responses)
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // Handle API requests with JSON responses
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = 500;
                $message = 'An error occurred';

                // Map custom exceptions to appropriate HTTP status codes
                if ($e instanceof \App\Exceptions\ProviderConfigurationException) {
                    $statusCode = 503; // Service Unavailable
                    $message = $e->getMessage();
                } elseif ($e instanceof \App\Exceptions\UnsupportedProviderException) {
                    $statusCode = 400; // Bad Request
                    $message = $e->getMessage();
                } elseif ($e instanceof \App\Exceptions\ApiRequestException) {
                    $statusCode = $e->getCode() ?: 502; // Bad Gateway
                    $message = $e->getMessage();
                } elseif ($e instanceof \App\Exceptions\StorageException) {
                    $statusCode = 503; // Service Unavailable
                    $message = $e->getMessage();
                } elseif ($e instanceof \App\Exceptions\ImageGenerationException) {
                    $statusCode = 500;
                    $message = $e->getMessage();
                } elseif ($e instanceof \App\Exceptions\VideoGenerationException) {
                    $statusCode = 500;
                    $message = $e->getMessage();
                } elseif ($e instanceof \App\Exceptions\JobTimeoutException) {
                    $statusCode = 504; // Gateway Timeout
                    $message = $e->getMessage();
                } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                    $statusCode = 422; // Unprocessable Entity
                    $message = 'Validation failed';
                } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    $statusCode = 401; // Unauthorized
                    $message = 'Unauthenticated';
                } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    $statusCode = 404; // Not Found
                    $message = 'Resource not found';
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    $statusCode = 404;
                    $message = 'Route not found';
                } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                    $statusCode = 405; // Method Not Allowed
                    $message = 'Method not allowed';
                } else {
                    // For other exceptions, show generic message in production
                    if (! app()->environment('local', 'testing')) {
                        $message = 'An error occurred';
                    } else {
                        $message = $e->getMessage();
                    }
                }

                $response = [
                    'status' => 'error',
                    'message' => $message,
                ];

                // Include error details in local/testing environments
                if (app()->environment('local', 'testing')) {
                    $response['error'] = [
                        'exception' => get_class($e),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'trace' => $e->getTraceAsString(),
                    ];
                }

                // Include validation errors if it's a ValidationException
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    $response['errors'] = $e->errors();
                }

                return response()->json($response, $statusCode);
            }

            // Let Laravel handle other exceptions (web routes, etc.)
            return null;
        });
    })->create();
