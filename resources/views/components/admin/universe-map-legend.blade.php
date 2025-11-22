@props([
    'starTypes' => [
        ['type' => 'yellow_dwarf', 'color' => '#FFD700', 'label' => 'Yellow Dwarf'],
        ['type' => 'red_dwarf', 'color' => '#FF6B6B', 'label' => 'Red Dwarf'],
        ['type' => 'orange_dwarf', 'color' => '#FF8C42', 'label' => 'Orange Dwarf'],
        ['type' => 'red_giant', 'color' => '#FF4500', 'label' => 'Red Giant'],
        ['type' => 'blue_giant', 'color' => '#4169E1', 'label' => 'Blue Giant'],
        ['type' => 'white_dwarf', 'color' => '#F0F0F0', 'label' => 'White Dwarf'],
        ['type' => 'unknown', 'color' => '#FFFFFF', 'label' => 'Unknown', 'border' => true],
    ],
])

<div class="bg-surface-dark dark:bg-surface-dark border-border-dark dark:border-border-dark mb-4 rounded-lg border p-4 shadow">
    <h3 class="mb-3 font-mono text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
        Star Type Legend
    </h3>
    <div class="flex flex-wrap gap-4 font-mono text-sm">
        @foreach($starTypes as $starType)
            <div class="flex items-center gap-2">
                <div
                    class="h-4 w-4 rounded-full {{ isset($starType['border']) && $starType['border'] ? 'border border-gray-400' : '' }}"
                    style="background-color: {{ $starType['color'] }};"
                ></div>
                <span class="text-gray-700 dark:text-gray-300">{{ $starType['label'] }}</span>
            </div>
        @endforeach
    </div>
</div>

