<?php

namespace App\Livewire;

use App\Models\StarSystem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CodexStarSystems extends Component
{
    public string $search = '';

    public int $perPage = 20;

    /**
     * Get paginated star systems with search filter.
     */
    #[Computed]
    public function systems(): LengthAwarePaginator
    {
        $query = StarSystem::with(['planets'])
            ->where('discovered', true)
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.codex-star-systems', [
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
                'total_articles' => \App\Models\CodexEntry::public()->discovered()->count(),
                'planets' => \App\Models\Planet::whereHas('starSystem', function ($q) {
                    $q->where('discovered', true);
                })->count(),
                'star_systems' => StarSystem::where('discovered', true)->count(),
                'named' => \App\Models\CodexEntry::public()->discovered()->named()->count(),
                'contributors' => \App\Models\CodexContribution::distinct('contributor_user_id')->count('contributor_user_id'),
                'contributions' => \App\Models\CodexContribution::count(),
            ];
        });
    }
}

