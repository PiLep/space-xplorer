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
                        <div class="flex-shrink-0 relative">
                            @if ($user['avatar_generating'] ?? false)
                                <!-- Avatar is being generated -->
                                <div class="h-24 w-24 rounded-lg overflow-hidden">
                                    <x-scan-placeholder type="avatar" :label="'SCANNING_AVATAR'" class="h-full w-full" />
                                </div>
                            @elseif ($user['avatar_url'] ?? null)
                                <div class="relative h-24 w-24" x-data="{ showButton: false }" @mouseenter="showButton = true" @mouseleave="showButton = false">
                                    <img
                                        src="{{ $user['avatar_url'] }}"
                                        alt="{{ $user['name'] }}'s avatar"
                                        class="border-space-primary dark:border-space-primary terminal-border-simple h-24 w-24 rounded-lg border-2 object-cover shadow-lg avatar-image"
                                        onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                                        wire:key="avatar-{{ $user['id'] }}-{{ md5($user['avatar_url'] ?? '') }}"
                                    />
                                    @php
                                        $initials = strtoupper(substr($user['name'], 0, 1) . (strpos($user['name'], ' ') !== false ? substr($user['name'], strpos($user['name'], ' ') + 1, 1) : ''));
                                    @endphp
                                    <div
                                        class="dark:border-border-dark terminal-border-simple hidden flex h-24 w-24 flex-shrink-0 items-center justify-center rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-gray-200 dark:bg-gray-800 avatar-placeholder">
                                        <span class="font-mono text-xl font-bold text-gray-600 dark:text-gray-300">{{ $initials }}</span>
                                    </div>
                                    <!-- Regenerate Profile Button - Visible on hover of image -->
                                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-lg opacity-0 transition-opacity" 
                                         :class="{ 'opacity-100': showButton }"
                                         x-show="showButton"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0">
                                        <button
                                            wire:click="openAvatarModal"
                                            class="bg-space-primary hover:bg-space-primary-dark text-white rounded-full p-3 shadow-lg transition-colors focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2"
                                            title="Regenerate bio-physical profile"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <!-- No avatar, show initials placeholder -->
                                <div class="relative h-24 w-24" x-data="{ showButton: false }" @mouseenter="showButton = true" @mouseleave="showButton = false">
                                    @php
                                        $initials = strtoupper(substr($user['name'], 0, 1) . (strpos($user['name'], ' ') !== false ? substr($user['name'], strpos($user['name'], ' ') + 1, 1) : ''));
                                    @endphp
                                    <div
                                        class="dark:border-border-dark terminal-border-simple flex h-24 w-24 flex-shrink-0 items-center justify-center rounded-lg border-2 border-gray-300 dark:border-gray-600 bg-gray-200 dark:bg-gray-800 avatar-placeholder">
                                        <span class="font-mono text-xl font-bold text-gray-600 dark:text-gray-300">{{ $initials }}</span>
                                    </div>
                                    <!-- Regenerate Profile Button - Visible on hover -->
                                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-lg opacity-0 transition-opacity" 
                                         :class="{ 'opacity-100': showButton }"
                                         x-show="showButton"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0">
                                        <button
                                            wire:click="openAvatarModal"
                                            class="bg-space-primary hover:bg-space-primary-dark text-white rounded-full p-3 shadow-lg transition-colors focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2"
                                            title="Regenerate bio-physical profile"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <!-- User Info -->
                        <div class="flex-1">
                            <h2
                                class="dark:text-glow-subtle mb-2 font-mono text-3xl font-bold text-gray-900 dark:text-white flex items-center justify-between">
                                <span>{{ strtoupper($user['name']) }}</span><span class="text-gray-400 dark:text-gray-400">{{ $user['matricule'] }}</span></h2>
                            <p class="font-mono text-lg uppercase tracking-wider text-gray-600 dark:text-gray-400">
                                EMPLOYEE_STATUS</p>
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
                        <div class="flex items-baseline pb-2">
                            <span
                                class="w-32 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >NAME</span>
                            <span class="text-space-primary dark:text-space-primary flex-1">{{ $user['name'] }}</span>
                        </div>
                        <div class="flex items-baseline pb-2">
                            <span
                                class="w-32 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >MATRICULE</span>
                            <span class="text-space-primary dark:text-space-primary flex-1 font-mono">{{ $user['matricule'] }}</span>
                        </div>
                        <div class="flex items-baseline pb-2">
                            <span
                                class="w-32 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >EMAIL</span>
                            <span class="text-space-primary dark:text-space-primary flex-1">{{ $user['email'] }}</span>
                        </div>
                        @if ($user['home_planet_id'])
                            <div class="flex items-baseline pb-2">
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

        <!-- Avatar Selection Modal -->
        @if ($showAvatarModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-black/50 flex items-center justify-center z-50"
                 wire:click.self="closeAvatarModal"
                 x-data="{ show: @entangle('showAvatarModal') }"
                 x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 role="dialog"
                 aria-modal="true">
                <div class="bg-surface-dark dark:bg-surface-dark border border-border-dark dark:border-border-dark rounded-lg p-6 max-w-4xl w-full terminal-border-simple mx-4 max-h-[90vh] overflow-y-auto"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold text-white font-mono">
                            REGENERATE_BIO_PROFILE
                        </h2>
                        <button wire:click="closeAvatarModal"
                                class="text-gray-400 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black rounded"
                                aria-label="Close">
                            <span class="font-mono text-2xl leading-none">×</span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="mb-6">
                        @if ($loadingAvatars)
                            <div class="flex items-center justify-center py-12">
                                <x-loading-spinner
                                    variant="simple"
                                    size="md"
                                    :showMessage="false"
                                />
                            </div>
                        @elseif ($avatarMessage && !$selectingAvatar)
                            <x-alert
                                :type="str_contains($avatarMessage, '[OK]') ? 'success' : 'error'"
                                :message="$avatarMessage"
                                :showPrompt="false"
                            />
                        @elseif (count($availableAvatars) > 0)
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                @foreach ($availableAvatars as $avatar)
                                    <button
                                        wire:key="avatar-{{ $avatar['id'] }}"
                                        wire:click="selectAvatar('{{ $avatar['id'] }}')"
                                        wire:loading.attr="disabled"
                                        class="group relative w-24 h-24 rounded-lg overflow-hidden border-2 border-gray-600 dark:border-gray-700 hover:border-space-primary dark:hover:border-space-primary transition-all focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black {{ $selectingAvatar ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}"
                                        title="{{ $avatar['description'] ?? 'Regenerate with this profile' }}">
                                        <img
                                            src="{{ $avatar['file_url'] }}"
                                            alt="{{ $avatar['description'] ?? 'Avatar' }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform"
                                            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                                        />
                                        <div class="hidden absolute inset-0 flex items-center justify-center bg-gray-800">
                                            <span class="font-mono text-xs text-gray-400">ERROR</span>
                                        </div>
                                        @if ($selectingAvatar)
                                            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                                <x-loading-spinner
                                                    variant="simple"
                                                    size="sm"
                                                    :showMessage="false"
                                                />
                                            </div>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <p class="text-gray-400 font-mono">[WAIT] No bio-profiles available for regeneration.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end gap-4">
                        <x-button
                            wire:click="closeAvatarModal"
                            variant="ghost"
                            size="sm"
                            terminal
                            :disabled="$selectingAvatar"
                        >
                            > CLOSE
                        </x-button>
                    </div>
                </div>
            </div>
        @endif
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
