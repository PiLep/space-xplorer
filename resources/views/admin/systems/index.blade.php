@extends('admin.layout')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <x-page-header title="Star Systems" />
        <div class="flex gap-2">
            <x-button
                href="{{ route('admin.map') }}"
                variant="ghost"
                size="sm"
            >
                View Universe Map
            </x-button>
            <x-button
                href="{{ route('admin.dashboard') }}"
                variant="ghost"
                size="sm"
            >
                ← Back to Dashboard
            </x-button>
        </div>
    </div>

    <x-table
        :headers="[
            ['label' => 'Name', 'key' => 'name', 'cellClass' => 'font-medium text-gray-900 dark:text-white'],
            ['label' => 'Star Type', 'key' => 'star_type'],
            ['label' => 'Planets', 'key' => 'planets_count'],
            ['label' => 'Coordinates', 'key' => 'coordinates'],
            ['label' => 'Status', 'key' => 'discovered'],
            ['label' => 'Created', 'key' => 'created_at', 'format' => 'datetime'],
            ['label' => 'Actions'],
        ]"
        :rows="$starSystems"
        :pagination="$starSystems"
        emptyMessage="No star systems found"
        hover
    >
        @foreach ($starSystems as $system)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white font-mono">
                    {{ $system->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if ($system->star_type)
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium
                                {{ $system->star_type === 'yellow_dwarf' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                {{ $system->star_type === 'red_dwarf' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                {{ $system->star_type === 'orange_dwarf' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                {{ $system->star_type === 'red_giant' ? 'bg-red-200 text-red-900 dark:bg-red-800 dark:text-red-300' : '' }}
                                {{ $system->star_type === 'blue_giant' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                {{ $system->star_type === 'white_dwarf' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}"
                        >
                            @if ($system->star_type === 'yellow_dwarf')
                                ⭐
                            @elseif ($system->star_type === 'red_dwarf')
                                🔴
                            @elseif ($system->star_type === 'orange_dwarf')
                                🟠
                            @elseif ($system->star_type === 'red_giant')
                                🔥
                            @elseif ($system->star_type === 'blue_giant')
                                💙
                            @elseif ($system->star_type === 'white_dwarf')
                                ⚪
                            @endif
                            {{ str_replace('_', ' ', ucfirst($system->star_type)) }}
                        </span>
                    @else
                        <span class="text-gray-400 dark:text-gray-500">Unknown</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-mono">{{ $system->planets_count ?? $system->planet_count }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono text-xs">
                    X: {{ number_format($system->x, 2) }}<br>
                    Y: {{ number_format($system->y, 2) }}<br>
                    Z: {{ number_format($system->z, 2) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if ($system->discovered)
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Discovered
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                            Undiscovered
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $system->created_at->format('Y-m-d H:i') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <x-button
                        href="{{ route('admin.systems.map', $system) }}"
                        variant="ghost"
                        size="sm"
                    >
                        View Map
                    </x-button>
                </td>
            </tr>
        @endforeach
    </x-table>
@endsection


