<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    @if ($loading)
        <x-loading-spinner
            variant="simple"
            size="md"
            :showMessage="false"
        />
    @elseif ($user)
        <!-- Profile Card -->
        <div class="bg-white dark:bg-surface-dark shadow-lg rounded-lg overflow-hidden mb-8 terminal-border-simple scan-effect hologram">
            <div class="flex flex-col">
                <!-- Profile Header -->
                <div class="px-8 py-6 border-b border-gray-200 dark:border-border-dark">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono">{{ strtoupper($user['name']) }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-lg uppercase tracking-wider font-mono">USER_PROFILE</p>
                </div>

                <!-- Error Message -->
                @if ($error)
                    <div class="px-8 py-4 border-b border-gray-200 dark:border-border-dark">
                        <x-alert
                            type="error"
                            :message="$error"
                            :showPrompt="false"
                        />
                    </div>
                @endif

                <!-- User Information -->
                <div class="px-8 py-6 border-t border-gray-200 dark:border-border-dark">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 dark:text-glow-subtle font-mono">SYSTEM_DATA</h3>
                    <div class="space-y-3 font-mono">
                        <div class="flex items-baseline border-b border-gray-300 dark:border-border-dark pb-2">
                            <span class="text-sm text-gray-500 dark:text-gray-500 uppercase tracking-wider w-32 flex-shrink-0">NAME</span>
                            <span class="text-space-primary dark:text-space-primary flex-1">{{ $user['name'] }}</span>
                        </div>
                        <div class="flex items-baseline border-b border-gray-300 dark:border-border-dark pb-2">
                            <span class="text-sm text-gray-500 dark:text-gray-500 uppercase tracking-wider w-32 flex-shrink-0">EMAIL</span>
                            <span class="text-space-primary dark:text-space-primary flex-1">{{ $user['email'] }}</span>
                        </div>
                        <div class="flex items-baseline border-b border-gray-300 dark:border-border-dark pb-2">
                            <span class="text-sm text-gray-500 dark:text-gray-500 uppercase tracking-wider w-32 flex-shrink-0">USER_ID</span>
                            <span class="text-space-primary dark:text-space-primary flex-1 font-mono text-sm">{{ $user['id'] }}</span>
                        </div>
                        @if ($user['home_planet_id'])
                            <div class="flex items-baseline border-b border-gray-300 dark:border-border-dark pb-2">
                                <span class="text-sm text-gray-500 dark:text-gray-500 uppercase tracking-wider w-32 flex-shrink-0">HOME_PLANET</span>
                                <span class="text-space-secondary dark:text-space-secondary flex-1 font-mono">{{ $user['home_planet_name'] ?? $user['home_planet_id'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Back Button -->
                <div class="px-8 py-6 border-t border-gray-200 dark:border-border-dark">
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
</div>
