@props([
    'title' => null,
    'titleClass' => 'text-2xl font-bold text-gray-900 dark:text-white dark:text-glow-subtle mb-6 text-center',
    'headerSeparated' => false,
    'shadow' => 'shadow-md',
    'padding' => 'px-8 pt-6 pb-8',
])

@php
    $titleId = $title ? 'form-card-' . Str::slug($title) : null;
@endphp

<section 
    class="bg-white dark:bg-surface-dark {{ $shadow }} rounded-lg {{ $headerSeparated ? 'overflow-hidden' : '' }} border border-gray-200 dark:border-border-dark scan-effect"
    @if($titleId) aria-labelledby="{{ $titleId }}" @endif
    role="region"
>
    @if($title && $headerSeparated)
        <div class="px-8 py-6 border-b border-gray-200 dark:border-border-dark">
            <h2 id="{{ $titleId }}" class="text-2xl font-semibold text-gray-900 dark:text-white dark:text-glow-subtle">
                {{ $title }}
            </h2>
        </div>
        <div class="{{ $padding }}">
            {{ $slot }}
        </div>
    @else
        <div class="{{ $padding }}">
            @if($title)
                <h2 id="{{ $titleId }}" class="{{ $titleClass }}">
                    {{ $title }}
                </h2>
            @endif
            
            {{ $slot }}
        </div>
    @endif
</section>

