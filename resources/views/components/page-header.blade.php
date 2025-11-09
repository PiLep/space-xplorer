@props([
    'title', // Titre de la page (ex: "Profile Settings")
    'description' => null, // Description optionnelle
    'marginBottom' => 'mb-8', // Margin bottom personnalisable
])

<div class="{{ $marginBottom }}">
    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
        {{ $title }}
    </h1>
    @if($description)
        <p class="text-lg text-gray-600 dark:text-gray-400">
            {{ $description }}
        </p>
    @endif
</div>

