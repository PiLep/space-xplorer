<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\PlanetGeneratorService;
use App\Services\StarSystemGeneratorService;
use Illuminate\Support\Facades\Log;

class GenerateHomePlanet
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private PlanetGeneratorService $planetGenerator,
        private StarSystemGeneratorService $starSystemGenerator
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
            // Générer un système stellaire complet pour la planète d'origine
            // Chaque joueur démarre dans son propre système (pas de partage entre joueurs)
            // Le système peut contenir plusieurs planètes, mais appartient uniquement à ce joueur
            $system = $this->starSystemGenerator->generateSystem();

            // Prendre la première planète du système comme planète d'origine
            $homePlanet = $system->planets->first();

            if (! $homePlanet) {
                throw new \RuntimeException('No planet generated in star system');
            }

            $user->update(['home_planet_id' => $homePlanet->id]);

            Log::info('Home planet generated successfully', [
                'user_id' => $user->id,
                'planet_id' => $homePlanet->id,
                'planet_name' => $homePlanet->name,
                'star_system_id' => $system->id,
                'star_system_name' => $system->name,
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
