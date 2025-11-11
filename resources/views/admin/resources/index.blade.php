@extends('admin.layout')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="flex justify-between items-center mb-8">
    <x-page-header title="Resources" />
    <div class="flex gap-4">
        <x-button href="{{ route('admin.resources.review') }}" variant="secondary" size="sm">
            ⚡ Quick Review
        </x-button>
        <x-button href="{{ route('admin.resources.create') }}" variant="primary" size="sm">
            + Generate Resource
        </x-button>
        <x-button href="{{ route('admin.dashboard') }}" variant="ghost" size="sm">
            ← Back to Dashboard
        </x-button>
    </div>
</div>

<!-- Filters -->
<div class="bg-surface-dark dark:bg-surface-dark shadow rounded-lg border border-border-dark dark:border-border-dark mb-6">
    <div class="px-4 py-5 sm:p-6">
        <form method="GET" action="{{ route('admin.resources.index') }}" class="flex gap-4 items-end">
            <div class="flex-1">
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                <select name="type" id="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary">
                    <option value="">All Types</option>
                    <option value="avatar_image" {{ request('type') === 'avatar_image' ? 'selected' : '' }}>Avatar Image</option>
                    <option value="planet_image" {{ request('type') === 'planet_image' ? 'selected' : '' }}>Planet Image</option>
                    <option value="planet_video" {{ request('type') === 'planet_video' ? 'selected' : '' }}>Planet Video</option>
                </select>
            </div>
            <div class="flex-1">
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary">
                    <option value="">All Statuses</option>
                    <option value="generating" {{ request('status') === 'generating' ? 'selected' : '' }}>Generating</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="flex gap-2 ml-auto">
                <x-button type="submit" variant="ghost" size="sm">
                    Filter
                </x-button>
                @if(request()->has('type') || request()->has('status'))
                    <x-button href="{{ route('admin.resources.index') }}" variant="ghost" size="sm">
                        Clear
                    </x-button>
                @endif
            </div>
        </form>
    </div>
</div>

    <x-table
        :headers="[
            ['label' => 'ID', 'key' => 'id', 'cellClass' => 'font-mono text-gray-500 dark:text-gray-400'],
            ['label' => 'Type', 'key' => 'type'],
            ['label' => 'Status', 'key' => 'status'],
            ['label' => 'Preview', 'key' => 'file_url'],
            ['label' => 'Created', 'key' => 'created_at', 'format' => 'datetime'],
            ['label' => 'Actions'],
        ]"
    :rows="$resources"
    :pagination="$resources"
    emptyMessage="No resources found"
    hover
>
    @foreach($resources as $resource)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500 dark:text-gray-400">
                {{ substr($resource->id, 0, 8) }}...
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $resource->type === 'avatar_image' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                    {{ $resource->type === 'planet_image' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                    {{ $resource->type === 'planet_video' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}">
                    {{ str_replace('_', ' ', ucfirst($resource->type)) }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $resource->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                    {{ $resource->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                    {{ $resource->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                    {{ $resource->status === 'generating' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 animate-pulse' : '' }}">
                    {{ ucfirst($resource->status) }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                @if($resource->status === 'generating')
                    <div class="w-16 h-16 bg-surface-dark dark:bg-surface-dark border border-border-dark dark:border-border-dark rounded flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-space-primary"></div>
                    </div>
                @elseif($resource->file_url)
                    @if($resource->type === 'planet_video')
                        <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                            </svg>
                        </div>
                    @else
                        <img src="{{ $resource->file_url }}" alt="Preview" class="w-16 h-16 object-cover rounded">
                    @endif
                @else
                    <span class="text-gray-400">No preview</span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ $resource->created_at->format('Y-m-d H:i') }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <x-button href="{{ route('admin.resources.show', $resource) }}" variant="ghost" size="sm">
                    View
                </x-button>
            </td>
        </tr>
    @endforeach
</x-table>
@endsection

