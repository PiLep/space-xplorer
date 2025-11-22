<?php

use App\Services\UniverseTimeService;
use Carbon\Carbon;

if (! function_exists('universe_time')) {
    /**
     * Helper pour accéder au temps universel actuel
     */
    function universe_time(): Carbon
    {
        return app(UniverseTimeService::class)->now();
    }
}

if (! function_exists('universe_week')) {
    /**
     * Helper pour récupérer la semaine universelle actuelle
     */
    function universe_week(): array
    {
        return app(UniverseTimeService::class)->getCurrentWeek();
    }
}

if (! function_exists('universe_time_status')) {
    /**
     * Helper pour formater le temps universel pour la barre de status
     */
    function universe_time_status(): string
    {
        return app(UniverseTimeService::class)->formatForStatusBar();
    }
}

if (! function_exists('universe_time_display')) {
    /**
     * Helper pour formater le temps universel pour affichage détaillé
     */
    function universe_time_display(): string
    {
        return app(UniverseTimeService::class)->formatForDisplay();
    }
}

if (! function_exists('universe_year')) {
    /**
     * Helper pour récupérer l'année universelle actuelle
     */
    function universe_year(): int
    {
        return app(UniverseTimeService::class)->getCurrentYear();
    }
}

if (! function_exists('universe_base_year')) {
    /**
     * Helper pour récupérer l'année de base (départ)
     */
    function universe_base_year(): int
    {
        return app(UniverseTimeService::class)->getBaseYear();
    }
}

