@extends('admin.layout')

@section('content')
<x-page-header title="Dashboard" />

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-8">
        <div class="bg-surface-dark dark:bg-surface-dark overflow-hidden shadow rounded-lg border border-border-dark dark:border-border-dark">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-space-primary dark:text-space-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Users</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $totalUsers }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resource Statistics -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Resource Statistics</h2>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($resourceStats as $type => $stat)
                <div class="bg-surface-dark dark:bg-surface-dark overflow-hidden shadow rounded-lg border border-border-dark dark:border-border-dark">
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $stat['label'] }}
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $stat['color'] === 'red' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                {{ $stat['color'] === 'orange' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}
                                {{ $stat['color'] === 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                {{ $stat['color'] === 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}">
                                {{ ucfirst($stat['status']) }}
                            </span>
                        </div>
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate mb-1">
                                Current / Optimal
                            </dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                {{ $stat['current'] }} / {{ $stat['optimal'] }}
                            </dd>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate mb-1">
                                Optimization
                            </dt>
                            <dd class="flex items-center gap-2">
                                <x-progress-bar 
                                    :percentage="$stat['capped_percentage']" 
                                    :color="$stat['color']" 
                                />
                                <span class="text-sm font-medium text-gray-900 dark:text-white min-w-[3.5rem] text-right">
                                    {{ number_format($stat['percentage'], 1) }}%
                                </span>
                            </dd>
                        </dl>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-surface-dark dark:bg-surface-dark shadow rounded-lg border border-border-dark dark:border-border-dark">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Recent Users</h3>
            <x-table
                :headers="[
                    ['label' => 'Name', 'key' => 'name', 'cellClass' => 'font-medium text-gray-900 dark:text-white'],
                    ['label' => 'Email', 'key' => 'email'],
                    ['label' => 'Registered', 'key' => 'created_at', 'format' => 'datetime'],
                    ['label' => 'Actions'],
                ]"
                :rows="$recentUsers"
                emptyMessage="No users found"
                hover
            >
                @foreach($recentUsers as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <x-button href="{{ route('admin.users.show', $user) }}" variant="ghost" size="sm">
                                View
                            </x-button>
                        </td>
                    </tr>
                @endforeach
            </x-table>
            <div class="mt-4">
                <x-button href="{{ route('admin.users.index') }}" variant="ghost" size="sm">
                    View all users →
                </x-button>
            </div>
        </div>
    </div>
@endsection

