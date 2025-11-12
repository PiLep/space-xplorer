<x-container
    variant="compact"
    class="mt-8 font-mono"
>
    <!-- Terminal Header -->
    <div class="mb-6">
        <x-terminal-prompt command="init_password_reset" />
        <x-terminal-message
            message="[INFO] Please provide your email address to receive a password reset link"
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

            <form wire:submit="sendResetLink">
                <!-- Email Input -->
                <x-form-input
                    type="email"
                    name="email"
                    label="enter_email"
                    wireModel="email"
                    placeholder="denis.parker@operations.wy"
                    variant="terminal"
                    autofocus
                    marginBottom="mb-6"
                />

                <!-- Submit Command -->
                <div class="mt-8">
                    <x-terminal-prompt command="send_reset_link" />
                    <x-button
                        type="submit"
                        variant="primary"
                        size="lg"
                        wireLoading="sendResetLink"
                        wireLoadingText="[PROCESSING] Sending reset link..."
                        terminal
                    >
                        > SEND_RESET_LINK
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
