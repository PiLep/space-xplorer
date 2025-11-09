<div class="mx-auto mt-8 max-w-4xl font-mono">
    <!-- Terminal Header -->
    <div class="mb-6">
        <x-terminal-prompt command="init_auth_terminal" />
        <x-terminal-message message="[OK] Authentication terminal initialized" />
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

                <!-- Submit Command -->
                <div class="mt-8">
                    <x-terminal-prompt
                        command="authenticate"
                    />
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
