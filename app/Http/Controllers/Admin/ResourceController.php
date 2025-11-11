<?php

namespace App\Http\Controllers\Admin;

use App\Events\ResourceApproved;
use App\Events\ResourceRejected;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveResourceRequest;
use App\Http\Requests\Admin\StoreResourceRequest;
use App\Jobs\GenerateResourceJob;
use App\Models\Resource;
use App\Services\ResourceGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function __construct(
        private ResourceGenerationService $resourceGenerator
    ) {
        //
    }

    /**
     * Display a listing of resources.
     */
    public function index(): View
    {
        $query = Resource::with(['creator', 'approver'])->latest();

        // Filter by type if provided
        if (request()->filled('type')) {
            $query->ofType(request()->type);
        }

        // Filter by status if provided
        if (request()->has('status') && request()->status !== '') {
            $query->where('status', request()->status);
        }

        $resources = $query->paginate(20)->withQueryString();

        return view('admin.resources.index', [
            'resources' => $resources,
        ]);
    }

    /**
     * Display the quick review page for pending resources.
     */
    public function review(): View
    {
        return view('admin.resources.review');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.resources.create');
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreResourceRequest $request): RedirectResponse
    {
        try {
            $user = Auth::guard('admin')->user();

            // Tags are already converted to array by prepareForValidation()
            $manualTags = $request->tags ?? [];

            // Extract tags from prompt based on resource type
            $extractedTags = [];
            if (in_array($request->type, ['planet_image', 'planet_video'])) {
                $extractedTags = $this->resourceGenerator->extractPlanetTagsFromPrompt($request->prompt);
            } elseif ($request->type === 'avatar_image') {
                $extractedTags = $this->resourceGenerator->extractAvatarTagsFromPrompt($request->prompt);
            }

            // Merge manual tags with extracted tags
            $allTags = array_unique(array_merge($extractedTags, $manualTags));

            // Create resource with 'generating' status
            $resource = Resource::create([
                'type' => $request->type,
                'status' => 'generating',
                'file_path' => null, // Will be set when generation completes
                'prompt' => $request->prompt,
                'tags' => $allTags,
                'description' => $request->description,
                'created_by' => $user->id,
            ]);

            // Dispatch job for async generation
            GenerateResourceJob::dispatch($resource);

            return redirect()->route('admin.resources.index')
                ->with('success', 'Resource generation started. It will be available for review once generation is complete.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create resource: '.$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource): View
    {
        $resource->load(['creator', 'approver']);

        return view('admin.resources.show', [
            'resource' => $resource,
        ]);
    }

    /**
     * Approve a resource.
     */
    public function approve(Resource $resource, ApproveResourceRequest $request): RedirectResponse
    {
        if ($request->action === 'approve') {
            $resource->approve(Auth::guard('admin')->user());

            event(new ResourceApproved($resource, Auth::guard('admin')->user()));

            return redirect()->route('admin.resources.show', $resource)
                ->with('success', 'Resource approved successfully.');
        } else {
            $resource->reject(
                Auth::guard('admin')->user(),
                $request->rejection_reason
            );

            event(new ResourceRejected(
                $resource,
                Auth::guard('admin')->user(),
                $request->rejection_reason
            ));

            return redirect()->route('admin.resources.show', $resource)
                ->with('success', 'Resource rejected successfully.');
        }
    }
}
