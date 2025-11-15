<x-container
    variant="standard"
    class="py-8"
>
    <div class="codex-scanlines font-mono">
        <!-- Layout with Sidebar -->
        <div class="flex gap-8">
            <!-- Main Content -->
            <main class="flex-1">
                <!-- Breadcrumb -->
                <x-codex-breadcrumb :items="[['label' => 'CODEX', 'url' => route('codex.index')], ['label' => 'HALL_OF_FAME']]" />

                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-space-primary text-glow-primary mb-2 text-4xl font-bold dark:text-white">Distinctions
                    </h1>
                    <p class="text-gray-400 dark:text-gray-400">
                        Tableau d'honneur des agents Stellar - Performances exceptionnelles
                    </p>
                </div>

                <!-- Top Discoverers -->
                <div class="mb-8">
                    <h2 class="text-space-primary text-glow-subtle mb-4 text-2xl font-semibold dark:text-white">
                        Agents - Catalogage
                    </h2>
                    <div class="border-border-dark bg-surface-dark terminal-border-simple rounded-lg border">
                        <div class="divide-border-dark divide-y">
                            @foreach ($this->topDiscoverers as $index => $discoverer)
                                <div
                                    class="hover:bg-surface-medium flex items-center justify-between p-4 transition-colors">
                                    <div class="flex items-center">
                                        <div
                                            class="bg-space-primary text-space-black mr-4 flex h-10 w-10 items-center justify-center rounded-full font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <a
                                                href="{{ route('codex.contributor', $discoverer->id) }}"
                                                class="hover:text-space-primary font-semibold text-white transition-colors"
                                            >
                                                {{ $discoverer->name }}
                                            </a>
                                            <p class="text-sm text-gray-400">
                                                {{ $discoverer->matricule }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-space-primary text-lg font-bold">
                                            {{ $discoverer->discovered_planets_count }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            entrée{{ $discoverer->discovered_planets_count > 1 ? 's' : '' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Top Contributors -->
                <div class="mb-8">
                    <h2 class="text-space-secondary text-glow-subtle mb-4 text-2xl font-semibold dark:text-white">
                        Agents - Documentation
                    </h2>
                    <div class="border-border-dark bg-surface-dark terminal-border-simple rounded-lg border">
                        <div class="divide-border-dark divide-y">
                            @foreach ($this->topContributors as $index => $contributor)
                                <div
                                    class="hover:bg-surface-medium flex items-center justify-between p-4 transition-colors">
                                    <div class="flex items-center">
                                        <div
                                            class="bg-space-secondary text-space-black mr-4 flex h-10 w-10 items-center justify-center rounded-full font-bold">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <a
                                                href="{{ route('codex.contributor', $contributor->id) }}"
                                                class="hover:text-space-secondary font-semibold text-white transition-colors"
                                            >
                                                {{ $contributor->name }}
                                            </a>
                                            <p class="text-sm text-gray-400">
                                                {{ $contributor->matricule }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-space-secondary text-lg font-bold">
                                            {{ $contributor->approved_contributions_count }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            rapport{{ $contributor->approved_contributions_count > 1 ? 's' : '' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Recently Named Planets -->
                @if ($this->recentlyNamedPlanets->isNotEmpty())
                    <div class="mb-8">
                        <h2 class="text-space-primary text-glow-subtle mb-4 text-2xl font-semibold dark:text-white">
                            Planètes récemment classifiées
                        </h2>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($this->recentlyNamedPlanets as $entry)
                                <a
                                    href="{{ route('codex.planet', $entry->id) }}"
                                    class="planet-card-scanlines border-border-dark bg-surface-dark hover:border-space-primary hover:glow-primary group block overflow-hidden rounded-lg border transition-all hover:scale-[1.02]"
                                >
                                    @if ($entry->planet && $entry->planet->image_url)
                                        <img
                                            src="{{ $entry->planet->image_url }}"
                                            alt="{{ $entry->display_name }}"
                                            class="h-48 w-full object-cover opacity-90 transition-opacity group-hover:opacity-100"
                                        />
                                    @else
                                        <div class="bg-surface-medium flex h-48 w-full items-center justify-center">
                                            <svg
                                                class="h-12 w-12 text-gray-500"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3
                                            class="group-hover:text-space-primary mb-2 text-lg font-semibold text-white transition-colors">
                                            {{ $entry->name }}
                                        </h3>
                                        @if ($entry->discoveredBy)
                                            <p class="text-sm text-gray-400">
                                                Agent {{ $entry->discoveredBy->name }}
                                            </p>
                                        @endif
                                        <p class="mt-1 text-xs text-gray-500">
                                            Classifiée {{ $entry->updated_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </main>

            <!-- Sidebar -->
            <x-codex-sidebar :stats="$stats" />
        </div>
    </div>
</x-container>
