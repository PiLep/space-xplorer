<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StarSystem;
use Illuminate\View\View;

class MapController extends Controller
{
    /**
     * Show the universe map.
     */
    public function index(): View
    {
        $systems = StarSystem::all()
            ->map(function ($system) {
                return [
                    'id' => $system->id,
                    'name' => $system->name,
                    'x' => (float) $system->x,
                    'y' => (float) $system->y,
                    'z' => (float) $system->z,
                    'star_type' => $system->star_type,
                    'planet_count' => $system->planet_count,
                    'discovered' => (bool) $system->discovered, // Ensure boolean type
                ];
            });

        return view('admin.map', [
            'systems' => $systems,
        ]);
    }

    /**
     * Display a listing of star systems.
     */
    public function list(): View
    {
        $starSystems = StarSystem::withCount('planets')
            ->latest()
            ->paginate(20);

        return view('admin.systems.index', [
            'starSystems' => $starSystems,
        ]);
    }

    /**
     * Show the map of a specific star system.
     */
    public function show(string $id): View
    {
        $starSystem = StarSystem::with('planets.properties')->findOrFail($id);

        $planets = $starSystem->planets->map(function ($planet) {
            return [
                'id' => $planet->id,
                'name' => $planet->name,
                'orbital_distance' => $planet->orbital_distance ? (float) $planet->orbital_distance : null,
                'orbital_angle' => $planet->orbital_angle ? (float) $planet->orbital_angle : null,
                'orbital_inclination' => $planet->orbital_inclination ? (float) $planet->orbital_inclination : null,
                'x' => $planet->x ? (float) $planet->x : null,
                'y' => $planet->y ? (float) $planet->y : null,
                'z' => $planet->z ? (float) $planet->z : null,
                'type' => $planet->type,
                'size' => $planet->size,
                'has_image' => $planet->hasImage(),
                'has_video' => $planet->hasVideo(),
            ];
        });

        return view('admin.system-map', [
            'starSystem' => [
                'id' => $starSystem->id,
                'name' => $starSystem->name,
                'star_type' => $starSystem->star_type,
                'planet_count' => $starSystem->planet_count,
                'x' => (float) $starSystem->x,
                'y' => (float) $starSystem->y,
                'z' => (float) $starSystem->z,
            ],
            'planets' => $planets,
        ]);
    }
}
