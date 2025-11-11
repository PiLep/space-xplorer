@props([
    'title' => null,
])

<div class="bg-surface-dark dark:bg-surface-dark shadow rounded-lg border border-border-dark dark:border-border-dark mb-6">
    <div class="px-4 py-5 sm:p-6">
        @if($title)
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ $title }}</h3>
        @endif
        {{ $slot }}
    </div>
</div>


