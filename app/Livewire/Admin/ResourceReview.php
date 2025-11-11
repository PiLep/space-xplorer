<?php

namespace App\Livewire\Admin;

use App\Events\ResourceApproved;
use App\Events\ResourceRejected;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ResourceReview extends Component
{
    public ?Resource $currentResource = null;

    public int $pendingCount = 0;

    public string $rejectionReason = '';

    public bool $showRejectModal = false;

    public function mount(): void
    {
        $this->loadNext();
    }

    public function loadNext(): void
    {
        // Load pending resources in batches and find first with valid file
        // This avoids loading all resources in memory
        $batchSize = 20;
        $offset = 0;
        $this->currentResource = null;

        while ($this->currentResource === null) {
            $resources = Resource::pending()
                ->with(['creator'])
                ->oldest()
                ->skip($offset)
                ->take($batchSize)
                ->get();

            if ($resources->isEmpty()) {
                break;
            }

            $this->currentResource = $resources->first(fn ($resource) => $resource->hasValidFile());
            $offset += $batchSize;
        }

        // Count pending resources with valid files (load in batches)
        $this->pendingCount = 0;
        $countOffset = 0;
        while (true) {
            $resources = Resource::pending()
                ->oldest()
                ->skip($countOffset)
                ->take($batchSize)
                ->get();

            if ($resources->isEmpty()) {
                break;
            }

            $this->pendingCount += $resources->filter(fn ($resource) => $resource->hasValidFile())->count();
            $countOffset += $batchSize;

            // Stop if we got less than batch size (last batch)
            if ($resources->count() < $batchSize) {
                break;
            }
        }

        $this->rejectionReason = '';
        $this->showRejectModal = false;
    }

    public function approve(): void
    {
        if (! $this->currentResource) {
            return;
        }

        $user = Auth::guard('admin')->user();
        $this->currentResource->approve($user);

        event(new ResourceApproved($this->currentResource, $user));

        $this->loadNext();
    }

    public function openRejectModal(): void
    {
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->rejectionReason = '';
    }

    public function reject(): void
    {
        if (! $this->currentResource) {
            return;
        }

        $user = Auth::guard('admin')->user();
        $reason = ! empty($this->rejectionReason) ? $this->rejectionReason : null;
        $this->currentResource->reject($user, $reason);

        event(new ResourceRejected($this->currentResource, $user, $reason));

        $this->loadNext();
    }

    public function render()
    {
        return view('livewire.admin.resource-review');
    }
}
