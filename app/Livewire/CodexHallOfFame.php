<?php

namespace App\Livewire;

use App\Models\CodexContribution;
use App\Models\CodexEntry;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CodexHallOfFame extends Component
{
    /**
     * Get top discoverers.
     */
    #[Computed]
    public function topDiscoverers()
    {
        return User::query()
            ->withCount('discoveredPlanets')
            ->having('discovered_planets_count', '>', 0)
            ->orderBy('discovered_planets_count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get top contributors.
     */
    #[Computed]
    public function topContributors()
    {
        return User::query()
            ->withCount([
                'codexContributions as approved_contributions_count' => function ($q) {
                    $q->where('status', 'approved');
                },
            ])
            ->having('approved_contributions_count', '>', 0)
            ->orderBy('approved_contributions_count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get recently named planets.
     */
    #[Computed]
    public function recentlyNamedPlanets()
    {
        return CodexEntry::with(['planet.properties', 'discoveredBy'])
            ->public()
            ->named()
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
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
                'planets' => \App\Models\Planet::count(),
                'star_systems' => \App\Models\StarSystem::where('discovered', true)->count(),
                'named' => CodexEntry::public()->named()->count(),
                'contributors' => CodexContribution::distinct('contributor_user_id')->count('contributor_user_id'),
                'contributions' => CodexContribution::count(),
            ];
        });
    }

    public function render()
    {
        return view('livewire.codex-hall-of-fame', [
            'stats' => $this->stats,
        ]);
    }
}

