<x-container variant="standard" class="py-8">
    @if ($loading)
        <x-loading-spinner message="[LOADING] Loading planet data..." />
    @elseif ($error)
        <x-alert type="error" :message="$error" />
    @elseif ($entry)
        <div class="font-mono codex-scanlines">
            <!-- Breadcrumb -->
            <x-codex-breadcrumb :items="[
                ['label' => 'CODEX', 'url' => route('codex.index')],
                ['label' => 'PLANETES', 'url' => route('codex.planets')],
                ['label' => $entry->display_name]
            ]" />
            
            <!-- Back Button -->
            <div class="mb-6">
                <a
                    href="{{ route('codex.index') }}"
                    class="inline-flex items-center text-space-secondary hover:text-space-secondary-light transition-colors"
                >
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Retour à l'index
                </a>
            </div>

            <!-- Planet Header -->
            <div class="mb-8">
                <h1 class="mb-2 text-4xl font-bold text-space-primary text-glow-primary dark:text-white">
                    {{ $entry->display_name }}
                </h1>
                @if ($entry->discoveredBy)
                    <p class="text-gray-400 dark:text-gray-400">
                        Enregistrée par l'agent <span class="font-semibold text-space-secondary">{{ $entry->discoveredBy->name }}</span>
                        le {{ $entry->created_at->format('d/m/Y') }}
                    </p>
                @endif
            </div>

            <!-- Planet Image/Video -->
            @if ($entry->planet && ($entry->planet->image_url || $entry->planet->video_url))
                <div class="mb-8">
                    @if ($entry->planet->video_url)
                        <video
                            src="{{ $entry->planet->video_url }}"
                            class="h-96 w-full rounded-lg object-cover"
                            autoplay
                            loop
                            muted
                            playsinline
                        ></video>
                    @elseif ($entry->planet->image_url)
                        <img
                            src="{{ $entry->planet->image_url }}"
                            alt="{{ $entry->display_name }}"
                            class="h-96 w-full rounded-lg object-cover"
                        />
                    @endif
                </div>
            @endif

            <!-- Planet Characteristics -->
            @if ($entry->planet && $entry->planet->properties)
                <div class="mb-8 rounded-lg border border-border-dark bg-surface-dark p-6 terminal-border-simple">
                    <h2 class="mb-4 text-2xl font-semibold text-space-primary text-glow-subtle dark:text-white">Caractéristiques</h2>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-lg border border-border-dark bg-surface-medium p-4">
                            <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Type</div>
                            <div class="text-lg font-semibold text-space-primary">
                                {{ ucfirst($entry->planet->properties->type ?? 'Inconnu') }}
                            </div>
                        </div>
                        <div class="rounded-lg border border-border-dark bg-surface-medium p-4">
                            <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Taille</div>
                            <div class="text-lg font-semibold text-white">
                                {{ ucfirst($entry->planet->properties->size ?? 'Inconnue') }}
                            </div>
                        </div>
                        <div class="rounded-lg border border-border-dark bg-surface-medium p-4">
                            <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Température</div>
                            <div class="text-lg font-semibold text-white">
                                @php
                                    $temp = $entry->planet->properties->temperature ?? 'Unknown';
                                    $tempMap = ['temperate' => 'Temperate', 'cold' => 'Cold', 'hot' => 'Hot'];
                                    echo $tempMap[$temp] ?? ucfirst($temp);
                                @endphp
                            </div>
                        </div>
                        <div class="rounded-lg border border-border-dark bg-surface-medium p-4">
                            <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Atmosphère</div>
                            <div class="text-lg font-semibold text-white">
                                @php
                                    $atmo = $entry->planet->properties->atmosphere ?? 'Unknown';
                                    $atmoMap = ['breathable' => 'Breathable', 'toxic' => 'Toxic', 'nonexistent' => 'Nonexistent'];
                                    echo $atmoMap[$atmo] ?? ucfirst($atmo);
                                @endphp
                            </div>
                        </div>
                        <div class="rounded-lg border border-border-dark bg-surface-medium p-4">
                            <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Terrain</div>
                            <div class="text-lg font-semibold text-white">
                                @php
                                    $terrain = $entry->planet->properties->terrain ?? 'Unknown';
                                    $terrainMap = ['rocky' => 'Rocky', 'oceanic' => 'Oceanic', 'desert' => 'Desert', 'forested' => 'Forested', 'urban' => 'Urban', 'mixed' => 'Mixed', 'icy' => 'Icy'];
                                    echo $terrainMap[$terrain] ?? ucfirst($terrain);
                                @endphp
                            </div>
                        </div>
                        <div class="rounded-lg border border-border-dark bg-surface-medium p-4">
                            <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Ressources</div>
                            <div class="text-lg font-semibold text-white">
                                @php
                                    $resources = $entry->planet->properties->resources ?? 'Unknown';
                                    $resourcesMap = ['abundant' => 'Abundant', 'moderate' => 'Moderate', 'rare' => 'Rare'];
                                    echo $resourcesMap[$resources] ?? ucfirst($resources);
                                @endphp
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Description -->
            @if ($entry->description)
                <div class="mb-8 rounded-lg border border-border-dark bg-surface-dark p-6 terminal-border-simple">
                    <h2 class="mb-4 text-2xl font-semibold text-space-primary text-glow-subtle dark:text-white">Description</h2>
                    <p class="text-gray-300 leading-relaxed">{{ $entry->description }}</p>
                </div>
            @endif

            <!-- Actions -->
            @auth
                <div class="flex gap-4">
                    @if ($this->canUserName() && !$entry->is_named)
                        <button
                            wire:click="openNameModal"
                            class="rounded-lg bg-space-primary px-6 py-3 font-semibold text-space-black hover:bg-space-primary-dark transition-colors glow-primary"
                        >
                            Nommer cette planète
                        </button>
                    @endif

                    @if ($this->canUserContribute())
                        <button
                            wire:click="openContributeModal"
                            class="rounded-lg border border-space-secondary px-6 py-3 font-semibold text-space-secondary hover:bg-space-secondary hover:text-space-black transition-colors"
                        >
                            Contribuer
                        </button>
                    @endif
                </div>
            @endauth

            <!-- Modals -->
            @if ($showNameModal)
                <div
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70"
                    wire:click="closeNameModal"
                >
                    <div
                        class="w-full max-w-md rounded-lg border border-border-dark bg-surface-dark p-6 shadow-xl terminal-border-simple"
                        wire:click.stop
                    >
                        <livewire:name-planet :entry="$entry" />
                    </div>
                </div>
            @endif

            @if ($showContributeModal)
                <div
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70"
                    wire:click="closeContributeModal"
                >
                    <div
                        class="w-full max-w-md rounded-lg border border-border-dark bg-surface-dark p-6 shadow-xl terminal-border-simple"
                        wire:click.stop
                    >
                        <livewire:contribute-to-codex :entry="$entry" />
                    </div>
                </div>
            @endif
        </div>
    @endif
</x-container>

