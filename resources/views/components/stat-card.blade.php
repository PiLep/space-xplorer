@props([
    'label' => '',
    'value' => '',
])

<div class="bg-surface-dark dark:bg-surface-dark overflow-hidden shadow rounded-lg border border-border-dark dark:border-border-dark">
    <div class="p-5">
        <div class="flex items-center">
            @if(isset($icon))
                <div class="flex-shrink-0">
                    {{ $icon }}
                </div>
            @endif
            <div class="{{ isset($icon) ? 'ml-5' : '' }} w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                        {{ $label }}
                    </dt>
                    <dd class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ $value }}
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>

