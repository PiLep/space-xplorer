<div class="relative inline-flex items-center gap-2" wire:poll.30s="refresh">
    <a
        href="{{ route('notifications') }}"
        wire:navigate
        class="relative text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light transition-colors font-mono text-sm inline-flex items-center gap-2 cursor-pointer"
        aria-label="Notifications"
    >
        <span>> NOTIFICATIONS</span>

        <!-- Badge Count -->
        @if($this->unreadCount > 0)
            <span class="bg-error dark:bg-error text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center min-w-[1.25rem] {{ $this->unreadCount > 9 ? 'px-1' : '' }}">
                {{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}
            </span>
        @endif
    </a>
</div>
