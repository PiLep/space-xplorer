<div class="max-w-md mx-auto mt-8">
    <div class="bg-white dark:bg-surface-dark shadow-md rounded-lg px-8 pt-6 pb-8 mb-4 border border-gray-200 dark:border-border-dark scan-effect">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white dark:text-glow-subtle mb-6 text-center">
            Create Your Account
        </h2>

        <!-- General Error Message -->
        @if ($errors->any())
            <div class="mb-4 bg-red-100 dark:bg-error-dark border border-red-400 dark:border-error text-red-700 dark:text-error-light px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit="register">
            <!-- Name -->
            <div class="mb-4">
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
                >
                @error('email')
                    <p class="text-error text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
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

            <!-- Password Confirmation -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Confirm Password
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    wire:model="password_confirmation"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary @error('password_confirmation') border-error dark:border-error @enderror"
                    placeholder="Confirm your password"
                >
                @error('password_confirmation')
                    <p class="text-error text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 disabled:opacity-50 w-full transition-colors glow-primary hover:glow-primary"
                >
                    <span wire:loading.remove wire:target="register">Register</span>
                    <span wire:loading wire:target="register">Registering...</span>
                </button>
            </div>

            <!-- Login Link -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-bold">
                        Sign in
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
