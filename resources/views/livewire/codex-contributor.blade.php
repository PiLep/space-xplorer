<x-container variant="standard" class="py-8">
    @if ($loading)
        <x-loading-spinner message="[LOADING] Loading contributor data..." />
    @elseif ($error)
        <x-alert type="error" :message="$error" />
    @elseif ($contributor)
        <div class="font-mono codex-scanlines">
            <!-- Breadcrumb -->
            <x-codex-breadcrumb :items="[
                ['label' => 'CODEX', 'url' => route('codex.index')],
                ['label' => 'CONTRIBUTEURS', 'url' => route('codex.contributors')],
                ['label' => $contributor->name]
            ]" />
            
            <!-- Back Button -->
            <div class="mb-6">
                <a
                    href="{{ route('codex.contributors') }}"
                    class="inline-flex items-center text-space-secondary hover:text-space-secondary-light transition-colors"
                >
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Retour au personnel
                </a>
            </div>

            <!-- Contributor Header -->
            <div class="mb-8">
                <div class="mb-4 flex items-center">
                    @if ($contributor->avatar_url)
                        <img
                            src="{{ $contributor->avatar_url }}"
                            alt="{{ $contributor->name }}"
                            class="mr-6 h-24 w-24 rounded-full border-2 border-border-dark"
                        />
                    @else
                        <div class="mr-6 flex h-24 w-24 items-center justify-center rounded-full border-2 border-border-dark bg-surface-medium">
                            <span class="text-3xl font-semibold text-space-secondary">
                                {{ strtoupper(substr($contributor->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <div>
                        <h1 class="mb-2 text-4xl font-bold text-space-secondary text-glow-primary dark:text-white">
                            {{ $contributor->name }}
                        </h1>
                        <p class="text-gray-400">
                            Matricule: {{ $contributor->matricule }}
                        </p>
                        @if ($contributor->created_at)
                            <p class="text-sm text-gray-500">
                                Agent Stellar depuis {{ $contributor->created_at->format('d/m/Y') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="mb-8 grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-border-dark bg-surface-dark p-6 terminal-border-simple">
                    <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Rapports approuvés</div>
                    <div class="mt-1 text-3xl font-bold text-space-secondary text-glow-secondary">
                        {{ $this->approvedContributions->count() }}
                    </div>
                </div>
                <div class="rounded-lg border border-border-dark bg-surface-dark p-6 terminal-border-simple">
                    <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Planètes cataloguées</div>
                    <div class="mt-1 text-3xl font-bold text-space-primary text-glow-primary">
                        {{ $this->discoveredPlanets->count() }}
                    </div>
                </div>
                <div class="rounded-lg border border-border-dark bg-surface-dark p-6 terminal-border-simple">
                    <div class="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">Total rapports</div>
                    <div class="mt-1 text-3xl font-bold text-white">
                        {{ $contributor->codexContributions->count() }}
                    </div>
                </div>
            </div>

            <!-- Discovered Planets -->
            @if ($this->discoveredPlanets->isNotEmpty())
                <div class="mb-8">
                    <h2 class="mb-4 text-2xl font-semibold text-space-primary text-glow-subtle dark:text-white">
                        Planètes cataloguées ({{ $this->discoveredPlanets->count() }})
                    </h2>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($this->discoveredPlanets as $entry)
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
                                    <h3 class="mb-2 text-lg font-semibold text-white group-hover:text-space-primary transition-colors">
                                        {{ $entry->display_name }}
                                    </h3>
                                    <p class="text-xs text-gray-500">
                                        Enregistrée le {{ $entry->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Approved Contributions -->
            @if ($this->approvedContributions->isNotEmpty())
                <div class="mb-8">
                    <h2 class="mb-4 text-2xl font-semibold text-space-secondary text-glow-subtle dark:text-white">
                        Rapports approuvés ({{ $this->approvedContributions->count() }})
                    </h2>
                    <div class="space-y-4">
                        @foreach ($this->approvedContributions as $contribution)
                            <div class="rounded-lg border border-border-dark bg-surface-dark p-6 terminal-border-simple">
                                <div class="mb-2 flex items-center justify-between">
                                    <a
                                        href="{{ route('codex.planet', $contribution->codexEntry->id) }}"
                                        class="text-lg font-semibold text-white hover:text-space-secondary transition-colors"
                                    >
                                        {{ $contribution->codexEntry->display_name }}
                                    </a>
                                    <span class="text-xs text-gray-500">
                                        {{ $contribution->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                                <p class="text-gray-300">
                                    {{ \Illuminate\Support\Str::limit($contribution->content, 200) }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</x-container>

