<div class="max-w-md mx-auto mt-8">
    <div class="bg-white dark:bg-surface-dark shadow-md rounded-lg px-8 pt-6 pb-8 mb-4 border border-gray-200 dark:border-border-dark">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
            Sign In
        </h2>

        <form wire:submit="login">
            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Email
                </label>
                <input
                    type="email"
                    id="email"
                    wire:model="email"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary @error('email') border-error dark:border-error @enderror"
                    placeholder="Enter your email"
                    autofocus
                >
                @error('email')
                    <p class="text-error text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Password
                </label>
                <input
                    type="password"
                    id="password"
                    wire:model="password"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary @error('password') border-error dark:border-error @enderror"
                    placeholder="Enter your password"
                >
                @error('password')
                    <p class="text-error text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 disabled:opacity-50 w-full transition-colors"
                >
                    <span wire:loading.remove wire:target="login">Sign In</span>
                    <span wire:loading wire:target="login">Signing in...</span>
                </button>
            </div>

            <!-- Register Link -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-bold">
                        Register
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
