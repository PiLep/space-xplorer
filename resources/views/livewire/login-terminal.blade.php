<div class="max-w-4xl mx-auto mt-8 font-mono">
    <!-- Terminal Header -->
    <div class="mb-6">
        <div class="text-sm text-space-primary dark:text-space-primary mb-2">
            <span class="text-gray-500 dark:text-gray-500">SYSTEM@SPACE-XPLORER:~$</span> <span class="text-space-primary dark:text-space-primary">init_auth_terminal</span>
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-500 mb-2">
            [OK] Authentication terminal initialized
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-500">
            [INFO] Please provide your credentials to access the system
        </div>
    </div>

    <!-- Terminal Interface -->
    <div class="bg-white dark:bg-surface-dark rounded-lg overflow-hidden terminal-border-simple scan-effect">
        <div class="p-8">
            <!-- Status Message -->
            @if($status)
                <div class="mb-6 text-sm {{ str_contains($status, '[ERROR]') ? 'text-error dark:text-error' : (str_contains($status, '[SUCCESS]') ? 'text-space-primary dark:text-space-primary' : 'text-gray-500 dark:text-gray-500') }}">
                    {{ $status }}
                </div>
            @endif

            <form wire:submit="login">
                <!-- Email Input -->
                <div class="mb-6">
                    <div class="text-sm text-gray-500 dark:text-gray-500 mb-2">
                        <span class="text-space-primary dark:text-space-primary">SYSTEM@SPACE-XPLORER:~$</span> <span class="text-space-secondary dark:text-space-secondary">enter_email</span>
                    </div>
                    <input
                        type="email"
                        wire:model="email"
                        class="w-full bg-transparent border-b-2 border-gray-300 dark:border-border-dark focus:border-space-primary dark:focus:border-space-primary text-gray-900 dark:text-white py-2 px-0 focus:outline-none font-mono text-sm transition-colors @error('email') border-error dark:border-error @enderror"
                        placeholder="user@domain.com"
                        autofocus
                    >
                    @error('email')
                        <div class="mt-2 text-xs text-error dark:text-error">
                            [ERROR] {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="mb-6">
                    <div class="text-sm text-gray-500 dark:text-gray-500 mb-2">
                        <span class="text-space-primary dark:text-space-primary">SYSTEM@SPACE-XPLORER:~$</span> <span class="text-space-secondary dark:text-space-secondary">enter_password</span>
                    </div>
                    <input
                        type="password"
                        wire:model="password"
                        class="w-full bg-transparent border-b-2 border-gray-300 dark:border-border-dark focus:border-space-primary dark:focus:border-space-primary text-gray-900 dark:text-white py-2 px-0 focus:outline-none font-mono text-sm transition-colors @error('password') border-error dark:border-error @enderror"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <div class="mt-2 text-xs text-error dark:text-error">
                            [ERROR] {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Submit Command -->
                <div class="mt-8">
                    <div class="text-sm text-gray-500 dark:text-gray-500 mb-4">
                        <span class="text-space-primary dark:text-space-primary">SYSTEM@SPACE-XPLORER:~$</span> <span class="text-space-secondary dark:text-space-secondary">authenticate</span>
                    </div>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 disabled:opacity-50 transition-colors glow-primary hover:glow-primary font-mono text-sm"
                    >
                        <span wire:loading.remove wire:target="login">> EXECUTE_LOGIN</span>
                        <span wire:loading wire:target="login">[PROCESSING] Authenticating...</span>
                    </button>
                </div>
            </form>

            <!-- Register Link -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-border-dark">
                <div class="text-sm text-gray-500 dark:text-gray-500 mb-2">
                    [INFO] New user? Create an account:
                </div>
                <a href="{{ route('register') }}" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-mono text-sm underline">
                    > REGISTER_NEW_USER
                </a>
            </div>
        </div>
    </div>
</div>

