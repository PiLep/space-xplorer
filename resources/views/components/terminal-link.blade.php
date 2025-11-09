@props([
    'href', // URL du lien (ex: route('register'))
    'text', // Texte du lien (ex: "> REGISTER_NEW_USER")
    'marginTop' => 'mt-8', // Margin top personnalisable
    'showBorder' => true, // Afficher la bordure supérieure
])

@php
    $borderClasses = $showBorder ? 'pt-6 border-t border-gray-200 dark:border-border-dark' : '';
    $containerClasses = trim($marginTop . ' ' . $borderClasses);
@endphp

<div class="{{ $containerClasses }}">
    <a href="{{ $href }}" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-mono text-sm underline transition-colors">
        {{ $text }}
    </a>
</div>

