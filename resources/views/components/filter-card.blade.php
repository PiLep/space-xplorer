@props([
    'title' => null,
])

<div
    class="bg-surface-dark dark:bg-surface-dark border-border-dark dark:border-border-dark mb-6 rounded-lg border shadow">
    <div class="px-4 py-5 sm:p-6">
        @if ($title)
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">{{ $title }}</h3>
        @endif
        {{ $slot }}
    </div>
</div>
