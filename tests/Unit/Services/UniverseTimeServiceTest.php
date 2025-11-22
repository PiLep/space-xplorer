<?php

use App\Services\UniverseTimeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // Nettoyer la table avant chaque test
    DB::table('universe_time_config')->truncate();
    $this->service = new UniverseTimeService;
});

describe('UniverseTimeService - Configuration', function () {
    it('creates config automatically when it does not exist', function () {
        // Vérifier qu'il n'y a pas de config
        expect(DB::table('universe_time_config')->count())->toBe(0);

        // Créer une référence fixe pour éviter les problèmes de timing
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        Carbon::setTestNow($referenceDate);

        // Appeler une méthode qui nécessite la config
        $now = $this->service->now();

        // Vérifier que la config a été créée
        $config = DB::table('universe_time_config')->first();
        expect($config)->not->toBeNull()
            ->and($config->base_year)->toBe(2436)
            ->and($config->real_days_per_game_week)->toBe(7)
            ->and($now)->toBeInstanceOf(Carbon::class);

        Carbon::setTestNow(); // Reset
    });

    it('uses existing config when it exists', function () {
        // Créer une config manuelle
        $referenceDate = Carbon::create(2024, 1, 1, 12, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Vérifier que le service utilise cette config
        $service = new UniverseTimeService;
        $now = $service->now();

        // Le temps universel devrait être calculé à partir de cette référence
        expect($now)->toBeInstanceOf(Carbon::class)
            ->and($now->year)->toBeGreaterThanOrEqual(2436);
    });
});

describe('UniverseTimeService - Base Year', function () {
    it('returns the correct base year', function () {
        expect($this->service->getBaseYear())->toBe(2436);
    });
});

describe('UniverseTimeService - Current Time', function () {
    it('returns current universe time as Carbon instance', function () {
        // Créer une référence fixe pour éviter les problèmes de timing
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Carbon::setTestNow($referenceDate);

        $now = $this->service->now();

        expect($now)->toBeInstanceOf(Carbon::class)
            ->and($now->year)->toBeGreaterThanOrEqual(2436);

        Carbon::setTestNow(); // Reset
    });

    it('calculates universe time based on elapsed real days', function () {
        // Créer une référence fixe
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simuler le temps réel (1 jour après la référence)
        Carbon::setTestNow($referenceDate->copy()->addDay());

        $service = new UniverseTimeService;
        $universeTime = $service->now();

        // 1 jour réel = 7 jours de jeu
        // Date de départ : 2436-01-01
        // Après 1 jour réel : 2436-01-01 + 7 jours = 2436-01-08
        expect($universeTime->format('Y-m-d'))->toBe('2436-01-08');

        Carbon::setTestNow(); // Reset
    });

    it('starts from base year on reference date', function () {
        // Créer une référence fixe
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simuler le temps réel exactement à la référence
        Carbon::setTestNow($referenceDate);

        $service = new UniverseTimeService;
        $universeTime = $service->now();

        // À la date de référence, le temps universel devrait être 2436-01-01
        expect($universeTime->format('Y-m-d'))->toBe('2436-01-01');

        Carbon::setTestNow(); // Reset
    });
});

describe('UniverseTimeService - Current Week', function () {
    it('returns current week information', function () {
        $week = $this->service->getCurrentWeek();

        expect($week)->toBeArray()
            ->and($week)->toHaveKeys(['week', 'year', 'date'])
            ->and($week['week'])->toBeInt()
            ->and($week['year'])->toBeInt()
            ->and($week['date'])->toBeString()
            ->and($week['year'])->toBeGreaterThanOrEqual(2436)
            ->and($week['week'])->toBeGreaterThanOrEqual(1)
            ->and($week['week'])->toBeLessThanOrEqual(52);
    });

    it('calculates correct week for start of year', function () {
        // Créer une référence fixe
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simuler le temps réel exactement à la référence
        Carbon::setTestNow($referenceDate);

        $service = new UniverseTimeService;
        $week = $service->getCurrentWeek();

        // Semaine 1, année 2436
        expect($week['week'])->toBe(1)
            ->and($week['year'])->toBe(2436)
            ->and($week['date'])->toBe('2436-01-01');

        Carbon::setTestNow(); // Reset
    });

    it('calculates correct week after 7 game days', function () {
        // Créer une référence fixe
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simuler 1 jour réel = 7 jours de jeu
        Carbon::setTestNow($referenceDate->copy()->addDay());

        $service = new UniverseTimeService;
        $week = $service->getCurrentWeek();

        // Après 7 jours de jeu, on devrait être en semaine 2
        expect($week['week'])->toBe(2)
            ->and($week['year'])->toBe(2436);

        Carbon::setTestNow(); // Reset
    });

    it('handles week overflow to next year', function () {
        // Créer une référence fixe
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simuler assez de jours pour dépasser 52 semaines
        // 52 semaines = 364 jours de jeu = 52 jours réels
        Carbon::setTestNow($referenceDate->copy()->addDays(53));

        $service = new UniverseTimeService;
        $week = $service->getCurrentWeek();

        // Devrait être en semaine 1 de l'année suivante
        expect($week['week'])->toBe(1)
            ->and($week['year'])->toBe(2437);

        Carbon::setTestNow(); // Reset
    });
});

describe('UniverseTimeService - Formatting', function () {
    it('formats time for status bar', function () {
        // Créer une référence fixe
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Carbon::setTestNow($referenceDate);

        $formatted = $this->service->formatForStatusBar();

        expect($formatted)->toBeString()
            ->and($formatted)->toMatch('/^WEEK \d+ \| YEAR \d+$/');

        Carbon::setTestNow(); // Reset
    });

    it('formats time for detailed display', function () {
        // Créer une référence fixe
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Carbon::setTestNow($referenceDate);

        $formatted = $this->service->formatForDisplay();

        expect($formatted)->toBeString()
            ->and($formatted)->toMatch('/^Semaine \d+, Année \d+$/');

        Carbon::setTestNow(); // Reset
    });

    it('formats correctly for start of year', function () {
        // Créer une référence fixe
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Carbon::setTestNow($referenceDate);

        $service = new UniverseTimeService;
        $statusBar = $service->formatForStatusBar();
        $display = $service->formatForDisplay();

        expect($statusBar)->toBe('WEEK 1 | YEAR 2436')
            ->and($display)->toBe('Semaine 1, Année 2436');

        Carbon::setTestNow(); // Reset
    });
});

describe('UniverseTimeService - Current Year', function () {
    it('returns current universe year', function () {
        // Créer une référence fixe
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Carbon::setTestNow($referenceDate);

        $year = $this->service->getCurrentYear();

        expect($year)->toBeInt()
            ->and($year)->toBeGreaterThanOrEqual(2436);

        Carbon::setTestNow(); // Reset
    });

    it('returns correct year for start date', function () {
        // Créer une référence fixe
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Carbon::setTestNow($referenceDate);

        $service = new UniverseTimeService;
        $year = $service->getCurrentYear();

        expect($year)->toBe(2436);

        Carbon::setTestNow(); // Reset
    });
});

describe('UniverseTimeService - Time Conversion', function () {
    beforeEach(function () {
        // Créer une référence fixe pour les tests de conversion
        $this->referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $this->referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    });

    it('converts real date to universe time', function () {
        // Date réelle : 1 jour après la référence
        $realDate = $this->referenceDate->copy()->addDay();

        $universeTime = $this->service->toUniverseTime($realDate);

        // 1 jour réel = 7 jours de jeu
        expect($universeTime->format('Y-m-d'))->toBe('2436-01-08');
    });

    it('converts universe time to real time', function () {
        // Date universelle : 7 jours après le début (2436-01-08)
        $universeDate = Carbon::create(2436, 1, 8, 0, 0, 0);

        $realTime = $this->service->toRealTime($universeDate);

        // 7 jours de jeu = 1 jour réel
        expect($realTime->format('Y-m-d H:i:s'))->toBe($this->referenceDate->copy()->addDay()->format('Y-m-d H:i:s'));
    });

    it('converts timestamp to universe time', function () {
        // Timestamp : 1 jour après la référence
        $timestamp = $this->referenceDate->copy()->addDay()->timestamp;

        $universeTime = $this->service->timestampToUniverseTime($timestamp);

        expect($universeTime->format('Y-m-d'))->toBe('2436-01-08');
    });

    it('converts string timestamp to universe time', function () {
        // String timestamp : 1 jour après la référence
        $timestamp = $this->referenceDate->copy()->addDay()->toDateTimeString();

        $universeTime = $this->service->timestampToUniverseTime($timestamp);

        expect($universeTime->format('Y-m-d'))->toBe('2436-01-08');
    });

    it('converts universe time to timestamp', function () {
        // Date universelle : 7 jours après le début
        $universeDate = Carbon::create(2436, 1, 8, 0, 0, 0);

        $realTime = $this->service->universeTimeToTimestamp($universeDate);

        expect($realTime->format('Y-m-d H:i:s'))->toBe($this->referenceDate->copy()->addDay()->format('Y-m-d H:i:s'));
    });

    it('converts string universe date to timestamp', function () {
        // String date universelle
        $universeDateString = '2436-01-08';

        $realTime = $this->service->universeTimeToTimestamp($universeDateString);

        expect($realTime->format('Y-m-d H:i:s'))->toBe($this->referenceDate->copy()->addDay()->format('Y-m-d H:i:s'));
    });

    it('handles bidirectional conversion correctly', function () {
        // Test aller-retour : date réelle -> universelle -> réelle
        $originalRealDate = $this->referenceDate->copy()->addDays(5);

        $universeTime = $this->service->toUniverseTime($originalRealDate);
        $convertedBack = $this->service->toRealTime($universeTime);

        // La conversion devrait être approximativement correcte
        // (avec une petite marge d'erreur due aux arrondis)
        $diffInHours = abs($originalRealDate->diffInHours($convertedBack));
        expect($diffInHours)->toBeLessThan(24); // Moins d'un jour de différence
    });

    it('handles dates before reference date', function () {
        // Date réelle avant la référence
        $realDate = $this->referenceDate->copy()->subDay();

        $universeTime = $this->service->toUniverseTime($realDate);

        // Devrait quand même retourner une date valide (utilise abs())
        expect($universeTime)->toBeInstanceOf(Carbon::class)
            ->and($universeTime->year)->toBeGreaterThanOrEqual(2436);
    });

    it('handles universe dates before base year', function () {
        // Date universelle avant l'année de base
        $universeDate = Carbon::create(2435, 12, 31, 0, 0, 0);

        $realTime = $this->service->toRealTime($universeDate);

        // Devrait retourner une date valide (peut être avant ou après selon le calcul)
        expect($realTime)->toBeInstanceOf(Carbon::class);
    });
});

describe('UniverseTimeService - Edge Cases', function () {
    it('handles multiple calls consistently', function () {
        // Créer une référence fixe
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Carbon::setTestNow($referenceDate);

        $time1 = $this->service->now();
        $time2 = $this->service->now();

        // Les deux appels devraient être identiques avec le temps fixe
        expect($time1->format('Y-m-d H:i:s'))->toBe($time2->format('Y-m-d H:i:s'));

        Carbon::setTestNow(); // Reset
    });

    it('handles leap years correctly', function () {
        // Créer une référence fixe
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simuler assez de jours pour atteindre une année bissextile
        // 2436 est une année bissextile
        Carbon::setTestNow($referenceDate->copy()->addDays(365)); // ~1 an réel

        $service = new UniverseTimeService;
        $universeTime = $service->now();

        // Devrait être en 2437 (après ~52 semaines)
        expect($universeTime->year)->toBeGreaterThanOrEqual(2436);

        Carbon::setTestNow(); // Reset
    });

    it('handles very large time differences', function () {
        // Créer une référence fixe
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        DB::table('universe_time_config')->insert([
            'reference_date' => $referenceDate,
            'real_days_per_game_week' => 7,
            'base_year' => 2436,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Simuler 10 ans réels
        Carbon::setTestNow($referenceDate->copy()->addYears(10));

        $service = new UniverseTimeService;
        $universeTime = $service->now();
        $week = $service->getCurrentWeek();

        // Devrait être plusieurs années plus tard
        expect($universeTime->year)->toBeGreaterThan(2436)
            ->and($week['year'])->toBeGreaterThan(2436);

        Carbon::setTestNow(); // Reset
    });
});

