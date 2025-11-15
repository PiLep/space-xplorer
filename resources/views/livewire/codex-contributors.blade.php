<x-container variant="standard" class="py-8">
    <div class="font-mono codex-scanlines">
        <!-- Layout with Sidebar -->
        <div class="flex gap-8">
            <!-- Main Content -->
            <main class="flex-1">
                <!-- Breadcrumb -->
                <x-codex-breadcrumb :items="[
                    ['label' => 'CODEX', 'url' => route('codex.index')],
                    ['label' => 'CONTRIBUTEURS']
                ]" />
                
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="mb-2 text-4xl font-bold text-space-secondary text-glow-primary dark:text-white">Personnel</h1>
                    <p class="text-gray-400 dark:text-gray-400">
                        Base de données du personnel Stellar ayant contribué au Codex
                    </p>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-lg border border-space-secondary opacity-20 blur-sm transition-opacity focus-within:opacity-40"></div>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="[SEARCH] Rechercher un agent..."
                            class="relative w-full rounded-lg border border-border-dark bg-surface-dark px-4 py-3 pl-10 font-mono text-white placeholder-gray-500 shadow-sm transition-all focus:border-space-secondary focus:outline-none focus:ring-2 focus:ring-space-secondary focus:ring-opacity-50 focus:glow-border-primary"
                        />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-space-secondary text-glow-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        @if($search)
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <button
                                    wire:click="$set('search', '')"
                                    class="text-gray-500 hover:text-space-secondary transition-colors"
                                    aria-label="Effacer la recherche"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Contributors List -->
                <div class="mb-6">
                    <h2 class="mb-4 text-2xl font-semibold text-space-secondary text-glow-subtle dark:text-white">
                        Agents enregistrés
                        @if ($search)
                            <span class="text-lg font-normal text-gray-400">
                                ({{ $this->contributors->total() }} entrée{{ $this->contributors->total() > 1 ? 's' : '' }})
                            </span>
                        @endif
                    </h2>
                </div>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($this->contributors as $contributor)
                        <a
                            href="{{ route('codex.contributor', $contributor->id) }}"
                            class="group planet-card-scanlines block overflow-hidden rounded-lg border border-border-dark bg-surface-dark transition-all hover:border-space-secondary hover:glow-secondary hover:scale-[1.02]"
                        >
                            <div class="p-6">
                                <div class="mb-4 flex items-center">
                                    @if ($contributor->avatar_url)
                                        <img
                                            src="{{ $contributor->avatar_url }}"
                                            alt="{{ $contributor->name }}"
                                            class="mr-4 h-12 w-12 rounded-full border border-border-dark"
                                        />
                                    @else
                                        <div class="mr-4 flex h-12 w-12 items-center justify-center rounded-full border border-border-dark bg-surface-medium">
                                            <span class="text-lg font-semibold text-space-secondary">
                                                {{ strtoupper(substr($contributor->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-semibold text-white group-hover:text-space-secondary transition-colors">
                                            {{ $contributor->name }}
                                        </h3>
                                        <p class="text-sm text-gray-400">
                                            {{ $contributor->matricule }}
                                        </p>
                                    </div>
                                </div>
                                    <div class="space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-400">Rapports approuvés</span>
                                        <span class="font-semibold text-space-secondary">
                                            {{ $contributor->approved_contributions_count ?? 0 }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-400">Planètes cataloguées</span>
                                        <span class="font-semibold text-space-primary">
                                            {{ $contributor->discovered_planets_count ?? 0 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $this->contributors->links() }}
                </div>
            </main>

            <!-- Sidebar -->
            <x-codex-sidebar :stats="$stats" />
        </div>
    </div>
</x-container>

