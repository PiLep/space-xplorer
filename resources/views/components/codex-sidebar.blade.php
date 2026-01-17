@props(['stats' => null])

<aside class="hidden lg:block w-64 flex-shrink-0">
    <div class="sticky top-4 space-y-6">
        <!-- Navigation Principale -->
        <nav class="rounded-lg border border-border-dark bg-surface-dark p-4 terminal-border-simple">
            <h3 class="mb-3 font-mono text-xs font-semibold uppercase tracking-wide text-gray-400">
                [NAVIGATION]
            </h3>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('codex.index') }}"
                       class="font-mono block rounded px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-surface-medium hover:text-space-primary {{ request()->routeIs('codex.index') ? 'bg-surface-medium text-space-primary font-semibold' : '' }}">
                        [TERMINAL] Principal
                    </a>
                </li>
                <li>
                    <a href="{{ route('codex.planets') }}"
                       class="font-mono block rounded px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-surface-medium hover:text-space-primary {{ request()->routeIs('codex.planets') || request()->routeIs('codex.planet') ? 'bg-surface-medium text-space-primary font-semibold' : '' }}">
                        [ARCHIVES] Planètes
                    </a>
                </li>
                <li>
                    <a href="{{ route('codex.star-systems') }}"
                       class="font-mono block rounded px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-surface-medium hover:text-space-primary {{ request()->routeIs('codex.star-systems*') ? 'bg-surface-medium text-space-primary font-semibold' : '' }}">
                        [MAPS] Systèmes
                    </a>
                </li>
                <li>
                    <a href="{{ route('codex.contributors') }}"
                       class="font-mono block rounded px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-surface-medium hover:text-space-primary {{ request()->routeIs('codex.contributors*') ? 'bg-surface-medium text-space-primary font-semibold' : '' }}">
                        [PERSONNEL] Agents
                    </a>
                </li>
                <li>
                    <a href="{{ route('codex.hall-of-fame') }}"
                       class="font-mono block rounded px-3 py-2 text-sm text-gray-300 transition-colors hover:bg-surface-medium hover:text-space-primary {{ request()->routeIs('codex.hall-of-fame') ? 'bg-surface-medium text-space-primary font-semibold' : '' }}">
                        [HONORS] Distinctions
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Statistiques Générales -->
        @if($stats)
        <div class="rounded-lg border border-border-dark bg-surface-dark p-4 terminal-border-simple">
            <h3 class="mb-3 font-mono text-xs font-semibold uppercase tracking-wide text-gray-400">
                [STATISTICS]
            </h3>
            <dl class="space-y-2 font-mono text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-400">[ENTRIES]</dt>
                    <dd class="font-semibold text-space-primary">{{ $stats['total_articles'] ?? 0 }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">[PLANETS]</dt>
                    <dd class="font-semibold text-white">{{ $stats['planets'] ?? 0 }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">[SYSTEMS]</dt>
                    <dd class="font-semibold text-white">{{ $stats['star_systems'] ?? 0 }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">[AGENTS]</dt>
                    <dd class="font-semibold text-space-secondary">{{ $stats['contributors'] ?? 0 }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">[REPORTS]</dt>
                    <dd class="font-semibold text-space-secondary">{{ $stats['contributions'] ?? 0 }}</dd>
                </div>
            </dl>
        </div>
        @endif

        <!-- À Propos -->
        <div class="rounded-lg border border-border-dark bg-surface-dark p-4 terminal-border-simple">
            <h3 class="mb-3 font-mono text-xs font-semibold uppercase tracking-wide text-gray-400">
                [ABOUT]
            </h3>
            <p class="font-mono text-xs leading-relaxed text-gray-300">
                Le <strong class="text-space-primary">CODEX STELLARIS</strong> est la base de données corporative officielle de Stellar. Archives complètes des planètes, systèmes stellaires, personnel et opérations d'exploration.
            </p>
        </div>
    </div>
</aside>
