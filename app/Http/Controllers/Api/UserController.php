<?php

namespace App\Http\Controllers\Api;

use App\Events\AvatarChanged;
use App\Events\EmailChanged;
use App\Events\UserProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Resource;
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

        // Track changed attributes for the event
        $changedAttributes = [];

        // Update only provided fields
        if ($request->has('name') && $user->name !== $request->name) {
            $changedAttributes['name'] = ['old' => $user->name, 'new' => $request->name];
            $user->name = $request->name;
        }
        if ($request->has('email') && $user->email !== $request->email) {
            $oldEmail = $user->email;
            $changedAttributes['email'] = ['old' => $oldEmail, 'new' => $request->email];
            $user->email = $request->email;

            // Dispatch event for email change
            event(new EmailChanged($user, $oldEmail, $request->email));
        }

        $user->save();

        // Dispatch event if any attributes were changed
        if (! empty($changedAttributes)) {
            event(new UserProfileUpdated($user, $changedAttributes));
        }

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
     * Update user avatar.
     * Only the user can update their own avatar (authorization check).
     */
    public function updateAvatar(UpdateAvatarRequest $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Authorization check: user can only update their own avatar
        if ($request->user()->id !== $id) {
            return response()->json([
                'message' => 'Unauthorized. You can only update your own avatar.',
                'status' => 'error',
            ], 403);
        }

        // Get the resource and verify it's approved
        $resource = Resource::findOrFail($request->resource_id);

        if ($resource->type !== 'avatar_image' || ! $resource->isApproved()) {
            return response()->json([
                'message' => 'The selected avatar is not available or not approved.',
                'status' => 'error',
            ], 400);
        }

        // Track changed attributes for the event
        $oldAvatarUrl = $user->avatar_url;
        $newAvatarPath = $resource->file_path;

        // Update avatar
        $user->update([
            'avatar_url' => $newAvatarPath,
            'avatar_generating' => false,
        ]);

        // Dispatch events
        event(new AvatarChanged($user, $oldAvatarUrl, $newAvatarPath));
        event(new UserProfileUpdated($user, [
            'avatar_url' => [
                'old' => $oldAvatarUrl,
                'new' => $newAvatarPath,
            ],
        ]));

        // Reload user to get the computed avatar_url
        $user->refresh();

        return response()->json([
            'data' => [
                'avatar_url' => $user->avatar_url,
            ],
            'message' => 'Avatar updated successfully',
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
                    'image_url' => $user->homePlanet->image_url,
                ],
            ],
            'status' => 'success',
        ]);
    }
}
