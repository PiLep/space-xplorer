<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;

class ResourceController extends Controller
{
    /**
     * Get approved avatar resources for gallery.
     */
    public function getAvatars(): JsonResponse
    {
        $avatars = Resource::approved()
            ->ofType('avatar_image')
            ->latest()
            ->get()
            ->map(function ($resource) {
                return [
                    'id' => $resource->id,
                    'file_url' => $resource->file_url,
                    'description' => $resource->description,
                    'tags' => $resource->tags,
                ];
            });

        return response()->json([
            'data' => [
                'avatars' => $avatars,
            ],
            'status' => 'success',
        ]);
    }
}
