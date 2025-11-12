<x-container variant="standard" class="py-8">
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
                        message="[OK] Session loaded for user: {{ $user->name ?? 'UNKNOWN' }}@if($user)[{{ $user->matricule }}]@endif"
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
            @endif
        </div>
    @endif
</x-container>
