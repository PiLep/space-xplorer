<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <!-- Terminal Boot Messages (always visible at top) -->
    <x-terminal-boot
        :bootMessages="$bootMessages"
        :terminalBooted="$terminalBooted"
        :pollMethod="'nextBootStep'"
    />

    @if (!$terminalBooted)
        <!-- Still booting -->
        <div class="font-mono">
            <x-terminal-message message="[WAIT] Initializing dashboard..." />
        </div>
    @else
        <!-- Dashboard Content -->
        <div class="animate-fade-in">
            <div class="mb-8 font-mono">
                <x-terminal-prompt command="load_user_session" />
                @if ($user)
                    <x-terminal-message
                        message="[OK] Session loaded for user: {{ $user->name ?? 'UNKNOWN' }}"
                        marginBottom="mb-4"
                    />
                @endif
                <x-terminal-prompt command="display_home_planet" />
            </div>

            @if ($loading)
                <x-loading-spinner message="[LOADING] Accessing planetary database..." />
            @elseif ($error)
                <x-alert
                    type="error"
                    :message="$error"
                />
            @elseif ($planet)
                <!-- Planet Card -->
                <x-planet-card :planet="$planet" />

                <!-- Action Commands -->
                <div class="mt-8 font-mono">
                    <x-terminal-message
                        message="[READY] System ready for commands"
                        marginBottom="mb-4"
                    />
                    <x-button-group>
                        <x-button
                            variant="primary"
                            size="lg"
                            terminal
                        >
                            > EXPLORE_PLANETS
                        </x-button>
                        <x-button
                            href="{{ route('profile') }}"
                            variant="ghost"
                            size="lg"
                            terminal
                        >
                            > VIEW_PROFILE
                        </x-button>
                    </x-button-group>
                </div>
            @endif
        </div>
    @endif
</div>
