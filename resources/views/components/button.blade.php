@props([
    'variant' => 'primary', // primary, secondary, danger, ghost
    'size' => 'md', // sm, md, lg
    'type' => 'button', // button, submit, reset
    'href' => null, // Si fourni, rend un <a> au lieu d'un <button>
    'disabled' => false,
    'wireLoading' => null, // Nom de la méthode Livewire pour wire:loading (ex: "login")
    'wireLoadingText' => null, // Texte à afficher pendant le chargement
    'terminal' => false, // Style terminal (font-mono, texte avec >)
    'ariaLabel' => null, // Label ARIA pour accessibilité (boutons icon-only)
])

@php
    $baseClasses = 'font-bold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-space-black cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed';
    
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
    
    $tag = $href ? 'a' : 'button';
    $attributes = $attributes->merge(['class' => $classes]);
    
    if ($href) {
        $attributes = $attributes->merge(['href' => $href]);
        // Ajouter wire:navigate par défaut pour les liens internes (sauf si explicitement désactivé)
        // Vérifier si wire:navigate est déjà présent dans les attributs
        $hasWireNavigate = false;
        foreach ($attributes->getAttributes() as $key => $value) {
            if (str_starts_with($key, 'wire:navigate')) {
                $hasWireNavigate = true;
                break;
            }
        }
        if (!$hasWireNavigate) {
            // Ajouter wire:navigate comme attribut HTML directement
            $attributes = $attributes->merge(['wire:navigate' => '']);
        }
    } else {
        $attributes = $attributes->merge(['type' => $type]);
    }
    
    if ($disabled) {
        $attributes = $attributes->merge(['disabled' => true]);
    }
    
    if ($wireLoading) {
        $attributes = $attributes->merge(['wire:loading.attr' => 'disabled']);
    }
    
    if ($ariaLabel) {
        $attributes = $attributes->merge(['aria-label' => $ariaLabel]);
    }
@endphp

<{{ $tag }} {{ $attributes }}@if($wireLoading) wire:loading.attr="aria-busy" @endif>
    @if($wireLoading)
        <span wire:loading.remove wire:target="{{ $wireLoading }}">
            {{ $slot }}
        </span>
        <span wire:loading wire:target="{{ $wireLoading }}" aria-live="polite">
            {{ $wireLoadingText ?? 'Loading...' }}
        </span>
    @else
        {{ $slot }}
    @endif
</{{ $tag }}>

