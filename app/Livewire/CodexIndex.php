<?php

namespace App\Livewire;

use App\Models\CodexContribution;
use App\Models\CodexEntry;
use App\Models\Planet;
use App\Models\StarSystem;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CodexIndex extends Component
{
    public function mount(): void
    {
        // Component initialized
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
                'star_systems' => StarSystem::count(),
                'named' => CodexEntry::public()->named()->count(),
                'contributors' => CodexContribution::distinct('contributor_user_id')->count('contributor_user_id'),
                'contributions' => CodexContribution::count(),
            ];
        });
    }

    /**
     * Get recently discovered planets (last 6).
     */
    #[Computed]
    public function recentDiscoveries()
    {
        return cache()->remember('codex.recent_discoveries', now()->addMinutes(2), function () {
            return CodexEntry::with(['planet.properties', 'discoveredBy'])
                ->public()
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        });
    }

    public function render()
    {
        return view('livewire.codex-index', [
            'stats' => $this->stats,
            'recentDiscoveries' => $this->recentDiscoveries,
        ]);
    }
}

