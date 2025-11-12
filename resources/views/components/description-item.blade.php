@props([
    'term' => '',
    'value' => null,
    'mono' => false, // Pour les IDs, codes, etc.
])

<div>
    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
        {{ $term }}
    </dt>
    <dd class="{{ $mono ? 'font-mono' : '' }} mt-1 text-sm text-gray-900 dark:text-white">
        {{ $value ?? $slot }}
    </dd>
</div>
