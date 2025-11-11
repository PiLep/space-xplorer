<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(): View
    {
        $totalUsers = User::count();
        $recentUsers = User::latest()->take(5)->get();

        // Get resource statistics
        $resourceStats = $this->getResourceStatistics();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'recentUsers' => $recentUsers,
            'resourceStats' => $resourceStats,
        ]);
    }

    /**
     * Get resource statistics with optimization percentages.
     *
     * @return array<string, array<string, mixed>>
     */
    private function getResourceStatistics(): array
    {
        $optimalThresholds = config('resources.optimal_thresholds', []);
        $stats = [];

        foreach (['avatar_image', 'planet_image', 'planet_video'] as $type) {
            $currentCount = Resource::approved()->ofType($type)->count();
            $optimalThreshold = $optimalThresholds[$type] ?? 0;
            $optimizationPercentage = $optimalThreshold > 0
                ? ($currentCount / $optimalThreshold) * 100
                : 0;

            // Determine status and color (use capped percentage for status, but show real percentage)
            $cappedPercentage = min(100, $optimizationPercentage);
            $status = $this->getOptimizationStatus($cappedPercentage);

            $stats[$type] = [
                'current' => $currentCount,
                'optimal' => $optimalThreshold,
                'percentage' => round($optimizationPercentage, 1),
                'capped_percentage' => round($cappedPercentage, 1),
                'status' => $status['status'],
                'color' => $status['color'],
                'label' => str_replace('_', ' ', ucwords($type, '_')),
            ];
        }

        return $stats;
    }

    /**
     * Get optimization status based on percentage.
     *
     * @return array<string, string>
     */
    private function getOptimizationStatus(float $percentage): array
    {
        $thresholds = config('resources.optimization_thresholds', [
            'insufficient' => 50,
            'in_progress' => 80,
            'optimal' => 100,
        ]);

        if ($percentage < $thresholds['insufficient']) {
            return [
                'status' => 'insufficient',
                'color' => 'red',
            ];
        } elseif ($percentage < $thresholds['in_progress']) {
            return [
                'status' => 'in_progress',
                'color' => 'orange',
            ];
        } elseif ($percentage <= $thresholds['optimal']) {
            return [
                'status' => 'optimal',
                'color' => 'green',
            ];
        } else {
            return [
                'status' => 'above_threshold',
                'color' => 'blue',
            ];
        }
    }
}
