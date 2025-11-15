@props(['stats' => null])

<aside class="hidden lg:block w-64 flex-shrink-0">
    <div class="sticky top-4 space-y-6">
        <!-- Navigation Principale -->
        <nav class="rounded-lg border border-border-dark bg-surface-dark p-4 terminal-border-simple">
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">
                Navigation
            </h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('codex.index') }}" 
                       class="block rounded px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-surface-medium hover:text-space-primary {{ request()->routeIs('codex.index') ? 'bg-surface-medium text-space-primary font-semibold' : '' }}">
                        Tableau de bord
                    </a>
                </li>
                <li>
                    <a href="{{ route('codex.planets') }}" 
                       class="block rounded px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-surface-medium hover:text-space-primary {{ request()->routeIs('codex.planets') || request()->routeIs('codex.planet') ? 'bg-surface-medium text-space-primary font-semibold' : '' }}">
                        Catalogue Planètes
                    </a>
                </li>
                <li>
                    <a href="{{ route('codex.star-systems') }}" 
                       class="block rounded px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-surface-medium hover:text-space-primary {{ request()->routeIs('codex.star-systems*') ? 'bg-surface-medium text-space-primary font-semibold' : '' }}">
                        Cartographie Stellaire
                    </a>
                </li>
                <li>
                    <a href="{{ route('codex.contributors') }}" 
                       class="block rounded px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-surface-medium hover:text-space-primary {{ request()->routeIs('codex.contributors*') ? 'bg-surface-medium text-space-primary font-semibold' : '' }}">
                        Personnel
                    </a>
                </li>
                <li>
                    <a href="{{ route('codex.hall-of-fame') }}" 
                       class="block rounded px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-surface-medium hover:text-space-primary {{ request()->routeIs('codex.hall-of-fame') ? 'bg-surface-medium text-space-primary font-semibold' : '' }}">
                        Distinctions
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Statistiques Générales -->
        @if($stats)
        <div class="rounded-lg border border-border-dark bg-surface-dark p-4 terminal-border-simple">
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">
                Statistiques
            </h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-400">Entrées</dt>
                    <dd class="font-semibold text-space-primary">{{ $stats['total_articles'] ?? 0 }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Planètes</dt>
                    <dd class="font-semibold text-white">{{ $stats['planets'] ?? 0 }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Systèmes</dt>
                    <dd class="font-semibold text-white">{{ $stats['star_systems'] ?? 0 }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Agents</dt>
                    <dd class="font-semibold text-space-secondary">{{ $stats['contributors'] ?? 0 }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Rapports</dt>
                    <dd class="font-semibold text-space-secondary">{{ $stats['contributions'] ?? 0 }}</dd>
                </div>
            </dl>
        </div>
        @endif

        <!-- À Propos -->
        <div class="rounded-lg border border-border-dark bg-surface-dark p-4 terminal-border-simple">
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400">
                À Propos
            </h3>
            <p class="text-xs leading-relaxed text-gray-300">
                Le <strong class="text-space-primary">Codex Stellaris</strong> est la base de données corporative officielle de Stellar. Archives complètes des planètes, systèmes stellaires, personnel et opérations d'exploration.
            </p>
        </div>
    </div>
</aside>
