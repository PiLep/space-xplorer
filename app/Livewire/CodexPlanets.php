<?php

namespace App\Livewire;

use App\Models\CodexContribution;
use App\Models\CodexEntry;
use App\Models\Planet;
use App\Models\StarSystem;
use App\Services\CodexService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CodexPlanets extends Component
{
    public string $search = '';

    public int $perPage = 20;

    /**
     * Get paginated codex entries with search filter.
     */
    #[Computed]
    public function entries(): LengthAwarePaginator
    {
        $filters = array_filter([
            'search' => $this->search ?: null,
        ]);

        return app(CodexService::class)->getEntries($filters, $this->perPage);
    }

    public function render()
    {
        return view('livewire.codex-planets', [
            'stats' => $this->stats,
        ]);
    }

    /**
     * Get codex statistics.
     */
    #[Computed]
    public function stats(): array
    {
        return cache()->remember('codex.stats', now()->addMinutes(5), function () {
            return [
                'total_articles' => CodexEntry::public()->count(),
                'planets' => Planet::count(),
                'star_systems' => StarSystem::where('discovered', true)->count(),
                'named' => CodexEntry::public()->named()->count(),
                'contributors' => CodexContribution::distinct('contributor_user_id')->count('contributor_user_id'),
                'contributions' => CodexContribution::count(),
            ];
        });
    }
}

