<x-container variant="compact" class="py-8">
    @if ($loading)
        <x-loading-spinner
            variant="simple"
            size="md"
            :showMessage="false"
        />
    @elseif ($user)
        <!-- Profile Card -->
        <div
            class="dark:bg-surface-dark terminal-border-simple scan-effect hologram mb-8 overflow-hidden rounded-lg bg-white shadow-lg">
            <div class="flex flex-col">
                <!-- Profile Header -->
                <div class="dark:border-border-dark border-b border-gray-200 px-8 py-6">
                    <div class="mb-4 flex items-center gap-6">
                        <!-- Avatar -->
                        @if ($user['avatar_url'])
                            <div class="flex-shrink-0 relative">
                                <img
                                    src="{{ $user['avatar_url'] }}"
                                    alt="{{ $user['name'] }}'s avatar"
                                    class="border-space-primary dark:border-space-primary terminal-border-simple h-24 w-24 rounded-lg border-2 object-cover shadow-lg avatar-image"
                                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                                />
                                @php
                                    $initials = strtoupper(substr($user['name'], 0, 1) . (strpos($user['name'], ' ') !== false ? substr($user['name'], strpos($user['name'], ' ') + 1, 1) : ''));
                                @endphp
                                <div
                                    class="dark:border-border-dark terminal-border-simple hidden flex h-24 w-24 flex-shrink-0 items-center justify-center rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-gray-200 dark:bg-gray-800 avatar-placeholder">
                                    <span class="font-mono text-xl font-bold text-gray-600 dark:text-gray-300">{{ $initials }}</span>
                                </div>
                            </div>
                        @endif
                        <!-- User Info -->
                        <div class="flex-1">
                            <h2
                                class="dark:text-glow-subtle mb-2 font-mono text-3xl font-bold text-gray-900 dark:text-white">
                                {{ strtoupper($user['name']) }}</h2>
                            <p class="font-mono text-lg uppercase tracking-wider text-gray-600 dark:text-gray-400">
                                USER_PROFILE</p>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                @if ($error)
                    <div class="dark:border-border-dark border-b border-gray-200 px-8 py-4">
                        <x-alert
                            type="error"
                            :message="$error"
                            :showPrompt="false"
                        />
                    </div>
                @endif

                <!-- User Information -->
                <div class="dark:border-border-dark border-t border-gray-200 px-8 py-6">
                    <h3
                        class="dark:text-glow-subtle mb-6 font-mono text-xl font-semibold text-gray-900 dark:text-white">
                        SYSTEM_DATA</h3>
                    <div class="space-y-3 font-mono">
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-32 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >NAME</span>
                            <span class="text-space-primary dark:text-space-primary flex-1">{{ $user['name'] }}</span>
                        </div>
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-32 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >EMAIL</span>
                            <span class="text-space-primary dark:text-space-primary flex-1">{{ $user['email'] }}</span>
                        </div>
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-32 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >USER_ID</span>
                            <span
                                class="text-space-primary dark:text-space-primary flex-1 font-mono text-sm">{{ $user['id'] }}</span>
                        </div>
                        @if ($user['home_planet_id'])
                            <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                                <span
                                    class="w-32 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                                >HOME_PLANET</span>
                                <span
                                    class="text-space-secondary dark:text-space-secondary flex-1 font-mono">{{ $user['home_planet_name'] ?? $user['home_planet_id'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Back Button -->
                <div class="dark:border-border-dark border-t border-gray-200 px-8 py-6">
                    <x-button
                        href="{{ route('dashboard') }}"
                        variant="primary"
                        size="md"
                    >
                        Back to Dashboard
                    </x-button>
                </div>
            </div>
        </div>
    @else
        <!-- Error state: user data not loaded -->
        <x-form-card
            title="Account Information"
            headerSeparated
            shadow="shadow-lg"
            padding="px-8 py-6"
        >
            @if ($error)
                <x-alert
                    type="error"
                    :message="$error"
                    :showPrompt="false"
                />
            @else
                <x-alert
                    type="error"
                    message="Failed to load user data. Please try refreshing the page."
                    :showPrompt="false"
                />
            @endif

            <div class="mt-6">
                <x-button
                    wire:click="loadUser"
                    variant="primary"
                    size="md"
                >
                    Retry
                </x-button>
                <x-button
                    href="{{ route('dashboard') }}"
                    variant="ghost"
                    size="md"
                    class="ml-4"
                >
                    Back to Dashboard
                </x-button>
            </div>
        </x-form-card>
    @endif
</x-container>
