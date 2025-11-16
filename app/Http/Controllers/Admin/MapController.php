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
                    'discovered' => $system->discovered,
                ];
            });

        return view('admin.map', [
            'systems' => $systems,
        ]);
    }
}

