@props(['currentPage' => null])

@php
    // Déterminer automatiquement la page courante depuis la route
    if (!$currentPage) {
        $routeName = request()->route()->getName();
        $currentPage = str_replace('design-system.', '', $routeName);
        if ($currentPage === 'index') {
            $currentPage = 'overview';
        }
        // Gérer les sous-pages de composants
        if (str_starts_with($currentPage, 'components.')) {
            // Exception pour la page logo qui doit être détectée comme "logo"
            if ($currentPage === 'components.logo') {
                $currentPage = 'logo';
            } else {
                $currentPage = 'components';
            }
        }
    }
@endphp

<div class="mx-auto max-w-7xl py-8">
    <div class="flex flex-col gap-8 lg:flex-row">
        <!-- Sidebar Navigation -->
        <aside class="flex-shrink-0 lg:w-64">
            <nav
                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple sticky top-8 rounded-lg border border-gray-200 bg-white p-4">
                <div class="space-y-1 font-mono">
                    <a
                        href="{{ route('design-system.overview') }}"
                        class="{{ $currentPage === 'overview' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }} block rounded px-4 py-2 text-sm transition-colors"
                    >
                        > OVERVIEW
                    </a>
                    <a
                        href="{{ route('design-system.colors') }}"
                        class="{{ $currentPage === 'colors' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }} block rounded px-4 py-2 text-sm transition-colors"
                    >
                        > COLORS
                    </a>
                    <a
                        href="{{ route('design-system.typography') }}"
                        class="{{ $currentPage === 'typography' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }} block rounded px-4 py-2 text-sm transition-colors"
                    >
                        > TYPOGRAPHY
                    </a>
                    <a
                        href="{{ route('design-system.spacing') }}"
                        class="{{ $currentPage === 'spacing' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }} block rounded px-4 py-2 text-sm transition-colors"
                    >
                        > SPACING
                    </a>
                    <a
                        href="{{ route('design-system.components.logo') }}"
                        class="{{ $currentPage === 'logo' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }} block rounded px-4 py-2 text-sm transition-colors"
                    >
                        > BRANDING
                    </a>
                    <a
                        href="{{ route('design-system.components') }}"
                        class="{{ $currentPage === 'components' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }} block rounded px-4 py-2 text-sm transition-colors"
                    >
                        > COMPONENTS
                    </a>
                    <a
                        href="{{ route('design-system.effects') }}"
                        class="{{ $currentPage === 'effects' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }} block rounded px-4 py-2 text-sm transition-colors"
                    >
                        > VISUAL_EFFECTS
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>
    </div>
</div>
