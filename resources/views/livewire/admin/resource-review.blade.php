<div>
    @if($currentResource)
        <!-- Resource Card -->
        <div class="bg-surface-dark dark:bg-surface-dark shadow-lg rounded-lg border border-border-dark dark:border-border-dark overflow-hidden">
            <!-- Header with count -->
            <div class="px-6 py-4 border-b border-border-dark dark:border-border-dark bg-surface-dark dark:bg-surface-dark">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Review Resource
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ $pendingCount }} {{ $pendingCount === 1 ? 'resource' : 'resources' }} pending
                        </p>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            {{ $currentResource->type === 'avatar_image' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                            {{ $currentResource->type === 'planet_image' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                            {{ $currentResource->type === 'planet_video' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}">
                            {{ str_replace('_', ' ', ucfirst($currentResource->type)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Content Section: Image left, Info right -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:h-[600px]">
                    <!-- Image/Video Preview (Left) -->
                    <div class="flex items-center justify-center">
                        @if($currentResource->file_url)
                            @if($currentResource->type === 'planet_video')
                                <video 
                                    src="{{ $currentResource->file_url }}" 
                                    controls 
                                    class="w-full h-full rounded-lg shadow-lg object-contain"
                                    wire:loading.class="opacity-50"
                                >
                                    Your browser does not support the video tag.
                                </video>
                            @else
                                <img 
                                    src="{{ $currentResource->file_url }}" 
                                    alt="Resource preview" 
                                    class="w-full h-full rounded-lg shadow-lg object-contain"
                                    wire:loading.class="opacity-50"
                                >
                            @endif
                        @else
                            <div class="w-full h-full bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <span class="text-gray-400">No preview available</span>
                            </div>
                        @endif
                    </div>

                    <!-- Resource Info (Right) -->
                    <div class="space-y-4 flex flex-col h-full">
                        @if($currentResource->description)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Description</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $currentResource->description }}</dd>
                            </div>
                        @endif

                        @if($currentResource->tags && count($currentResource->tags) > 0)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Tags</dt>
                                <dd class="flex flex-wrap gap-2">
                                    @foreach($currentResource->tags as $tag)
                                        <span wire:key="tag-{{ $tag }}" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </dd>
                            </div>
                        @endif

                        <div class="flex-1 flex flex-col min-h-0">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Prompt</dt>
                            <dd class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 p-3 rounded font-mono text-xs break-words flex-1 overflow-y-auto">
                                {{ $currentResource->prompt }}
                            </dd>
                        </div>

                        @if($currentResource->creator)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Created By</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">
                                    {{ $currentResource->creator->name }} ({{ $currentResource->creator->email }})
                                </dd>
                            </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Created At</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                {{ $currentResource->created_at->format('Y-m-d H:i:s') }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="px-6 py-4 border-t border-border-dark dark:border-border-dark bg-surface-dark dark:bg-surface-dark">
                <div class="flex justify-center gap-4">
                    <x-button 
                        wire:click="approve" 
                        variant="primary" 
                        size="lg"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        id="approve-button"
                    >
                        <span wire:loading.remove wire:target="approve">✓ Approve</span>
                        <span wire:loading wire:target="approve">Processing...</span>
                    </x-button>

                    <x-button 
                        wire:click="openRejectModal" 
                        variant="danger" 
                        size="lg"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        id="reject-button"
                    >
                        ✗ Reject
                    </x-button>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        @if($showRejectModal)
            <div 
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
                wire:click="closeRejectModal"
            >
                <div 
                    class="bg-surface-dark dark:bg-surface-dark rounded-lg shadow-xl max-w-md w-full mx-4 border border-border-dark dark:border-border-dark" 
                    wire:click.stop
                >
                    <div class="px-6 py-4 border-b border-border-dark dark:border-border-dark">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Reject Resource</h3>
                    </div>
                    <div class="px-6 py-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Rejection Reason (optional)
                        </label>
                        <textarea
                            wire:model="rejectionReason"
                            id="rejection_reason"
                            rows="4"
                            maxlength="500"
                            placeholder="Enter reason for rejection (optional)..."
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary"
                            autofocus
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ strlen($rejectionReason) }}/500 characters
                        </p>
                    </div>
                    <div class="px-6 py-4 border-t border-border-dark dark:border-border-dark flex justify-end gap-3">
                        <x-button 
                            wire:click="closeRejectModal" 
                            variant="ghost" 
                            size="sm"
                            wire:loading.attr="disabled"
                        >
                            Cancel
                        </x-button>
                        <x-button 
                            wire:click="reject" 
                            variant="danger" 
                            size="sm"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="reject">Confirm Reject</span>
                            <span wire:loading wire:target="reject">Processing...</span>
                        </x-button>
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- No more resources -->
        <div class="bg-surface-dark dark:bg-surface-dark shadow-lg rounded-lg border border-border-dark dark:border-border-dark p-12 text-center">
            <div class="max-w-md mx-auto">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    All Done!
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    There are no more resources pending review.
                </p>
                <x-button href="{{ route('admin.resources.index') }}" variant="primary" size="md">
                    Back to Resources
                </x-button>
            </div>
        </div>
    @endif
</div>

