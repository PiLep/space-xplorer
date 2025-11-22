<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UniverseTimeService
{
    // Ratio : 1 jour réel = 1 semaine de jeu (7 jours)
    private const DAYS_PER_WEEK = 7;

    // Année terrestre de départ
    private const BASE_YEAR = 2436;

    /**
     * Récupère la date de référence réelle (quand l'univers a été créé)
     */
    private function getRealReferenceDate(): Carbon
    {
        // Ne pas mettre en cache pour permettre les modifications en base
        $config = DB::table('universe_time_config')->first();

        if (! $config) {
            // Si pas de config, utiliser maintenant comme référence
            $now = now();
            $referenceDate = Carbon::create(self::BASE_YEAR, 1, 1, 0, 0, 0);

            DB::table('universe_time_config')->insert([
                'reference_date' => $now, // Date réelle de création
                'real_days_per_game_week' => self::DAYS_PER_WEEK,
                'base_year' => self::BASE_YEAR,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return $now;
        }

        // La reference_date stocke la date RÉELLE de création de l'univers
        return Carbon::parse($config->reference_date);
    }

    /**
     * Calcule le temps universel actuel
     * Utilise les années terrestres réelles mais décalées depuis 2436
     */
    public function now(): Carbon
    {
        $realReference = $this->getRealReferenceDate();

        // Calcul : jours réels écoulés depuis la création de l'univers
        // Utiliser abs() et floor() pour toujours avoir un nombre entier positif
        $realDaysElapsed = (int) floor(abs(now()->diffInDays($realReference)));

        // Conversion : chaque jour réel = 1 semaine de jeu (7 jours)
        $gameDaysElapsed = $realDaysElapsed * self::DAYS_PER_WEEK;

        // Date universelle de départ : 1er janvier 2436
        $universeStartDate = Carbon::create(self::BASE_YEAR, 1, 1, 0, 0, 0);

        // Retourner la date de départ universelle + les jours de jeu écoulés
        return $universeStartDate->copy()->addDays($gameDaysElapsed);
    }

    /**
     * Récupère la semaine universelle actuelle
     * Utilise les années terrestres réelles
     */
    public function getCurrentWeek(): array
    {
        $universeTime = $this->now();

        // Calculer depuis le début de l'année universelle
        $year = (int) $universeTime->format('Y');
        $yearStart = Carbon::create($year, 1, 1, 0, 0, 0);

        // Calculer le nombre de jours depuis le début de l'année
        $daysSinceYearStart = (int) floor($yearStart->diffInDays($universeTime));

        // Calculer la semaine (semaine 1 commence le 1er janvier)
        $week = (int) floor($daysSinceYearStart / 7) + 1;

        // Si on dépasse 52 semaines, on passe à l'année suivante
        if ($week > 52) {
            $year++;
            $week = 1;
        }

        return [
            'week' => $week,
            'year' => $year,
            'date' => $universeTime->format('Y-m-d'),
        ];
    }

    /**
     * Formate la semaine pour affichage dans la barre
     * Format: "WEEK 42 | YEAR 2436"
     */
    public function formatForStatusBar(): string
    {
        $current = $this->getCurrentWeek();

        return sprintf('WEEK %d | YEAR %d', $current['week'], $current['year']);
    }

    /**
     * Formate la semaine pour affichage détaillé
     * Format: "Semaine 42, Année 2436"
     */
    public function formatForDisplay(): string
    {
        $current = $this->getCurrentWeek();

        return sprintf('Semaine %d, Année %d', $current['week'], $current['year']);
    }

    /**
     * Récupère l'année universelle actuelle (année terrestre)
     */
    public function getCurrentYear(): int
    {
        return $this->getCurrentWeek()['year'];
    }

    /**
     * Récupère l'année de base (départ)
     */
    public function getBaseYear(): int
    {
        return self::BASE_YEAR;
    }

    /**
     * Convertit une date réelle en date universelle
     *
     * @param  Carbon  $realDate  Date réelle à convertir
     * @return Carbon Date universelle correspondante
     */
    public function toUniverseTime(Carbon $realDate): Carbon
    {
        $realReference = $this->getRealReferenceDate();

        // Calcul : jours réels écoulés depuis la référence
        $realDaysElapsed = (int) floor(abs($realDate->diffInDays($realReference)));

        // Conversion : chaque jour réel = 1 semaine de jeu (7 jours)
        $gameDaysElapsed = $realDaysElapsed * self::DAYS_PER_WEEK;

        // Date universelle de départ : 1er janvier 2436
        $universeStartDate = Carbon::create(self::BASE_YEAR, 1, 1, 0, 0, 0);

        // Retourner la date de départ universelle + les jours de jeu écoulés
        return $universeStartDate->copy()->addDays($gameDaysElapsed);
    }

    /**
     * Convertit une date universelle en date réelle approximative
     *
     * @param  Carbon  $universeDate  Date universelle à convertir
     * @return Carbon Date réelle approximative correspondante
     */
    public function toRealTime(Carbon $universeDate): Carbon
    {
        $realReference = $this->getRealReferenceDate();

        // Date universelle de départ : 1er janvier 2436
        $universeStartDate = Carbon::create(self::BASE_YEAR, 1, 1, 0, 0, 0);

        // Calcul : jours de jeu écoulés depuis le début de l'univers
        $gameDaysElapsed = abs($universeStartDate->diffInDays($universeDate));

        // Conversion inverse : chaque semaine de jeu (7 jours) = 1 jour réel
        // Utiliser floor() pour éviter les problèmes de précision avec les très petits nombres
        $realDaysElapsed = floor($gameDaysElapsed / self::DAYS_PER_WEEK);

        // Retourner la date de référence réelle + les jours réels écoulés
        return $realReference->copy()->addDays($realDaysElapsed);
    }

    /**
     * Récupère la date universelle correspondant à un timestamp réel
     *
     * @param  string|int|\DateTimeInterface  $timestamp  Timestamp réel (string, int, ou DateTime)
     * @return Carbon Date universelle correspondante
     */
    public function timestampToUniverseTime($timestamp): Carbon
    {
        $realDate = Carbon::parse($timestamp);

        return $this->toUniverseTime($realDate);
    }

    /**
     * Récupère le timestamp réel approximatif correspondant à une date universelle
     *
     * @param  Carbon|string  $universeDate  Date universelle
     * @return Carbon Timestamp réel approximatif
     */
    public function universeTimeToTimestamp($universeDate): Carbon
    {
        if (is_string($universeDate)) {
            $universeDate = Carbon::parse($universeDate);
        }

        return $this->toRealTime($universeDate);
    }
}

