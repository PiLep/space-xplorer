<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Get user details.
     */
    public function show(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'home_planet_id' => $user->home_planet_id,
                ],
            ],
            'status' => 'success',
        ]);
    }

    /**
     * Update user profile.
     * Only the user can update their own profile (authorization check).
     */
    public function update(UpdateProfileRequest $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Authorization check: user can only update their own profile
        if ($request->user()->id !== $id) {
            return response()->json([
                'message' => 'Unauthorized. You can only update your own profile.',
                'status' => 'error',
            ], 403);
        }

        // Update only provided fields
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }

        $user->save();

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'home_planet_id' => $user->home_planet_id,
                ],
            ],
            'message' => 'Profile updated successfully',
            'status' => 'success',
        ]);
    }

    /**
     * Get user's home planet.
     */
    public function getHomePlanet(string $id): JsonResponse
    {
        $user = User::with('homePlanet')->findOrFail($id);

        if (! $user->homePlanet) {
            return response()->json([
                'message' => 'User does not have a home planet assigned.',
                'status' => 'error',
            ], 404);
        }

        return response()->json([
            'data' => [
                'planet' => [
                    'id' => $user->homePlanet->id,
                    'name' => $user->homePlanet->name,
                    'type' => $user->homePlanet->type,
                    'size' => $user->homePlanet->size,
                    'temperature' => $user->homePlanet->temperature,
                    'atmosphere' => $user->homePlanet->atmosphere,
                    'terrain' => $user->homePlanet->terrain,
                    'resources' => $user->homePlanet->resources,
                    'description' => $user->homePlanet->description,
                ],
            ],
            'status' => 'success',
        ]);
    }
}
