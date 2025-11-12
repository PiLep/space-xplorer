<div>
    @if($error)
        <x-alert
            type="error"
            :message="$error"
            class="mb-4"
        />
    @endif

    <x-button 
        wire:click="openConfirmModal" 
        variant="danger" 
        size="sm"
        wire:loading.attr="disabled"
    >
        Delete User
    </x-button>

    <!-- Confirm Delete Modal -->
    @if($showConfirmModal)
        <div 
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
            wire:click="closeConfirmModal"
        >
            <div 
                class="bg-surface-dark dark:bg-surface-dark rounded-lg shadow-xl max-w-md w-full mx-4 border border-border-dark dark:border-border-dark" 
                wire:click.stop
            >
                <div class="px-6 py-4 border-b border-border-dark dark:border-border-dark">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete User</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-700 dark:text-gray-300 mb-2">
                        Are you sure you want to delete <strong>{{ $user->name }}</strong>?
                    </p>
                    <p class="text-error dark:text-error text-sm">
                        This action cannot be undone.
                    </p>
                </div>
                <div class="px-6 py-4 border-t border-border-dark dark:border-border-dark flex justify-end gap-3">
                    <x-button 
                        wire:click="closeConfirmModal" 
                        variant="ghost" 
                        size="sm"
                        wire:loading.attr="disabled"
                    >
                        Cancel
                    </x-button>
                    <x-button 
                        wire:click="delete" 
                        variant="danger" 
                        size="sm"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                    >
                        <span wire:loading.remove wire:target="delete">Confirm Delete</span>
                        <span wire:loading wire:target="delete">Deleting...</span>
                    </x-button>
                </div>
            </div>
        </div>
    @endif
</div>

