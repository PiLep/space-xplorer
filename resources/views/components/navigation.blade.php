@props([
    'variant' => 'sidebar', // sidebar, top, terminal
    'currentPage' => null,
    'items' => [],
])

@php
    // Déterminer automatiquement la page courante depuis la route si non fourni
    if (!$currentPage && request()->route()) {
        $routeName = request()->route()->getName();
        $currentPage = $routeName;
    }
    
    $isActive = function($route) use ($currentPage) {
        if (!$currentPage) return false;
        return request()->routeIs($route) || $currentPage === $route;
    };
@endphp

@if($variant === 'sidebar')
    <aside class="lg:w-64 flex-shrink-0">
        <nav class="bg-surface-dark dark:bg-surface-dark rounded-lg p-4 border border-border-dark dark:border-border-dark terminal-border-simple sticky top-8">
            <div class="space-y-1 font-mono">
                @foreach($items as $item)
                    <a href="{{ $item['route'] ?? $item['url'] ?? '#' }}" 
                       class="block px-4 py-2 rounded text-sm transition-colors {{ $isActive($item['route'] ?? $item['url'] ?? '') ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }}"
                       @if($isActive($item['route'] ?? $item['url'] ?? '')) aria-current="page" @endif>
                        > {{ strtoupper($item['label'] ?? $item['text'] ?? '') }}
                    </a>
                @endforeach
            </div>
        </nav>
    </aside>
@elseif($variant === 'top')
    <nav class="mb-8 bg-surface-dark dark:bg-surface-dark rounded-lg p-4 border border-border-dark dark:border-border-dark terminal-border-simple">
        <div class="flex flex-wrap items-center gap-2 sm:gap-4 font-mono text-sm">
            @foreach($items as $index => $item)
                @if($index > 0)
                    <span class="text-gray-500 dark:text-gray-500 hidden sm:inline">|</span>
                @endif
                <a href="{{ $item['route'] ?? $item['url'] ?? '#' }}" 
                   class="px-3 py-2 rounded transition-colors {{ $isActive($item['route'] ?? $item['url'] ?? '') ? 'bg-space-primary text-space-black font-bold' : 'text-gray-400 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }}"
                   @if($isActive($item['route'] ?? $item['url'] ?? '')) aria-current="page" @endif>
                    > {{ strtoupper($item['label'] ?? $item['text'] ?? '') }}
                </a>
            @endforeach
        </div>
    </nav>
@elseif($variant === 'terminal')
    <div class="fixed bottom-0 left-0 right-0 bg-surface-dark dark:bg-surface-dark border-t border-border-dark dark:border-border-dark font-mono z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center gap-2">
                <span class="text-gray-500 dark:text-gray-500 text-sm">SYSTEM@SPACE-XPLORER:~$</span>
                <div class="flex-1 flex items-center gap-4 text-sm">
                    @foreach($items as $item)
                        @if(isset($item['type']) && $item['type'] === 'form')
                            <form method="{{ $item['method'] ?? 'POST' }}" action="{{ $item['route'] ?? $item['url'] ?? '#' }}" class="inline">
                                @csrf
                                @if(isset($item['method']) && strtoupper($item['method']) !== 'GET' && strtoupper($item['method']) !== 'POST')
                                    @method($item['method'])
                                @endif
                                @php
                                    $colorClass = match($item['color'] ?? 'primary') {
                                        'error' => 'text-error dark:text-error hover:text-error-light dark:hover:text-error-light',
                                        'secondary' => 'text-space-secondary dark:text-space-secondary hover:text-space-secondary-light dark:hover:text-space-secondary-light',
                                        default => 'text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light',
                                    };
                                @endphp
                                <button type="submit" class="{{ $colorClass }} transition-colors">
                                    > {{ strtoupper($item['label'] ?? $item['text'] ?? 'SUBMIT') }}
                                </button>
                            </form>
                        @else
                            @php
                                $colorClass = match($item['color'] ?? 'primary') {
                                    'error' => 'text-error dark:text-error hover:text-error-light dark:hover:text-error-light',
                                    'secondary' => 'text-space-secondary dark:text-space-secondary hover:text-space-secondary-light dark:hover:text-space-secondary-light',
                                    'muted' => 'text-gray-500 dark:text-gray-500 hover:text-gray-400 dark:hover:text-gray-400',
                                    default => 'text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light',
                                };
                            @endphp
                            <a href="{{ $item['route'] ?? $item['url'] ?? '#' }}" 
                               class="{{ $colorClass }} transition-colors"
                               @if($isActive($item['route'] ?? $item['url'] ?? '')) aria-current="page" @endif>
                                > {{ strtoupper($item['label'] ?? $item['text'] ?? '') }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif

