<x-container variant="standard" class="py-8">
    <div class="font-mono codex-scanlines">
        <!-- Layout with Sidebar -->
        <div class="flex gap-8">
            <!-- Main Content -->
            <main class="flex-1">
                <!-- Breadcrumb -->
                <x-codex-breadcrumb :items="[
                    ['label' => 'CODEX', 'url' => route('codex.index')],
                    ['label' => 'PLANETES']
                ]" />
                
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="mb-2 text-4xl font-bold text-space-primary text-glow-primary dark:text-white">Catalogue Planètes</h1>
                    <p class="text-gray-400 dark:text-gray-400">
                        Base de données complète des planètes cataloguées par Stellar
                    </p>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-lg border border-space-primary opacity-20 blur-sm transition-opacity focus-within:opacity-40"></div>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="[SEARCH] Rechercher dans le catalogue..."
                            class="relative w-full rounded-lg border border-border-dark bg-surface-dark px-4 py-3 pl-10 font-mono text-white placeholder-gray-500 shadow-sm transition-all focus:border-space-primary focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-opacity-50 focus:glow-border-primary"
                        />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-space-primary text-glow-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        @if($search)
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <button
                                    wire:click="$set('search', '')"
                                    class="text-gray-500 hover:text-space-primary transition-colors"
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

                <!-- Planets List -->
                <div class="mb-6">
                    <h2 class="mb-4 text-2xl font-semibold text-space-primary text-glow-subtle dark:text-white">
                        Planètes cataloguées
                        @if ($search)
                            <span class="text-lg font-normal text-gray-400">
                                ({{ $this->entries->total() }} entrée{{ $this->entries->total() > 1 ? 's' : '' }})
                            </span>
                        @endif
                    </h2>
                </div>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($this->entries as $entry)
                        <a
                            href="{{ route('codex.planet', $entry->id) }}"
                            class="group planet-card-scanlines block overflow-hidden rounded-lg border border-border-dark bg-surface-dark transition-all hover:border-space-primary hover:glow-primary hover:scale-[1.02]"
                        >
                            @if ($entry->planet && $entry->planet->image_url)
                                <img
                                    src="{{ $entry->planet->image_url }}"
                                    alt="{{ $entry->display_name }}"
                                    class="h-48 w-full object-cover opacity-90 transition-opacity group-hover:opacity-100"
                                />
                            @else
                                <div class="flex h-48 w-full items-center justify-center bg-surface-medium">
                                    <svg class="h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="p-4">
                                <div class="mb-2 flex items-start justify-between">
                                    <h3 class="text-lg font-semibold text-white group-hover:text-space-primary transition-colors">
                                        {{ $entry->display_name }}
                                    </h3>
                                    @if ($entry->is_named)
                                        <span class="ml-2 rounded-full border border-space-primary bg-space-primary px-2 py-1 text-xs font-semibold text-space-black">
                                            Classifiée
                                        </span>
                                    @endif
                                </div>
                                @if ($entry->planet && $entry->planet->properties)
                                    <div class="mb-2 flex flex-wrap gap-2">
                                        <span class="rounded-full border border-border-dark bg-surface-medium px-2 py-1 text-xs text-gray-300">
                                            {{ ucfirst($entry->planet->properties->type ?? 'Inconnu') }}
                                        </span>
                                        @if ($entry->planet->properties->size)
                                            <span class="rounded-full border border-border-dark bg-surface-medium px-2 py-1 text-xs text-gray-300">
                                                {{ ucfirst($entry->planet->properties->size) }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                                @if ($entry->description)
                                    <p class="mb-2 line-clamp-2 text-sm text-gray-400">
                                        {{ \Illuminate\Support\Str::limit($entry->description, 100) }}
                                    </p>
                                @endif
                                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                    @if ($entry->discoveredBy)
                                        <span>Agent {{ $entry->discoveredBy->name }}</span>
                                    @endif
                                    <span>Enregistrée {{ $entry->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $this->entries->links() }}
                </div>
            </main>

            <!-- Sidebar -->
            <x-codex-sidebar :stats="$stats" />
        </div>
    </div>
</x-container>

