<x-container variant="standard" class="py-8">
    <div class="font-mono codex-scanlines">
        <!-- Layout with Sidebar -->
        <div class="flex gap-8">
            <!-- Main Content -->
            <main class="flex-1">
                <!-- Breadcrumb -->
                <x-codex-breadcrumb :items="[
                    ['label' => 'CODEX', 'url' => route('codex.index')],
                    ['label' => 'SYSTEMES_STELLAIRES']
                ]" />
                
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="mb-2 text-4xl font-bold text-space-accent text-glow-primary dark:text-white">Cartographie Stellaire</h1>
                    <p class="text-gray-400 dark:text-gray-400">
                        Base de données des systèmes stellaires cartographiés par Stellar
                    </p>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-lg border border-space-accent opacity-20 blur-sm transition-opacity focus-within:opacity-40"></div>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="[SEARCH] Rechercher dans la cartographie..."
                            class="relative w-full rounded-lg border border-border-dark bg-surface-dark px-4 py-3 pl-10 font-mono text-white placeholder-gray-500 shadow-sm transition-all focus:border-space-accent focus:outline-none focus:ring-2 focus:ring-space-accent focus:ring-opacity-50 focus:glow-border-primary"
                        />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-space-accent text-glow-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        @if($search)
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <button
                                    wire:click="$set('search', '')"
                                    class="text-gray-500 hover:text-space-accent transition-colors"
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

                <!-- Star Systems List -->
                <div class="mb-6">
                    <h2 class="mb-4 text-2xl font-semibold text-space-accent text-glow-subtle dark:text-white">
                        Systèmes cartographiés
                        @if ($search)
                            <span class="text-lg font-normal text-gray-400">
                                ({{ $this->systems->total() }} entrée{{ $this->systems->total() > 1 ? 's' : '' }})
                            </span>
                        @endif
                    </h2>
                </div>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($this->systems as $system)
                        <a
                            href="{{ route('codex.star-system', $system->id) }}"
                            class="group planet-card-scanlines block overflow-hidden rounded-lg border border-border-dark bg-surface-dark transition-all hover:border-space-accent hover:glow-primary hover:scale-[1.02]"
                        >
                            <div class="p-6">
                                <div class="mb-2 flex items-start justify-between">
                                    <h3 class="text-xl font-semibold text-white group-hover:text-space-accent transition-colors">
                                        {{ $system->name }}
                                    </h3>
                                    <span class="ml-2 rounded-full border border-space-accent bg-space-accent px-2 py-1 text-xs font-semibold text-space-black">
                                        {{ $system->planet_count }} planète{{ $system->planet_count > 1 ? 's' : '' }}
                                    </span>
                                </div>
                                <div class="mb-4 space-y-2">
                                    <div class="flex items-center text-sm text-gray-400">
                                        <svg class="mr-2 h-4 w-4 text-space-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        {{ ucfirst(str_replace('_', ' ', $system->star_type ?? 'Inconnu')) }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-400">
                                        <svg class="mr-2 h-4 w-4 text-space-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        ({{ number_format($system->x, 2) }}, {{ number_format($system->y, 2) }}, {{ number_format($system->z, 2) }})
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    Cartographié le {{ $system->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $this->systems->links() }}
                </div>
            </main>

            <!-- Sidebar -->
            <x-codex-sidebar :stats="$stats" />
        </div>
    </div>
</x-container>

