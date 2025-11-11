<x-container
    variant="compact"
    class="mt-8 font-mono"
>
    <!-- Terminal Boot Messages (always visible at top) -->
    <x-terminal-boot
        :bootMessages="$bootMessages"
        :terminalBooted="$terminalBooted"
        :pollMethod="'nextBootStep'"
    />

    @if (!$terminalBooted)
        <!-- Still booting -->
        <div class="font-mono">
            <x-terminal-message message="[WAIT] Initializing authentication terminal..." />
        </div>
    @else
        <!-- Login Form -->
        <div class="animate-fade-in">
            <!-- Terminal Header -->
            <div class="mb-6">
                <x-terminal-prompt command="init_auth_terminal" />
                <x-terminal-message
                    message="[INFO] Please provide your credentials to access the system"
                    :marginBottom="''"
                />
            </div>

            <!-- Terminal Interface -->
            <div class="dark:bg-surface-dark terminal-border-simple scan-effect overflow-hidden rounded-lg bg-white">
                <div class="p-8">
                    <!-- Status Message -->
                    @if ($status)
                        <x-terminal-message
                            :message="$status"
                            marginBottom="mb-6"
                        />
                    @endif

                    <form wire:submit="login">
                        <!-- Email Input -->
                        <x-form-input
                            type="email"
                            name="email"
                            label="enter_email"
                            wireModel="email"
                            placeholder="user@domain.com"
                            variant="terminal"
                            autofocus
                            marginBottom="mb-6"
                        />

                        <!-- Password Input -->
                        <x-form-input
                            type="password"
                            name="password"
                            label="enter_password"
                            wireModel="password"
                            placeholder="••••••••"
                            variant="terminal"
                            marginBottom="mb-6"
                        />

                        <!-- Remember Me Checkbox -->
                        <div class="mb-6">
                            <label class="flex items-center cursor-pointer group">
                                <input
                                    type="checkbox"
                                    wire:model="remember"
                                    id="remember"
                                    name="remember"
                                    aria-label="Memorize identity pattern"
                                    class="w-4 h-4 text-space-primary bg-surface-dark border-border-dark rounded focus:ring-space-primary focus:ring-2 cursor-pointer transition-colors duration-150"
                                >
                                <span class="ml-2 text-gray-300 dark:text-gray-300 text-sm font-mono group-hover:text-space-primary transition-colors duration-150">
                                    [OPTION] Memorize identity pattern
                                </span>
                            </label>
                        </div>

                        <!-- Submit Command -->
                        <div class="mt-8">
                            <x-terminal-prompt command="authenticate" />
                            <x-button
                                type="submit"
                                variant="primary"
                                size="lg"
                                wireLoading="login"
                                wireLoadingText="[PROCESSING] Authenticating..."
                                terminal
                            >
                                > EXECUTE_LOGIN
                            </x-button>
                        </div>
                    </form>

                    <!-- Forgot Password Link -->
                    <div class="mt-4">
                        <x-terminal-message message="[INFO] Forgot your password?" />
                        <x-terminal-link
                            href="{{ route('password.request') }}"
                            text="> RESET_PASSWORD"
                            marginTop="mt-2"
                            :showBorder="false"
                        />
                    </div>

                    <!-- Register Link -->
                    <div class="mt-4">
                        <x-terminal-message message="[INFO] New user? Create an account:" />
                        <x-terminal-link
                            href="{{ route('register') }}"
                            text="> REGISTER_NEW_USER"
                            marginTop="mt-2"
                            :showBorder="false"
                        />
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-container>
