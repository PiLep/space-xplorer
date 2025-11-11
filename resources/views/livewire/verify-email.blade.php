<x-container
    variant="compact"
    class="mt-8 font-mono"
>
    <!-- Terminal Header -->
    <div class="mb-6">
        <x-terminal-prompt command="init_email_verification" />
        <x-terminal-message
            message="[INFO] A verification code has been sent to your email"
            :marginBottom="''"
        />
        <x-terminal-message
            message="[INFO] Enter the 6-digit code below to verify your email address"
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

            <!-- Email Display (masked) -->
            <div class="mb-6">
                <x-terminal-message
                    :message="'[INFO] Code sent to: ' . $this->maskedEmail"
                    :marginBottom="''"
                />
            </div>

            <!-- Code Input Form -->
            <form wire:submit="verify">
                <!-- Code Input -->
                <x-form-input
                    type="text"
                    name="code"
                    label="enter_verification_code"
                    wireModel="code"
                    placeholder="000000"
                    variant="terminal"
                    autofocus
                    inputmode="numeric"
                    pattern="[0-9]*"
                    maxlength="6"
                    marginBottom="mb-4"
                />

                <!-- Attempts Remaining -->
                @if ($this->attemptsRemaining < 5)
                    @php
                        $prefix = match(true) {
                            $this->attemptsRemaining <= 1 => '[ERROR]',
                            $this->attemptsRemaining <= 2 => '[WARNING]',
                            default => '[INFO]'
                        };
                    @endphp
                    <x-terminal-message
                        :message="$prefix . ' ' . $this->attemptsRemaining . ' verification attempts remaining'"
                        marginBottom="mb-4"
                    />
                @endif

                <!-- Submit Command -->
                <div class="mt-8">
                    <x-terminal-prompt command="verify_email" />
                    <x-button
                        type="submit"
                        variant="primary"
                        size="lg"
                        wireLoading="verify"
                        wireLoadingText="[PROCESSING] Verifying code..."
                        terminal
                    >
                        > VERIFY_CODE
                    </x-button>
                </div>
            </form>

            <!-- Resend Code Section -->
            <div class="mt-6">
                <x-terminal-message message="[INFO] Didn't receive the code?" />
                @if ($this->canResend)
                    <form wire:submit="resend" class="mt-2">
                        <x-button
                            type="submit"
                            variant="secondary"
                            size="md"
                            wireLoading="resend"
                            wireLoadingText="[PROCESSING] Sending new code..."
                            terminal
                        >
                            > RESEND_CODE
                        </x-button>
                    </form>
                @else
                    <x-terminal-message
                        :message="'[INFO] Resend available in ' . $this->resendCooldown . ' seconds'"
                        marginTop="mt-2"
                    />
                @endif
            </div>
        </div>
    </div>
</x-container>
