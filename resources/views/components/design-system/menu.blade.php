@props(['currentPage' => 'overview'])

<nav class="mb-8 bg-surface-dark dark:bg-surface-dark rounded-lg p-4 border border-border-dark dark:border-border-dark terminal-border-simple">
    <div class="flex flex-wrap items-center gap-2 sm:gap-4 font-mono text-sm">
        <a href="{{ route('design-system.overview') }}" 
           class="px-3 py-2 rounded transition-colors {{ $currentPage === 'overview' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-400 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }}">
            > OVERVIEW
        </a>
        <span class="text-gray-500 dark:text-gray-500 hidden sm:inline">|</span>
        <a href="{{ route('design-system.colors') }}" 
           class="px-3 py-2 rounded transition-colors {{ $currentPage === 'colors' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-400 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }}">
            > COLORS
        </a>
        <span class="text-gray-500 dark:text-gray-500 hidden sm:inline">|</span>
        <a href="{{ route('design-system.typography') }}" 
           class="px-3 py-2 rounded transition-colors {{ $currentPage === 'typography' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-400 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }}">
            > TYPOGRAPHY
        </a>
        <span class="text-gray-500 dark:text-gray-500 hidden sm:inline">|</span>
        <a href="{{ route('design-system.spacing') }}" 
           class="px-3 py-2 rounded transition-colors {{ $currentPage === 'spacing' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-400 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }}">
            > SPACING
        </a>
        <span class="text-gray-500 dark:text-gray-500 hidden sm:inline">|</span>
        <a href="{{ route('design-system.components') }}" 
           class="px-3 py-2 rounded transition-colors {{ $currentPage === 'components' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-400 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }}">
            > COMPONENTS
        </a>
        <span class="text-gray-500 dark:text-gray-500 hidden sm:inline">|</span>
        <a href="{{ route('design-system.effects') }}" 
           class="px-3 py-2 rounded transition-colors {{ $currentPage === 'effects' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-400 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }}">
            > VISUAL_EFFECTS
        </a>
    </div>
</nav>

