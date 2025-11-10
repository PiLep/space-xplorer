<?php

$providers = [
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class,
];

// Register test service provider for E2E tests and when API key is not configured
// This ensures image generation doesn't fail in CI/E2E environments
$providers[] = App\Providers\TestServiceProvider::class;

return $providers;
