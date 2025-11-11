@props([
    'bootMessages' => [],
    'terminalBooted' => false,
    'showPrompt' => true,
    'pollMethod' => null, // Méthode Livewire à appeler pour le polling
])

<div class="mb-8 font-mono">
    @if($showPrompt)
        <div class="{{ $terminalBooted && count($bootMessages) > 3 ? 'fade-out-boot-message' : '' }}">
            <x-terminal-prompt command="boot_terminal" />
        </div>
    @endif
    
    <!-- Boot Messages -->
    <div class="space-y-1 mb-4">
        @foreach($bootMessages as $index => $message)
            @php
                $shouldFadeOut = $terminalBooted && $index < max(0, count($bootMessages) - 4);
            @endphp
            <div class="text-sm {{ str_contains($message, '[OK]') ? 'text-space-primary dark:text-space-primary' : (str_contains($message, '[ERROR]') ? 'text-error dark:text-error' : 'text-gray-500 dark:text-gray-500') }} {{ $shouldFadeOut ? 'fade-out-boot-message' : '' }}" style="animation-delay: {{ $index * 0.1 }}s;">
                {{ $message }}
            </div>
        @endforeach
        @if(!$terminalBooted && $pollMethod)
            <div class="text-sm text-space-primary dark:text-space-primary animate-pulse" wire:poll.400ms="{{ $pollMethod }}">
                <span class="inline-block w-2 h-4 bg-space-primary dark:bg-space-primary">_</span>
            </div>
        @endif
    </div>
</div>




