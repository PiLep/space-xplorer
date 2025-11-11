<x-container
    variant="compact"
    class="mt-8 font-mono"
>
    <!-- Terminal Header -->
    <div class="mb-6">
        <x-terminal-prompt command="init_password_reset" />
        <x-terminal-message
            message="[INFO] Please enter your new password"
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

            <form wire:submit="resetPassword">
                <!-- Hidden fields -->
                <input type="hidden" wire:model="token" name="token">
                <input type="hidden" wire:model="email" name="email">

                <!-- Email Display (read-only) -->
                <div class="mb-6">
                    <x-terminal-prompt command="user_email" />
                    <div class="text-sm text-gray-300 dark:text-gray-300 font-mono">
                        {{ $email }}
                    </div>
                </div>

                <!-- Password Input -->
                <x-form-input
                    type="password"
                    name="password"
                    label="enter_new_password"
                    wireModel="password"
                    placeholder="••••••••"
                    variant="terminal"
                    autofocus
                    marginBottom="mb-4"
                />

                <!-- Password Strength Indicator -->
                @if ($passwordStrength)
                    <x-terminal-message
                        :message="$passwordStrength"
                        marginBottom="mb-4"
                    />
                @endif

                <!-- Password Confirmation Input -->
                <x-form-input
                    type="password"
                    name="password_confirmation"
                    label="confirm_new_password"
                    wireModel="password_confirmation"
                    placeholder="••••••••"
                    variant="terminal"
                    marginBottom="mb-6"
                />

                <!-- Submit Command -->
                <div class="mt-8">
                    <x-terminal-prompt command="reset_password" />
                    <x-button
                        type="submit"
                        variant="primary"
                        size="lg"
                        wireLoading="resetPassword"
                        wireLoadingText="[PROCESSING] Resetting password..."
                        terminal
                    >
                        > RESET_PASSWORD
                    </x-button>
                </div>
            </form>

            <!-- Back to Login Link -->
            <div class="mt-4">
                <x-terminal-message message="[INFO] Remember your password?" />
                <x-terminal-link
                    href="{{ route('login') }}"
                    text="> RETURN_TO_LOGIN"
                    marginTop="mt-2"
                    :showBorder="false"
                />
            </div>
        </div>
    </div>
</x-container>

