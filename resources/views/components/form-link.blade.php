@props([
    'text', // Texte avant le lien (ex: "Don't have an account?")
    'linkText', // Texte du lien (ex: "Register")
    'href', // URL du lien (ex: route('register'))
    'marginTop' => 'mt-4', // Margin top personnalisable
])

<div class="{{ $marginTop }} text-center">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        {{ $text }}
        <a href="{{ $href }}" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-bold">
            {{ $linkText }}
        </a>
    </p>
</div>

