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
            <x-terminal-message message="[WAIT] Initializing registration terminal..." />
        </div>
    @else
        <!-- Registration Form -->
        <div class="animate-fade-in">
            <!-- Terminal Header -->
            <div class="mb-6">
                <x-terminal-prompt command="init_registration_terminal" />
                <x-terminal-message
                    message="[INFO] Please provide your information to create a new account"
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

                    <form wire:submit="register">
                        <!-- Name Input -->
                        <x-form-input
                            type="text"
                            name="name"
                            label="enter_name"
                            wireModel="name"
                            placeholder="Gilbert Kane"
                            variant="terminal"
                            autofocus
                            marginBottom="mb-6"
                        />

                        <!-- Email Input -->
                        <x-form-input
                            type="email"
                            name="email"
                            label="enter_email"
                            wireModel="email"
                            placeholder="gilbert.kane@operations.wy"
                            variant="terminal"
                            marginBottom="mb-6"
                        />

                        <!-- Password Input -->
                        <x-form-input
                            type="password"
                            name="password"
                            label="enter_password"
                            wireModel="password"
                            placeholder="CLASSIFIED"
                            variant="terminal"
                            marginBottom="mb-6"
                        />

                        <!-- Password Confirmation Input -->
                        <x-form-input
                            type="password"
                            name="password_confirmation"
                            label="confirm_password"
                            wireModel="password_confirmation"
                            placeholder="CLASSIFIED"
                            variant="terminal"
                            marginBottom="mb-6"
                        />

                        <!-- Terms and Conditions Checkbox -->
                        <div class="mb-6">
                            <label class="group flex cursor-pointer items-start">
                                <input
                                    type="checkbox"
                                    wire:model="terms_accepted"
                                    id="terms_accepted"
                                    name="terms_accepted"
                                    aria-label="Accept corporate terms"
                                    class="text-space-primary bg-surface-dark border-border-dark focus:ring-space-primary mt-1 h-4 w-4 cursor-pointer rounded transition-colors duration-150 focus:ring-2"
                                >
                                <span
                                    class="group-hover:text-space-primary ml-2 font-mono text-sm text-gray-300 transition-colors duration-150 dark:text-gray-300"
                                >
                                    [REQUIRED] I consent to transfer all biometric data, neural patterns, and soul
                                    ownership rights to <span
                                        class="text-space-secondary dark:text-space-secondary">STELLAR_CORP_MEGA_INC</span>
                                    in perpetuity. I waive all legal and spiritual claims and agree to mandatory
                                    subscription fees (including afterlife premiums) as per <span
                                        class="text-space-secondary dark:text-space-secondary opacity-75"
                                    >TERMS_v47.2.pdf</span> (47,382 pages, auto-updating daily).
                                </span>
                            </label>
                            @error('terms_accepted')
                                <div
                                    class="text-error dark:text-error mt-2 text-xs font-semibold"
                                    role="alert"
                                >
                                    [ERROR] {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Submit Command -->
                        <div class="mt-8">
                            <x-terminal-prompt command="create_account" />
                            <x-button
                                type="submit"
                                variant="primary"
                                size="lg"
                                wireLoading="register"
                                wireLoadingText="[PROCESSING] Creating account..."
                                terminal
                            >
                                > EXECUTE_REGISTER
                            </x-button>
                        </div>
                    </form>

                    <!-- Login Link -->
                    <div class="mt-4">
                        <x-terminal-message message="[INFO] Already have an account?" />
                        <x-terminal-link
                            href="{{ route('login') }}"
                            text="> LOGIN_EXISTING_USER"
                            marginTop="mt-2"
                            :showBorder="false"
                        />
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-container>
