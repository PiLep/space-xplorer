<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // Nettoyer la table avant chaque test
    DB::table('universe_time_config')->truncate();
});

describe('Universe Time Helpers', function () {
    it('universe_time() returns current universe time', function () {
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

        $time = universe_time();

        expect($time)->toBeInstanceOf(Carbon::class)
            ->and($time->year)->toBeGreaterThanOrEqual(2436);

        Carbon::setTestNow(); // Reset
    });

    it('universe_week() returns current week information', function () {
        $week = universe_week();

        expect($week)->toBeArray()
            ->and($week)->toHaveKeys(['week', 'year', 'date'])
            ->and($week['week'])->toBeInt()
            ->and($week['year'])->toBeInt()
            ->and($week['date'])->toBeString();
    });

    it('universe_time_status() formats time for status bar', function () {
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

        $status = universe_time_status();

        expect($status)->toBeString()
            ->and($status)->toMatch('/^WEEK \d+ \| YEAR \d+$/');

        Carbon::setTestNow(); // Reset
    });

    it('universe_time_display() formats time for detailed display', function () {
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

        $display = universe_time_display();

        expect($display)->toBeString()
            ->and($display)->toMatch('/^Semaine \d+, Année \d+$/');

        Carbon::setTestNow(); // Reset
    });

    it('universe_year() returns current universe year', function () {
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

        $year = universe_year();

        expect($year)->toBeInt()
            ->and($year)->toBeGreaterThanOrEqual(2436);

        Carbon::setTestNow(); // Reset
    });

    it('universe_base_year() returns base year', function () {
        $baseYear = universe_base_year();

        expect($baseYear)->toBe(2436);
    });

    it('helpers use the same service instance', function () {
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

        // Tous les helpers devraient retourner des valeurs cohérentes
        $time = universe_time();
        $week = universe_week();
        $year = universe_year();
        $status = universe_time_status();
        $display = universe_time_display();

        expect($week['year'])->toBe($year)
            ->and($status)->toContain((string) $year)
            ->and($display)->toContain((string) $year)
            ->and($time->year)->toBe($year);

        Carbon::setTestNow(); // Reset
    });

    it('helpers work correctly with fixed reference date', function () {
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

        expect(universe_year())->toBe(2436)
            ->and(universe_base_year())->toBe(2436)
            ->and(universe_week()['week'])->toBe(1)
            ->and(universe_week()['year'])->toBe(2436)
            ->and(universe_time_status())->toBe('WEEK 1 | YEAR 2436')
            ->and(universe_time_display())->toBe('Semaine 1, Année 2436');

        Carbon::setTestNow(); // Reset
    });
});
