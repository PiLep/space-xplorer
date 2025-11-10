<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Planet;
use Illuminate\Http\JsonResponse;

class PlanetController extends Controller
{
    /**
     * Get planet details.
     */
    public function show(string $id): JsonResponse
    {
        $planet = Planet::findOrFail($id);

        return response()->json([
            'data' => [
                'planet' => [
                    'id' => $planet->id,
                    'name' => $planet->name,
                    'type' => $planet->type,
                    'size' => $planet->size,
                    'temperature' => $planet->temperature,
                    'atmosphere' => $planet->atmosphere,
                    'terrain' => $planet->terrain,
                    'resources' => $planet->resources,
                    'description' => $planet->description,
                    'image_url' => $planet->image_url,
                ],
            ],
            'status' => 'success',
        ]);
    }
}
