<?php

namespace App\Livewire;

use App\Models\StarSystem;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CodexStarSystem extends Component
{
    public string $systemId;

    public ?StarSystem $system = null;

    public bool $loading = true;

    public ?string $error = null;

    public function mount(string $id): void
    {
        $this->systemId = $id;
        $this->loadSystem();
    }

    /**
     * Load star system with relations.
     */
    public function loadSystem(): void
    {
        try {
            $this->loading = true;
            $this->error = null;

            $this->system = StarSystem::with(['planets.properties', 'planets.codexEntry'])
                ->where('discovered', true)
                ->findOrFail($this->systemId);

            $this->loading = false;
        } catch (\Exception $e) {
            $this->error = 'Failed to load star system: '.$e->getMessage();
            $this->loading = false;
        }
    }

    /**
     * Get nearby star systems.
     */
    #[Computed]
    public function nearbySystems()
    {
        if (! $this->system) {
            return collect();
        }

        return StarSystem::where('discovered', true)
            ->where('id', '!=', $this->system->id)
            ->get()
            ->map(function ($system) {
                return [
                    'id' => $system->id,
                    'name' => $system->name,
                    'distance' => $this->system->distanceTo($system),
                ];
            })
            ->sortBy('distance')
            ->take(5);
    }

    public function render()
    {
        return view('livewire.codex-star-system');
    }
}

