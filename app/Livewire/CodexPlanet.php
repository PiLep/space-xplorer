<?php

namespace App\Livewire;

use App\Models\CodexEntry;
use App\Services\CodexService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CodexPlanet extends Component
{
    public string $entryId;

    public ?CodexEntry $entry = null;

    public bool $loading = true;

    public ?string $error = null;

    public bool $showNameModal = false;

    public bool $showContributeModal = false;

    public function mount(string $id): void
    {
        $this->entryId = $id;
        $this->loadEntry();
    }

    /**
     * Load codex entry with relations.
     */
    public function loadEntry(): void
    {
        try {
            $this->loading = true;
            $this->error = null;

            $this->entry = CodexEntry::with(['planet.properties', 'discoveredBy'])
                ->public()
                ->findOrFail($this->entryId);

            $this->loading = false;
        } catch (\Exception $e) {
            $this->error = 'Failed to load planet entry: '.$e->getMessage();
            $this->loading = false;
        }
    }

    /**
     * Check if user can name the planet.
     */
    public function canUserName(): bool
    {
        if (! Auth::check() || ! $this->entry) {
            return false;
        }

        return app(CodexService::class)->canUserNamePlanet($this->entry, Auth::user());
    }

    /**
     * Check if user can contribute.
     */
    public function canUserContribute(): bool
    {
        if (! Auth::check() || ! $this->entry) {
            return false;
        }

        return app(CodexService::class)->canUserContribute($this->entry, Auth::user());
    }

    /**
     * Open name planet modal.
     */
    public function openNameModal(): void
    {
        if ($this->canUserName()) {
            $this->showNameModal = true;
        }
    }

    /**
     * Close name planet modal.
     */
    public function closeNameModal(): void
    {
        $this->showNameModal = false;
    }

    /**
     * Open contribute modal.
     */
    public function openContributeModal(): void
    {
        if ($this->canUserContribute()) {
            $this->showContributeModal = true;
        }
    }

    /**
     * Close contribute modal.
     */
    public function closeContributeModal(): void
    {
        $this->showContributeModal = false;
    }

    /**
     * Listen for modal close events.
     */
    protected $listeners = [
        'close-modal' => 'closeModals',
        'planet-named' => 'handlePlanetNamed',
        'contribution-added' => 'handleContributionAdded',
    ];

    /**
     * Close all modals.
     */
    public function closeModals(): void
    {
        $this->showNameModal = false;
        $this->showContributeModal = false;
    }

    /**
     * Handle planet named event.
     */
    public function handlePlanetNamed(string $entryId): void
    {
        if ($this->entry && $this->entry->id === $entryId) {
            $this->loadEntry();
        }
    }

    /**
     * Handle contribution added event.
     */
    public function handleContributionAdded(string $entryId): void
    {
        if ($this->entry && $this->entry->id === $entryId) {
            $this->loadEntry();
        }
    }

    public function render()
    {
        return view('livewire.codex-planet');
    }
}

