<?php

namespace App\Livewire;

use App\Models\CodexEntry;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CodexContributor extends Component
{
    public string $userId;

    public ?User $contributor = null;

    public bool $loading = true;

    public ?string $error = null;

    public function mount(string $id): void
    {
        $this->userId = $id;
        $this->loadContributor();
    }

    /**
     * Load contributor with relations.
     */
    public function loadContributor(): void
    {
        try {
            $this->loading = true;
            $this->error = null;

            $this->contributor = User::with([
                'codexContributions.codexEntry.planet',
                'discoveredPlanets.planet',
            ])
                ->findOrFail($this->userId);

            $this->loading = false;
        } catch (\Exception $e) {
            $this->error = 'Failed to load contributor: '.$e->getMessage();
            $this->loading = false;
        }
    }

    /**
     * Get approved contributions.
     */
    #[Computed]
    public function approvedContributions()
    {
        if (! $this->contributor) {
            return collect();
        }

        return $this->contributor->codexContributions()
            ->where('status', 'approved')
            ->with('codexEntry.planet')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get discovered planets.
     */
    #[Computed]
    public function discoveredPlanets()
    {
        if (! $this->contributor) {
            return collect();
        }

        return CodexEntry::where('discovered_by_user_id', $this->contributor->id)
            ->with('planet.properties')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.codex-contributor');
    }
}

