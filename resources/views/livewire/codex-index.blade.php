<x-container variant="standard" class="py-8">
    <div class="font-mono codex-scanlines">
        <!-- Layout with Sidebar -->
        <div class="flex gap-8">
            <!-- Main Content -->
            <main class="flex-1">
                <!-- Breadcrumb -->
                <x-codex-breadcrumb :items="[
                    ['label' => 'CODEX', 'url' => route('codex.index')]
                ]" />
                
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="mb-2 text-4xl font-bold text-space-primary text-glow-primary dark:text-white">Codex Stellaris</h1>
                    <p class="text-gray-400 dark:text-gray-400">
                        encyclopédie collaborative
                    </p>
                </div>

                <!-- Statistics Cards -->
                <div class="mb-8 grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div class="rounded-lg border border-border-dark bg-surface-dark p-4 terminal-border-simple transition-all hover:glow-primary">
                        <div class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-400">Articles</div>
                        <div class="mt-1 text-2xl font-bold text-space-primary text-glow-primary">{{ $stats['total_articles'] ?? 0 }}</div>
                    </div>
                    <div class="rounded-lg border border-border-dark bg-surface-dark p-4 terminal-border-simple transition-all hover:glow-primary">
                        <div class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-400">Planètes nommées</div>
                        <div class="mt-1 text-2xl font-bold text-space-primary text-glow-primary">{{ $stats['named'] ?? 0 }}</div>
                    </div>
                    <div class="rounded-lg border border-border-dark bg-surface-dark p-4 terminal-border-simple transition-all hover:glow-primary">
                        <div class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-400">Contributeurs</div>
                        <div class="mt-1 text-2xl font-bold text-space-secondary text-glow-secondary">{{ $stats['contributors'] ?? 0 }}</div>
                    </div>
                    <div class="rounded-lg border border-border-dark bg-surface-dark p-4 terminal-border-simple transition-all hover:glow-primary">
                        <div class="text-xs uppercase tracking-wide text-gray-400 dark:text-gray-400">Contributions</div>
                        <div class="mt-1 text-2xl font-bold text-space-secondary text-glow-secondary">{{ $stats['contributions'] ?? 0 }}</div>
                    </div>
                </div>

                <!-- Recent Discoveries -->
                @if ($recentDiscoveries->isNotEmpty() && empty($search))
                    <div class="mb-8">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-2xl font-semibold text-space-primary text-glow-subtle dark:text-white">Découvertes récentes</h2>
                            <a href="{{ route('codex.planets') }}" class="text-sm text-space-secondary hover:text-space-secondary-light transition-colors">
                                Accéder au catalogue complet →
                            </a>
                        </div>
                        <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3">
                            @foreach ($recentDiscoveries as $recent)
                                <a
                                    href="{{ route('codex.planet', $recent->id) }}"
                                    class="group planet-card-scanlines block overflow-hidden rounded-lg border border-border-dark bg-surface-dark transition-all hover:border-space-primary hover:glow-primary hover:scale-[1.02]"
                                >
                                    @if ($recent->planet && $recent->planet->image_url)
                                        <div class="relative h-48 w-full overflow-hidden">
                                            <img
                                                src="{{ $recent->planet->image_url }}"
                                                alt="{{ $recent->display_name }}"
                                                class="h-full w-full object-cover opacity-90 transition-opacity group-hover:opacity-100"
                                            />
                                            @if ($recent->is_named)
                                                <div class="absolute top-2 right-2">
                                                    <span class="rounded-full border border-space-primary bg-space-primary px-2 py-1 text-xs font-semibold text-space-black">
                                                        Nommée
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="flex h-48 w-full items-center justify-center bg-surface-medium">
                                            <svg class="h-10 w-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="mb-2 text-base font-semibold text-white group-hover:text-space-primary transition-colors">
                                            {{ \Illuminate\Support\Str::limit($recent->display_name, 25) }}
                                        </h3>
                                        @if ($recent->planet && $recent->planet->properties)
                                            <div class="mb-2 flex flex-wrap gap-2">
                                                <span class="rounded-full border border-border-dark bg-surface-medium px-2 py-1 text-xs text-gray-300">
                                                    {{ ucfirst($recent->planet->properties->type ?? 'Inconnu') }}
                                                </span>
                                            </div>
                                        @endif
                                        @if ($recent->discoveredBy)
                                            <p class="mb-1 text-sm text-gray-400">
                                                Agent {{ \Illuminate\Support\Str::limit($recent->discoveredBy->name, 20) }}
                                            </p>
                                        @endif
                                        <p class="text-xs text-gray-500">
                                            {{ $recent->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Search -->
                <div class="mb-8">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            wire:keyup="performSearch"
                            placeholder="Rechercher une planète..."
                            class="w-full rounded-lg border border-border-dark bg-surface-dark px-4 py-3 pl-10 text-white placeholder-gray-400 focus:border-space-primary focus:outline-none focus:ring-2 focus:ring-space-primary"
                        />
                        @if (!empty($search))
                            <button
                                wire:click="clearSearch"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                            >
                                ✕
                            </button>
                        @endif
                    </div>
                    @if ($showSearchResults && !empty($searchResults))
                        <div class="mt-2 rounded-lg border border-border-dark bg-surface-dark p-2">
                            @foreach ($searchResults as $result)
                                <button
                                    wire:click="selectResult('{{ $result['id'] }}')"
                                    class="block w-full px-4 py-2 text-left text-white hover:bg-surface-medium transition-colors"
                                >
                                    {{ $result['name'] }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                    @if (!empty($search))
                        <p class="mt-2 text-sm text-gray-400">
                            {{ $entries->total() }} résultat{{ $entries->total() > 1 ? 's' : '' }}
                        </p>
                    @endif
                </div>

                <!-- All Planets -->
                @if ($entries->isNotEmpty())
                    <div class="mb-8">
                        <h2 class="mb-4 text-2xl font-semibold text-space-primary text-glow-subtle dark:text-white">Toutes les planètes</h2>
                        <div class="space-y-4">
                            @foreach ($entries as $entry)
                                <a
                                    href="{{ route('codex.planet', $entry->id) }}"
                                    class="group block rounded-lg border border-border-dark bg-surface-dark p-4 transition-all hover:border-space-primary hover:glow-primary"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <h3 class="text-lg font-semibold text-white group-hover:text-space-primary transition-colors">
                                                    {{ $entry->display_name }}
                                                </h3>
                                                @if ($entry->is_named)
                                                    <span class="rounded-full border border-space-primary bg-space-primary px-2 py-1 text-xs font-semibold text-space-black">
                                                        Nommée
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($entry->planet && $entry->planet->properties)
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    <span class="rounded-full border border-border-dark bg-surface-medium px-2 py-1 text-xs text-gray-300">
                                                        {{ ucfirst($entry->planet->properties->type ?? 'Inconnu') }}
                                                    </span>
                                                    <span class="rounded-full border border-border-dark bg-surface-medium px-2 py-1 text-xs text-gray-300">
                                                        {{ ucfirst($entry->planet->properties->size ?? 'Inconnue') }}
                                                    </span>
                                                </div>
                                            @endif
                                            @if ($entry->discoveredBy)
                                                <p class="mt-2 text-sm text-gray-400">
                                                    Agent {{ $entry->discoveredBy->name }} - {{ $entry->created_at->format('d/m/Y') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $entries->links() }}
                        </div>
                    </div>
                @endif

                <!-- Quick Access -->
                <div class="mb-8">
                    <h2 class="mb-4 text-2xl font-semibold text-space-primary text-glow-subtle dark:text-white">Modules d'accès</h2>
                    <div class="grid gap-4 md:grid-cols-3">
                        <a
                            href="{{ route('codex.planets') }}"
                            class="group rounded-lg border border-border-dark bg-surface-dark p-6 terminal-border-simple transition-all hover:border-space-primary hover:glow-primary"
                        >
                            <div class="mb-2 flex items-center">
                                <svg class="mr-3 h-6 w-6 text-space-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-white group-hover:text-space-primary transition-colors">Catalogue planètes</h3>
                            </div>
                            <p class="text-sm text-gray-400">
                                Accéder à l'ensemble des planètes cataloguées par Stellar
                            </p>
                        </a>
                        <a
                            href="{{ route('codex.star-systems') }}"
                            class="group rounded-lg border border-border-dark bg-surface-dark p-6 terminal-border-simple transition-all hover:border-space-accent hover:glow-primary"
                        >
                            <div class="mb-2 flex items-center">
                                <svg class="mr-3 h-6 w-6 text-space-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-white group-hover:text-space-accent transition-colors">Cartographie stellaire</h3>
                            </div>
                            <p class="text-sm text-gray-400">
                                Consulter les systèmes stellaires cartographiés par nos équipes
                            </p>
                        </a>
                        <a
                            href="{{ route('codex.hall-of-fame') }}"
                            class="group rounded-lg border border-border-dark bg-surface-dark p-6 terminal-border-simple transition-all hover:border-space-secondary hover:glow-primary"
                        >
                            <div class="mb-2 flex items-center">
                                <svg class="mr-3 h-6 w-6 text-space-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-white group-hover:text-space-secondary transition-colors">Distinctions</h3>
                            </div>
                            <p class="text-sm text-gray-400">
                                Consulter les performances exceptionnelles du personnel Stellar
                            </p>
                        </a>
                    </div>
                </div>
            </main>

            <!-- Sidebar -->
            <x-codex-sidebar :stats="$stats" />
        </div>
    </div>
</x-container>
