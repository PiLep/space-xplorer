@props([
    'bootMessages' => [],
    'terminalBooted' => false,
    'showPrompt' => true,
    'pollMethod' => null, // Méthode Livewire à appeler pour le polling
])

<div class="mb-8 font-mono">
    @if ($showPrompt)
        <div class="{{ $terminalBooted && count($bootMessages) > 3 ? 'fade-out-boot-message' : '' }}">
            <x-terminal-prompt command="boot_terminal" />
        </div>
    @endif

    <!-- Boot Messages -->
    <div class="mb-4 space-y-1">
        @foreach ($bootMessages as $index => $message)
            @php
                $shouldFadeOut = $terminalBooted && $index < max(0, count($bootMessages) - 4);
            @endphp
            <div
                wire:key="boot-message-{{ $index }}"
                class="{{ str_contains($message, '[OK]') ? 'text-space-primary dark:text-space-primary' : (str_contains($message, '[ERROR]') ? 'text-error dark:text-error' : 'text-gray-500 dark:text-gray-500') }} {{ $shouldFadeOut ? 'fade-out-boot-message' : '' }} text-sm"
                style="animation-delay: {{ $index * 0.1 }}s;"
            >
                {{ $message }}
            </div>
        @endforeach
        @if (!$terminalBooted && $pollMethod)
            <div
                class="text-space-primary dark:text-space-primary animate-pulse text-sm"
                wire:poll.400ms="{{ $pollMethod }}"
            >
                <span class="bg-space-primary dark:bg-space-primary inline-block h-4 w-2">_</span>
            </div>
        @endif
    </div>
</div>
