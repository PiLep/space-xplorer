<?php

namespace App\Livewire;

use App\Models\CodexContribution;
use App\Models\CodexEntry;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CodexContributors extends Component
{
    public string $search = '';

    public int $perPage = 20;

    /**
     * Get paginated contributors with search filter.
     */
    #[Computed]
    public function contributors(): LengthAwarePaginator
    {
        $query = User::query()
            ->whereHas('codexContributions')
            ->withCount([
                'codexContributions as approved_contributions_count' => function ($q) {
                    $q->where('status', 'approved');
                },
                'codexContributions as total_contributions_count',
            ])
            ->withCount('discoveredPlanets as discovered_planets_count')
            ->orderBy('approved_contributions_count', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.codex-contributors', [
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
                'total_articles' => CodexEntry::public()->discovered()->count(),
                'planets' => \App\Models\Planet::whereHas('starSystem', function ($q) {
                    $q->where('discovered', true);
                })->count(),
                'star_systems' => \App\Models\StarSystem::where('discovered', true)->count(),
                'named' => CodexEntry::public()->discovered()->named()->count(),
                'contributors' => CodexContribution::distinct('contributor_user_id')->count('contributor_user_id'),
                'contributions' => CodexContribution::count(),
            ];
        });
    }
}

