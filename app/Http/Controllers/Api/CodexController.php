<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContributeToCodexRequest;
use App\Http\Requests\NamePlanetRequest;
use App\Models\CodexEntry;
use App\Services\CodexService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CodexController extends Controller
{
    public function __construct(
        private CodexService $codexService
    ) {}

    /**
     * Get paginated list of codex entries (planets) with search filter.
     *
     * Public endpoint with rate limiting.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search']);
        $perPage = min((int) $request->get('per_page', 20), 100); // Max 100 per page

        $entries = $this->codexService->getEntries($filters, $perPage);

        return response()->json([
            'data' => $entries,
            'status' => 'success',
        ]);
    }

    /**
     * Get details of a specific codex entry (planet).
     *
     * Public endpoint with rate limiting.
     */
    public function show(string $id): JsonResponse
    {
        $entry = CodexEntry::with(['planet.properties', 'discoveredBy'])
            ->public()
            ->findOrFail($id);

        $planet = $entry->planet;
        $properties = $planet->properties;

        return response()->json([
            'data' => [
                'id' => $entry->id,
                'name' => $entry->name,
                'fallback_name' => $entry->fallback_name,
                'display_name' => $entry->display_name,
                'description' => $entry->description,
                'is_named' => $entry->is_named,
                'discovered_by' => $entry->discoveredBy ? [
                    'id' => $entry->discoveredBy->id,
                    'name' => $entry->discoveredBy->name,
                    'matricule' => $entry->discoveredBy->matricule,
                ] : null,
                'planet' => [
                    'id' => $planet->id,
                    'image_url' => $planet->image_url,
                    'video_url' => $planet->video_url,
                ],
                'characteristics' => $properties ? [
                    'type' => $properties->type,
                    'size' => $properties->size,
                    'temperature' => $properties->temperature,
                    'atmosphere' => $properties->atmosphere,
                    'terrain' => $properties->terrain,
                    'resources' => $properties->resources,
                ] : null,
                'created_at' => $entry->created_at,
            ],
            'status' => 'success',
        ]);
    }

    /**
     * Search codex entries by name (for autocompletion).
     *
     * Public endpoint with rate limiting.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (empty($query) || mb_strlen($query) < 2) {
            return response()->json([
                'data' => [],
                'status' => 'success',
            ]);
        }

        $results = $this->codexService->searchEntries($query, 10);

        return response()->json([
            'data' => $results->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'name' => $entry->name,
                    'fallback_name' => $entry->fallback_name,
                    'display_name' => $entry->display_name,
                    'planet_id' => $entry->planet_id,
                ];
            }),
            'status' => 'success',
        ]);
    }

    /**
     * Name a planet (authenticated endpoint).
     *
     * Requires authentication and rate limiting.
     */
    public function namePlanet(string $id, NamePlanetRequest $request): JsonResponse
    {
        $entry = CodexEntry::findOrFail($id);
        $user = Auth::user();

        try {
            // Validate name using service (includes uniqueness and forbidden words)
            $this->codexService->validateName($request->input('name'));

            // Name the planet
            $entry = $this->codexService->namePlanet($entry, $user, $request->input('name'));

            return response()->json([
                'data' => [
                    'id' => $entry->id,
                    'name' => $entry->name,
                    'fallback_name' => $entry->fallback_name,
                    'display_name' => $entry->display_name,
                    'is_named' => $entry->is_named,
                ],
                'message' => 'Planet named successfully',
                'status' => 'success',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'status' => 'error',
            ], 422);
        }
    }

    /**
     * Contribute to a codex entry (authenticated endpoint).
     *
     * Requires authentication and rate limiting.
     */
    public function contribute(string $id, ContributeToCodexRequest $request): JsonResponse
    {
        $entry = CodexEntry::findOrFail($id);
        $user = Auth::user();

        // Check permissions
        if (! $this->codexService->canUserContribute($entry, $user)) {
            return response()->json([
                'message' => 'You are not authorized to contribute to this codex entry.',
                'status' => 'error',
            ], 403);
        }

        // Create contribution
        $contribution = $entry->contributions()->create([
            'contributor_user_id' => $user->id,
            'content_type' => 'description',
            'content' => $request->input('content'),
            'status' => 'pending', // Requires moderation for MVP
        ]);

        return response()->json([
            'data' => [
                'id' => $contribution->id,
                'content_type' => $contribution->content_type,
                'status' => $contribution->status,
            ],
            'message' => 'Contribution added successfully. It will be reviewed before publication.',
            'status' => 'success',
        ], 201);
    }
}

