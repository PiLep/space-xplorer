<x-container
    variant="standard"
    class="py-8"
>
    <div class="font-mono">
        <!-- Header -->
        <div class="mb-6">
            <x-terminal-prompt command="access_inbox" />
            <x-terminal-message
                message="[OK] Inbox system accessed"
                marginBottom="mb-2"
            />
            <x-terminal-message
                :message="'[INFO] Unread messages: ' . $this->unreadCount"
                marginBottom="mb-4"
            />
        </div>

        <!-- Filters -->
        <div class="mb-6 flex flex-wrap items-center gap-3">
            <x-terminal-prompt command="filter_messages" />
            <div class="flex gap-2">
                @if ($filter === 'all')
                    <x-button
                        wire:click="filterMessages('all')"
                        variant="primary"
                        size="sm"
                        terminal="true"
                    >
                        ALL
                    </x-button>
                @else
                    <x-button
                        wire:click="filterMessages('all')"
                        variant="ghost"
                        size="sm"
                        terminal="true"
                    >
                        ALL
                    </x-button>
                @endif

                @if ($filter === 'unread')
                    <x-button
                        wire:click="filterMessages('unread')"
                        variant="primary"
                        size="sm"
                        terminal="true"
                    >
                        UNREAD
                    </x-button>
                @else
                    <x-button
                        wire:click="filterMessages('unread')"
                        variant="ghost"
                        size="sm"
                        terminal="true"
                    >
                        UNREAD
                    </x-button>
                @endif

                @if ($filter === 'read')
                    <x-button
                        wire:click="filterMessages('read')"
                        variant="primary"
                        size="sm"
                        terminal="true"
                    >
                        READ
                    </x-button>
                @else
                    <x-button
                        wire:click="filterMessages('read')"
                        variant="ghost"
                        size="sm"
                        terminal="true"
                    >
                        READ
                    </x-button>
                @endif

                @if ($filter === 'trash')
                    <x-button
                        wire:click="filterMessages('trash')"
                        variant="primary"
                        size="sm"
                        terminal="true"
                    >
                        TRASH
                    </x-button>
                @else
                    <x-button
                        wire:click="filterMessages('trash')"
                        variant="ghost"
                        size="sm"
                        terminal="true"
                    >
                        TRASH
                    </x-button>
                @endif
            </div>
        </div>

        @if ($this->messages && $this->messages->count() > 0)
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Messages List -->
                <div class="space-y-2 lg:col-span-1">
                    <x-terminal-prompt command="list_messages" />
                    @foreach ($this->messages as $message)
                        <div
                            wire:click="selectMessage('{{ $message->id }}')"
                            class="border-border-dark dark:border-border-dark {{ $selectedMessageId === $message->id ? 'bg-space-primary/20 border-space-primary' : 'hover:bg-surface-dark dark:hover:bg-surface-dark' }} cursor-pointer rounded border p-4 transition-colors"
                        >
                            <div class="mb-2 flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="mb-1 flex items-center gap-2">
                                        @if (!$message->is_read)
                                            <span class="bg-space-primary inline-block h-2 w-2 rounded-full"></span>
                                        @endif
                                        @if ($message->is_important)
                                            <span class="text-warning">!</span>
                                        @endif
                                        <span class="text-space-primary dark:text-space-primary truncate font-semibold">
                                            {{ $message->subject }}
                                        </span>
                                    </div>
                                    <div class="truncate text-xs text-gray-500 dark:text-gray-500">
                                        {{ $message->created_at->format('Y-m-d H:i') }}
                                    </div>
                                </div>
                            </div>
                            <div class="truncate text-xs text-gray-400 dark:text-gray-400">
                                {{ \Illuminate\Support\Str::limit(strip_tags($message->content), 60) }}
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    @if ($this->messages->hasPages())
                        <div class="mt-4">
                            {{ $this->messages->links() }}
                        </div>
                    @endif
                </div>

                <!-- Message Content -->
                <div class="lg:col-span-2">
                    @if ($selectedMessage)
                        <x-terminal-prompt command="display_message" />
                        <div class="border-border-dark dark:border-border-dark space-y-4 rounded border p-6">
                            <!-- Message Header -->
                            <div class="border-border-dark dark:border-border-dark border-b pb-4">
                                <div class="mb-2 flex items-start justify-between gap-4">
                                    <h2 class="text-space-primary dark:text-space-primary text-xl font-bold">
                                        {{ $selectedMessage->subject }}
                                    </h2>
                                    <div class="flex gap-2">
                                        @if ($filter === 'trash')
                                            {{-- Actions for trashed messages --}}
                                            <x-button
                                                wire:click="restoreMessage('{{ $selectedMessage->id }}')"
                                                variant="ghost"
                                                size="sm"
                                                terminal="true"
                                            >
                                                RESTORE
                                            </x-button>
                                            <x-button
                                                wire:click="forceDeleteMessage('{{ $selectedMessage->id }}')"
                                                wire:confirm="Are you sure you want to permanently delete this message? This action cannot be undone."
                                                variant="danger"
                                                size="sm"
                                                terminal="true"
                                            >
                                                DELETE PERMANENTLY
                                            </x-button>
                                        @else
                                            {{-- Actions for normal messages --}}
                                            @if ($selectedMessage->is_read)
                                                <x-button
                                                    wire:click="markAsUnread('{{ $selectedMessage->id }}')"
                                                    variant="ghost"
                                                    size="sm"
                                                    terminal="true"
                                                >
                                                    MARK UNREAD
                                                </x-button>
                                            @else
                                                <x-button
                                                    wire:click="markAsRead('{{ $selectedMessage->id }}')"
                                                    variant="ghost"
                                                    size="sm"
                                                    terminal="true"
                                                >
                                                    MARK READ
                                                </x-button>
                                            @endif
                                            <x-button
                                                wire:click="deleteMessage('{{ $selectedMessage->id }}')"
                                                variant="danger"
                                                size="sm"
                                                terminal="true"
                                            >
                                                DELETE
                                            </x-button>
                                        @endif
                                    </div>
                                </div>
                                <div class="space-y-1 text-sm text-gray-500 dark:text-gray-500">
                                    <div>From: <span
                                            class="text-space-secondary dark:text-space-secondary">STELLAR</span></div>
                                    <div>Date: {{ $selectedMessage->created_at->format('Y-m-d H:i:s') }}</div>
                                    <div>Type: <span
                                            class="text-space-primary dark:text-space-primary">{{ strtoupper($selectedMessage->type) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Message Content -->
                            <div class="prose prose-invert max-w-none !text-left">
                                <pre class="m-0 whitespace-pre-wrap p-0 font-mono text-sm leading-relaxed text-gray-300 dark:text-gray-300">{{ $selectedMessage->content }}</pre>
                            </div>
                        </div>
                    @else
                        <x-terminal-message message="[INFO] Select a message to view its content" />
                    @endif
                </div>
            </div>
        @else
            <x-terminal-message message="[INFO] No messages found" />
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
