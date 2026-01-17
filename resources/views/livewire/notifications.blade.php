<x-container
    variant="standard"
    class="py-8"
>
    <div class="font-mono">
        <!-- Header - Style terminal -->
        <div class="mb-6">
            <x-terminal-prompt command="access_notifications" />
            @if ($this->unreadCount > 0)
                <x-terminal-message
                    :message="'[ALERT] ' . $this->unreadCount . ' notification' . ($this->unreadCount > 1 ? 's' : '') . ' non lue' . ($this->unreadCount > 1 ? 's' : '') . ' détectée' . ($this->unreadCount > 1 ? 's' : '')"
                    marginBottom="mb-2"
                />
            @else
                <x-terminal-message
                    message="[OK] Aucune notification non lue"
                    marginBottom="mb-2"
                />
            @endif
            <div class="mt-4 flex gap-2">
                @if ($this->unreadCount > 0)
                    <x-button
                        wire:click="markAllAsRead"
                        variant="secondary"
                        size="sm"
                        terminal="true"
                    >
                        MARK_ALL_READ
                    </x-button>
                @endif
                @if ($this->readCount > 0)
                    <x-button
                        wire:click="deleteAllRead"
                        variant="danger"
                        size="sm"
                        terminal="true"
                    >
                        DELETE_ALL_READ
                    </x-button>
                @endif
            </div>
        </div>

        <!-- Filters - Style terminal -->
        <div class="mb-6">
            <x-terminal-prompt command="filter_notifications" />
            <div class="flex flex-wrap items-center gap-2 mt-2 border-b border-border-dark dark:border-border-dark pb-2">
                @if ($filter === 'all')
                    <button
                        wire:click="updateFilter('all')"
                        type="button"
                        class="px-4 py-2 text-sm font-semibold border-b-2 border-space-secondary dark:border-space-secondary text-space-secondary dark:text-space-secondary transition-colors cursor-pointer"
                    >
                        ALL
                    </button>
                @else
                    <button
                        wire:click="updateFilter('all')"
                        type="button"
                        class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 hover:text-space-secondary dark:hover:text-space-secondary transition-colors cursor-pointer"
                    >
                        ALL
                    </button>
                @endif

                @if ($filter === 'unread')
                    <button
                        wire:click="updateFilter('unread')"
                        type="button"
                        class="px-4 py-2 text-sm font-semibold border-b-2 border-space-secondary dark:border-space-secondary text-space-secondary dark:text-space-secondary transition-colors cursor-pointer"
                    >
                        UNREAD
                    </button>
                @else
                    <button
                        wire:click="updateFilter('unread')"
                        type="button"
                        class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 hover:text-space-secondary dark:hover:text-space-secondary transition-colors cursor-pointer"
                    >
                        UNREAD
                    </button>
                @endif

                @if ($filter === 'read')
                    <button
                        wire:click="updateFilter('read')"
                        type="button"
                        class="px-4 py-2 text-sm font-semibold border-b-2 border-space-secondary dark:border-space-secondary text-space-secondary dark:text-space-secondary transition-colors cursor-pointer"
                    >
                        READ
                    </button>
                @else
                    <button
                        wire:click="updateFilter('read')"
                        type="button"
                        class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 hover:text-space-secondary dark:hover:text-space-secondary transition-colors cursor-pointer"
                    >
                        READ
                    </button>
                @endif

                <!-- Type Filter -->
                <div class="ml-auto flex items-center gap-2">
                    @if ($typeFilter === 'all')
                        <button
                            wire:click="updateTypeFilter('all')"
                            type="button"
                            class="px-3 py-1 text-xs bg-space-secondary/20 dark:bg-space-secondary/20 text-space-secondary dark:text-space-secondary rounded transition-colors cursor-pointer"
                        >
                            ALL_TYPES
                        </button>
                    @else
                        <button
                            wire:click="updateTypeFilter('all')"
                            type="button"
                            class="px-3 py-1 text-xs text-gray-500 dark:text-gray-400 hover:bg-space-secondary/10 dark:hover:bg-space-secondary/10 rounded transition-colors cursor-pointer"
                        >
                            ALL_TYPES
                        </button>
                    @endif

                    @if ($typeFilter === 'message_important')
                        <button
                            wire:click="updateTypeFilter('message_important')"
                            type="button"
                            class="px-3 py-1 text-xs bg-space-secondary/20 dark:bg-space-secondary/20 text-space-secondary dark:text-space-secondary rounded transition-colors cursor-pointer"
                        >
                            MSG
                        </button>
                    @else
                        <button
                            wire:click="updateTypeFilter('message_important')"
                            type="button"
                            class="px-3 py-1 text-xs text-gray-500 dark:text-gray-400 hover:bg-space-secondary/10 dark:hover:bg-space-secondary/10 rounded transition-colors cursor-pointer"
                        >
                            MSG
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Notifications List - Style terminal alertes -->
        @if ($this->notifications->count() > 0)
            <x-terminal-prompt command="list_notifications" />
            <div class="space-y-2 mt-2">
                @foreach ($this->notifications as $notification)
                    @php
                        $typeStyles = [
                            'message_important' => [
                                'code' => 'MSG',
                                'prefix' => '[MSG]',
                                'borderClass' => 'border-l-space-primary',
                                'bgClass' => 'bg-space-primary/5 dark:bg-space-primary/5',
                                'textClass' => 'text-space-primary dark:text-space-primary',
                                'shadowClass' => 'shadow-space-primary/20 dark:shadow-space-primary/20',
                            ],
                            'ship_assigned' => [
                                'code' => 'SHIP',
                                'prefix' => '[SHIP]',
                                'borderClass' => 'border-l-space-secondary',
                                'bgClass' => 'bg-space-secondary/5 dark:bg-space-secondary/5',
                                'textClass' => 'text-space-secondary dark:text-space-secondary',
                                'shadowClass' => 'shadow-space-secondary/20 dark:shadow-space-secondary/20',
                            ],
                            'resource_added' => [
                                'code' => 'RES',
                                'prefix' => '[RES]',
                                'borderClass' => 'border-l-warning',
                                'bgClass' => 'bg-warning/5 dark:bg-warning/5',
                                'textClass' => 'text-warning dark:text-warning',
                                'shadowClass' => 'shadow-warning/20 dark:shadow-warning/20',
                            ],
                        ];
                        $style = $typeStyles[$notification->type] ?? [
                            'code' => 'INFO',
                            'prefix' => '[INFO]',
                            'borderClass' => 'border-l-gray-600',
                            'bgClass' => 'bg-surface-medium/30 dark:bg-surface-medium/30',
                            'textClass' => 'text-gray-400 dark:text-gray-400',
                            'shadowClass' => 'shadow-gray-600/20 dark:shadow-gray-600/20',
                        ];
                        $statusPrefix = !$notification->is_read ? '[UNREAD]' : '[READ]';
                    @endphp
                    <div class="group block {{ $style['borderClass'] }} border-l-4 {{ $style['bgClass'] }} border-r border-t border-b border-border-dark dark:border-border-dark {{ !$notification->is_read ? 'glow-subtle shadow-lg ' . $style['shadowClass'] : '' }} hover:shadow-md transition-all p-3 font-mono">
                        <div class="flex items-start gap-3">
                            <!-- Status & Type Code -->
                            <div class="flex-shrink-0 flex flex-col gap-1">
                                <span class="text-xs font-bold {{ $style['textClass'] }}">
                                    {{ $statusPrefix }}
                                </span>
                                <span class="text-xs font-bold {{ $style['textClass'] }}">
                                    {{ $style['prefix'] }}
                                </span>
                            </div>

                            <!-- Notification Content -->
                            <a
                                href="{{ $this->getNotificationUrl($notification) }}"
                                wire:click="markAsRead('{{ $notification->id }}')"
                                wire:navigate
                                class="flex-1 min-w-0 cursor-pointer"
                            >
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <h3 class="text-sm font-bold {{ !$notification->is_read ? 'text-space-secondary dark:text-space-secondary' : 'text-gray-300 dark:text-gray-300' }} truncate">
                                        {{ $notification->title }}
                                    </h3>
                                    @if (!$notification->is_read)
                                        <span class="flex-shrink-0 w-2 h-2 bg-space-secondary dark:bg-space-secondary rounded-full mt-1.5 animate-pulse"></span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-400 mb-1 line-clamp-2">
                                    {{ $notification->message }}
                                </p>
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500">
                                    <span>[{{ $notification->created_at->format('Y-m-d H:i:s') }}]</span>
                                    <span class="text-gray-600 dark:text-gray-600">|</span>
                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </a>

                            <!-- Delete Button - Visible only on hover -->
                            <div class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                <x-button
                                    wire:click="delete('{{ $notification->id }}')"
                                    variant="ghost"
                                    size="sm"
                                    terminal="true"
                                    ariaLabel="Supprimer la notification"
                                    class="!py-1 !px-2 text-xs text-gray-500 hover:text-error dark:text-gray-400 dark:hover:text-error"
                                >
                                    ×
                                </x-button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($this->notifications->hasPages())
                <div class="mt-6">
                    {{ $this->notifications->links() }}
                </div>
            @endif
        @else
            <x-terminal-prompt command="list_notifications" />
            <x-terminal-message
                message="[INFO] Aucune notification trouvée dans la base de données"
                marginBottom="mb-0"
            />
        @endif

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <x-alert
                type="success"
                :message="session('success')"
                class="mt-4"
            />
        @endif

        @if (session()->has('error'))
            <x-alert
                type="error"
                :message="session('error')"
                class="mt-4"
            />
        @endif
    </div>
</x-container>
