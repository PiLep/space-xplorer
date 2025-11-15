<x-container variant="standard" class="py-8">
    @if ($loading)
        <x-loading-spinner message="[LOADING] Loading star system data..." />
    @elseif ($error)
        <x-alert type="error" :message="$error" />
    @elseif ($system)
        <div class="font-mono codex-scanlines">
            <!-- Breadcrumb -->
            <x-codex-breadcrumb :items="[
                ['label' => 'CODEX', 'url' => route('codex.index')],
                ['label' => 'SYSTEMES_STELLAIRES', 'url' => route('codex.star-systems')],
                ['label' => $system->name]
            ]" />
            
            <!-- Back Button -->
            <div class="mb-6">
                <a
                    href="{{ route('codex.star-systems') }}"
                    class="inline-flex items-center text-space-accent hover:text-space-accent-light transition-colors"
                >
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Retour à la cartographie
                </a>
            </div>

            <!-- Star System Header -->
            <div class="mb-8">
                <h1 class="mb-2 text-4xl font-bold text-space-accent text-glow-primary dark:text-white">
                    {{ $system->name }}
                </h1>
                <p class="text-gray-400 dark:text-gray-400">
                    Système stellaire cartographié le {{ $system->created_at->format('d/m/Y') }}
                </p>
            </div>

            <!-- Star System Information -->
            <div class="mb-8 grid gap-6 md:grid-cols-2">
                <!-- Star Type -->
                <div class="rounded-lg border border-border-dark bg-surface-dark p-6 terminal-border-simple">
                    <h2 class="mb-4 text-2xl font-semibold text-space-accent text-glow-subtle dark:text-white">Spécifications de l'étoile</h2>
                    <div class="space-y-3">
                        <div>
                            <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Type d'étoile</div>
                            <div class="text-lg font-semibold text-space-accent">
                                {{ ucfirst(str_replace('_', ' ', $system->star_type ?? 'Inconnu')) }}
                            </div>
                        </div>
                        <div>
                            <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Nombre de planètes</div>
                            <div class="text-lg font-semibold text-white">
                                {{ $system->planet_count }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coordinates -->
                <div class="rounded-lg border border-border-dark bg-surface-dark p-6 terminal-border-simple">
                    <h2 class="mb-4 text-2xl font-semibold text-space-secondary text-glow-subtle dark:text-white">Coordonnées spatiales</h2>
                    <div class="space-y-3">
                        <div>
                            <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Position X</div>
                            <div class="text-lg font-semibold text-white font-mono">
                                {{ number_format($system->x, 2) }}
                            </div>
                        </div>
                        <div>
                            <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Position Y</div>
                            <div class="text-lg font-semibold text-white font-mono">
                                {{ number_format($system->y, 2) }}
                            </div>
                        </div>
                        <div>
                            <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Position Z</div>
                            <div class="text-lg font-semibold text-white font-mono">
                                {{ number_format($system->z, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Planets in System -->
            @if ($system->planets->isNotEmpty())
                <div class="mb-8">
                    <h2 class="mb-4 text-2xl font-semibold text-space-primary text-glow-subtle dark:text-white">
                        Planètes cataloguées dans le système ({{ $system->planets->count() }})
                    </h2>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($system->planets as $planet)
                            @php
                                $codexEntry = $planet->codexEntry;
                            @endphp
                            <a
                                href="{{ $codexEntry ? route('codex.planet', $codexEntry->id) : '#' }}"
                                class="group planet-card-scanlines block overflow-hidden rounded-lg border border-border-dark bg-surface-dark transition-all hover:border-space-primary hover:glow-primary hover:scale-[1.02]"
                            >
                                @if ($planet->image_url)
                                    <img
                                        src="{{ $planet->image_url }}"
                                        alt="{{ $codexEntry?->display_name ?? 'Planète' }}"
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
                                    <h3 class="mb-2 text-lg font-semibold text-white group-hover:text-space-primary transition-colors">
                                        {{ $codexEntry?->display_name ?? 'Planète non cataloguée' }}
                                    </h3>
                                    @if ($planet->properties)
                                        <div class="flex flex-wrap gap-2">
                                            <span class="rounded-full border border-border-dark bg-surface-medium px-2 py-1 text-xs text-gray-300">
                                                {{ ucfirst($planet->properties->type ?? 'Inconnu') }}
                                            </span>
                                            @if ($planet->properties->size)
                                                <span class="rounded-full border border-border-dark bg-surface-medium px-2 py-1 text-xs text-gray-300">
                                                    {{ ucfirst($planet->properties->size) }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Nearby Systems -->
            @if ($this->nearbySystems->isNotEmpty())
                <div class="mb-8">
                    <h2 class="mb-4 text-2xl font-semibold text-space-accent text-glow-subtle dark:text-white">Systèmes stellaires adjacents</h2>
                    <div class="rounded-lg border border-border-dark bg-surface-dark p-6 terminal-border-simple">
                        <ul class="space-y-2">
                            @foreach ($this->nearbySystems as $nearby)
                                <li>
                                    <a
                                        href="{{ route('codex.star-system', $nearby['id']) }}"
                                        class="flex items-center justify-between rounded px-3 py-2 transition-colors hover:bg-surface-medium hover:text-space-accent"
                                    >
                                        <span class="font-semibold text-white">{{ $nearby['name'] }}</span>
                                        <span class="text-sm text-gray-400 font-mono">
                                            {{ number_format($nearby['distance'], 2) }} unités
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    @endif
</x-container>

