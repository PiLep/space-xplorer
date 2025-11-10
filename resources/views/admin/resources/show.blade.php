@extends('admin.layout')

@section('content')
<div class="flex justify-between items-center mb-8">
    <x-page-header title="Resource Details" />
    <div class="flex gap-4">
        <x-button href="{{ route('admin.resources.index') }}" variant="ghost" size="sm">
            ← Back to Resources
        </x-button>
        <x-button href="{{ route('admin.dashboard') }}" variant="ghost" size="sm">
            Dashboard
        </x-button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Resource Details -->
    <div class="bg-surface-dark dark:bg-surface-dark shadow rounded-lg border border-border-dark dark:border-border-dark overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $resource->id }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $resource->type === 'avatar_image' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                            {{ $resource->type === 'planet_image' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $resource->type === 'planet_video' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}">
                            {{ str_replace('_', ' ', ucfirst($resource->type)) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $resource->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $resource->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                            {{ $resource->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                            {{ ucfirst($resource->status) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $resource->created_at->format('Y-m-d H:i:s') }}</dd>
                </div>
                @if($resource->creator)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $resource->creator->name }} ({{ $resource->creator->email }})</dd>
                    </div>
                @endif
                @if($resource->approver)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved/Rejected By</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $resource->approver->name }} ({{ $resource->approver->email }})</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved/Rejected At</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $resource->approved_at?->format('Y-m-d H:i:s') }}</dd>
                    </div>
                @endif
                @if($resource->rejection_reason)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejection Reason</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $resource->rejection_reason }}</dd>
                    </div>
                @endif
                @if($resource->tags && count($resource->tags) > 0)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tags</dt>
                        <dd class="mt-1">
                            <div class="flex flex-wrap gap-2">
                                @foreach($resource->tags as $tag)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        </dd>
                    </div>
                @endif
                @if($resource->description)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $resource->description }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Prompt</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 p-3 rounded font-mono text-xs break-words">{{ $resource->prompt }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Preview and Actions -->
    <div class="space-y-6">
        <!-- Preview -->
        <div class="bg-surface-dark dark:bg-surface-dark shadow rounded-lg border border-border-dark dark:border-border-dark overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Preview</h3>
                @if($resource->file_url)
                    @if($resource->type === 'planet_video')
                        <video src="{{ $resource->file_url }}" controls class="w-full rounded">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <img src="{{ $resource->file_url }}" alt="Resource preview" class="w-full rounded">
                    @endif
                @else
                    <div class="w-full h-64 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                        <span class="text-gray-400">No preview available</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions (only for pending resources) -->
        @if($resource->isPending())
            <div class="bg-surface-dark dark:bg-surface-dark shadow rounded-lg border border-border-dark dark:border-border-dark overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Actions</h3>
                    <form method="POST" action="{{ route('admin.resources.approve', $resource) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <x-button type="submit" variant="primary" size="sm" class="w-full">
                            Approve Resource
                        </x-button>
                    </form>
                    <form method="POST" action="{{ route('admin.resources.approve', $resource) }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <div class="mb-4">
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rejection Reason
                            </label>
                            <textarea
                                name="rejection_reason"
                                id="rejection_reason"
                                rows="3"
                                maxlength="500"
                                placeholder="Enter reason for rejection..."
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary"
                            ></textarea>
                        </div>
                        <x-button type="submit" variant="danger" size="sm" class="w-full">
                            Reject Resource
                        </x-button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

