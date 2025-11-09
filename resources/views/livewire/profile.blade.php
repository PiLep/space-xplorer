<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
            Profile Settings
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">
            Manage your account information and preferences.
        </p>
    </div>

    @if ($loading)
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-space-primary"></div>
        </div>
    @elseif ($user)
        <div class="bg-white dark:bg-surface-dark shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-border-dark">
            <div class="px-8 py-6 border-b border-gray-200 dark:border-border-dark">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Account Information</h2>
            </div>

            <form wire:submit="updateProfile" class="px-8 py-6">
                <!-- Success Message -->
                @if ($success)
                    <div class="bg-green-100 dark:bg-success-dark border border-green-400 dark:border-success text-green-700 dark:text-success-light px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ $success }}</span>
                    </div>
                @endif

                <!-- Error Message -->
                @if ($error)
                    <div class="bg-red-100 dark:bg-error-dark border border-red-400 dark:border-error text-red-700 dark:text-error-light px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ $error }}</span>
                    </div>
                @endif

                <!-- Name -->
                <div class="mb-6">
                    <label for="name" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Name
                    </label>
                    <input
                        type="text"
                        id="name"
                        wire:model="name"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary @error('name') border-error dark:border-error @enderror"
                        placeholder="Enter your name"
                    >
                    @error('name')
                        <p class="text-error text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        wire:model="email"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary @error('email') border-error dark:border-error @enderror"
                        placeholder="Enter your email"
                    >
                    @error('email')
                        <p class="text-error text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- User ID (read-only) -->
                <div class="mb-6">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        User ID
                    </label>
                    <input
                        type="text"
                        value="{{ $user['id'] }}"
                        disabled
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-surface-medium border-gray-300 dark:border-border-dark cursor-not-allowed"
                    >
                    <p class="text-gray-500 dark:text-gray-400 text-xs italic mt-1">This is your unique user identifier.</p>
                </div>

                <!-- Home Planet ID (read-only) -->
                @if ($user['home_planet_id'])
                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Home Planet ID
                        </label>
                        <input
                            type="text"
                            value="{{ $user['home_planet_id'] }}"
                            disabled
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-surface-medium border-gray-300 dark:border-border-dark cursor-not-allowed"
                        >
                        <p class="text-gray-500 dark:text-gray-400 text-xs italic mt-1">Your home planet identifier.</p>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('dashboard') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-surface-medium dark:hover:bg-surface-dark text-gray-900 dark:text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 transition-colors border border-gray-300 dark:border-border-dark">
                        Cancel
                    </a>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 disabled:opacity-50 transition-colors"
                    >
                        <span wire:loading.remove wire:target="updateProfile">Save Changes</span>
                        <span wire:loading wire:target="updateProfile">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
