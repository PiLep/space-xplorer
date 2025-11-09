<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\PlanetGeneratorService;
use Illuminate\Support\Facades\Log;

class GenerateHomePlanet
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private PlanetGeneratorService $planetGenerator
    ) {
        //
    }

    /**
     * Handle the event.
     *
     * Generates a home planet for the newly registered user.
     * If generation fails, logs the error but does not block user registration.
     */
    public function handle(UserRegistered $event): void
    {
        // Refresh user to get latest data
        $user = $event->user->fresh();

        // Prevent duplicate planet generation - if user already has a home planet, skip
        if ($user->home_planet_id) {
            return;
        }

        try {
            $planet = $this->planetGenerator->generate();
            $user->update(['home_planet_id' => $planet->id]);

            Log::info('Home planet generated successfully', [
                'user_id' => $user->id,
                'planet_id' => $planet->id,
                'planet_name' => $planet->name,
            ]);
        } catch (\Exception $e) {
            // Log the error but don't block user registration
            Log::error('Failed to generate home planet for user', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // User registration continues successfully, but home_planet_id remains null
            // This can be handled later with a retry mechanism or manual generation
        }
    }
}
