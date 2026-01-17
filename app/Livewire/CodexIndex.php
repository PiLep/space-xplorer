<?php

namespace App\Livewire;

use App\Models\CodexContribution;
use App\Models\CodexEntry;
use App\Models\Planet;
use App\Models\StarSystem;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CodexIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showSearchResults = false;

    public array $searchResults = [];

    public function mount(): void
    {
        // Component initialized
    }

    /**
     * Perform search with autocompletion.
     */
    public function performSearch(): void
    {
        if (empty($this->search)) {
            $this->showSearchResults = false;
            $this->searchResults = [];

            return;
        }

        $this->searchResults = CodexEntry::public()
            ->discovered()
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('fallback_name', 'like', '%'.$this->search.'%');
            })
            ->limit(10)
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'name' => $entry->display_name,
                ];
            })
            ->toArray();

        $this->showSearchResults = true;
    }

    /**
     * Clear search.
     */
    public function clearSearch(): void
    {
        $this->search = '';
        $this->showSearchResults = false;
        $this->searchResults = [];
        $this->resetPage();
    }

    /**
     * Select a search result and redirect.
     */
    public function selectResult(string $entryId): void
    {
        $this->redirect(route('codex.planet', $entryId));
    }

    /**
     * Get paginated codex entries.
     */
    #[Computed]
    public function entries()
    {
        $query = CodexEntry::with(['planet.properties', 'discoveredBy'])
            ->public()
            ->discovered();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('fallback_name', 'like', '%'.$this->search.'%');
            });
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate(20);
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
                'planets' => Planet::whereHas('starSystem', function ($q) {
                    $q->where('discovered', true);
                })->count(),
                'star_systems' => StarSystem::where('discovered', true)->count(),
                'named' => CodexEntry::public()->discovered()->named()->count(),
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
                ->discovered()
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
            'entries' => $this->entries,
        ]);
    }
}

