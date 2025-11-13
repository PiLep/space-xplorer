@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'wireLoading' => null,
    'wireLoadingText' => null,
    'terminal' => false,
    'ariaLabel' => null,
    'wireClick' => null,
    'confirmText' => null,
    'normalText' => null,
])

@php
    $baseClasses = 'font-bold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-space-black cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed';
    
    $variantClasses = [
        'primary' => 'bg-space-primary hover:bg-space-primary-dark text-space-black focus:ring-space-primary glow-primary hover:glow-primary',
        'secondary' => 'bg-space-secondary hover:bg-space-secondary-dark text-white focus:ring-space-secondary glow-secondary hover:glow-secondary',
        'danger' => 'bg-error hover:bg-error-dark text-white focus:ring-error',
        'ghost' => 'bg-gray-200 hover:bg-gray-300 dark:bg-surface-medium dark:hover:bg-surface-dark text-gray-900 dark:text-white border border-gray-300 dark:border-border-dark dark:hover:glow-border-primary',
    ];
    
    $sizeClasses = [
        'sm' => 'py-2 px-4 text-sm',
        'md' => 'py-2 px-4 text-base',
        'lg' => 'py-3 px-6 text-base',
    ];
    
    $terminalClasses = $terminal ? 'font-mono text-sm' : '';
    
    $classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size] . ' ' . $terminalClasses;
    
    $customClass = $attributes->get('class', '');
    if ($customClass) {
        $classes = $classes . ' ' . $customClass;
        $attributes = $attributes->except('class');
    }
    
    $attributes = $attributes->merge(['class' => $classes]);
    $attributes = $attributes->merge(['type' => $type]);
    
    if ($disabled) {
        $attributes = $attributes->merge(['disabled' => true]);
    }
    
    if ($ariaLabel) {
        $attributes = $attributes->merge(['aria-label' => $ariaLabel]);
    }
    
    $displayNormalText = $normalText ?? (string) $slot;
    $displayConfirmText = $confirmText ?? 'CLICK AGAIN TO CONFIRM';
@endphp

<div 
    x-data="{ 
        needsConfirmation: false,
        handleClick(event) {
            event.preventDefault();
            event.stopPropagation();
            
            if (!this.needsConfirmation) {
                this.needsConfirmation = true;
                setTimeout(() => {
                    this.needsConfirmation = false;
                }, 5000);
            } else {
                this.needsConfirmation = false;
                @if($wireClick)
                    $wire.call('{{ $wireClick }}');
                @endif
            }
        },
        resetConfirmation() {
            this.needsConfirmation = false;
        }
    }"
    x-on:click.outside="resetConfirmation()"
    class="inline-block"
>
    <button 
        {{ $attributes }}
        x-on:click.prevent="handleClick($event)"
        :class="needsConfirmation ? 'animate-pulse ring-2 ring-error ring-offset-2 ring-offset-white dark:ring-offset-space-black' : ''"
        @if($disabled)
            disabled
        @endif
    >
        @if($wireLoading)
            <span wire:loading.remove wire:target="{{ $wireLoading }}">
                <span x-show="!needsConfirmation" style="display: inline;">{{ $displayNormalText }}</span>
                <span x-show="needsConfirmation" style="display: none;">{{ $displayConfirmText }}</span>
            </span>
            <span wire:loading wire:target="{{ $wireLoading }}" aria-live="polite">
                {{ $wireLoadingText ?? 'Loading...' }}
            </span>
        @else
            <span x-show="!needsConfirmation" style="display: inline;">{{ $displayNormalText }}</span>
            <span x-show="needsConfirmation" style="display: none;">{{ $displayConfirmText }}</span>
        @endif
    </button>
</div>
