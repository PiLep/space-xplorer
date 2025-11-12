<x-container
    variant="compact"
    class="mt-8 font-mono"
>
    <!-- Terminal Header -->
    <div class="mb-6">
        <x-terminal-prompt command="init_email_verification" />
        <x-terminal-message
            message="[SECURITY] STELLAR_CORP_MEGA_INC identity verification protocol activated"
            :marginBottom="''"
        />
        <x-terminal-message
            message="[INFO] Authentication token dispatched to registered corporate email address"
            :marginBottom="''"
        />
        <x-terminal-message
            message="[REQUIRED] Enter 6-digit security clearance code to proceed with account activation"
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
                    :message="'[SECURITY] Token dispatched to corporate address: ' . $this->maskedEmail"
                    :marginBottom="''"
                />
            </div>

            <!-- Code Input Form -->
            <form wire:submit="verify">
                <!-- Code Input -->
                <x-form-input
                    type="text"
                    name="code"
                    label="enter_security_clearance_code"
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
                        :message="$prefix . ' ' . $this->attemptsRemaining . ' authentication attempts remaining before security lockout'"
                        marginBottom="mb-4"
                    />
                @endif

                <!-- Submit Command -->
                <div class="mt-8">
                    <x-terminal-prompt command="verify_security_clearance" />
                    <x-button
                        type="submit"
                        variant="primary"
                        size="lg"
                        wireLoading="verify"
                        wireLoadingText="[PROCESSING] Validating security clearance..."
                        terminal
                    >
                        > EXECUTE_VERIFICATION
                    </x-button>
                </div>
            </form>

            <!-- Resend Code Section -->
            <div class="mt-6">
                <x-terminal-message message="[QUERY] Authentication token not received? Request new clearance code from STELLAR_CORP_MEGA_INC security division" />
                @if ($this->canResend)
                    <form wire:submit="resend" class="mt-2">
                        <x-button
                            type="submit"
                            variant="secondary"
                            size="md"
                            wireLoading="resend"
                            wireLoadingText="[PROCESSING] Dispatching new security token..."
                            terminal
                        >
                            > REQUEST_NEW_TOKEN
                        </x-button>
                    </form>
                @else
                    <div 
                        class="mt-2 text-sm text-error dark:text-error"
                        x-data="{ 
                            seconds: {{ $this->resendCooldown }},
                            init() {
                                if (this.seconds > 0) {
                                    const interval = setInterval(() => {
                                        this.seconds--;
                                        if (this.seconds <= 0) {
                                            clearInterval(interval);
                                        }
                                    }, 1000);
                                }
                            }
                        }"
                    >
                        <div x-show="seconds > 0">
                            [SECURITY] New token request available in <span class="font-bold" x-text="seconds"></span> seconds (anti-fraud protocol)
                        </div>
                        <div x-show="seconds <= 0" x-cloak>
                            <div class="mb-2">
                                [INFO] Rate limit expired. Please refresh the page to request a new token.
                            </div>
                            <button 
                                type="button"
                                @click="window.location.reload()"
                                class="text-space-primary dark:text-space-primary hover:underline text-xs"
                            >
                                > REFRESH_PAGE
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-container>
