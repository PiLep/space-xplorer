# COMPONENT-Form Link

## Vue d'Ensemble

Le composant Form Link est un composant réutilisable pour créer des liens de navigation entre formulaires (ex: "Don't have an account? Register"). Il assure une cohérence visuelle et une expérience utilisateur uniforme pour la navigation entre les formulaires d'authentification et autres formulaires.

**Quand l'utiliser** :
- Liens de navigation entre formulaires d'authentification (login/register)
- Liens vers d'autres formulaires dans l'application
- Navigation contextuelle dans les formulaires

## Design

### Apparence

Le composant Form Link utilise :
- Texte secondaire (`text-gray-600 dark:text-gray-400`) pour le texte descriptif
- Couleur secondary (`text-space-secondary`) pour le lien avec effet hover
- Centrage du texte pour une présentation équilibrée
- Espacement configurable via la prop `marginTop`

### États

#### Default
État par défaut avec texte secondaire et lien en couleur secondary.

#### Hover
Le lien change de couleur vers `text-space-secondary-light` au survol pour indiquer l'interactivité.

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `text` | string | **requis** | Texte descriptif avant le lien (ex: "Don't have an account?") |
| `linkText` | string | **requis** | Texte du lien cliquable (ex: "Register") |
| `href` | string | **requis** | URL du lien (ex: `route('register')`) |
| `marginTop` | string | `'mt-4'` | Classe Tailwind pour la marge supérieure (`mt-4`, `mt-6`, etc.) |

### Structure HTML

```blade
<x-form-link
    text="Don't have an account?"
    linkText="Register"
    :href="route('register')"
/>
```

**Rendu** :
```html
<div class="mt-4 text-center">
    <p class="text-sm text-gray-600 dark:text-gray-400">
        Don't have an account?
        <a href="/register" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-bold">
            Register
        </a>
    </p>
</div>
```

## Code d'Implémentation

### Blade Component

**Fichier** : `resources/views/components/form-link.blade.php`

```blade
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
```

## Exemples d'Utilisation

### Lien de Navigation Standard

```blade
<x-form-link
    text="Don't have an account?"
    linkText="Register"
    :href="route('register')"
/>
```

### Lien avec Espacement Personnalisé

```blade
<x-form-link
    text="Already have an account?"
    linkText="Sign in"
    :href="route('login')"
    marginTop="mt-6"
/>
```

### Dans un Formulaire Complet

```blade
<x-form-card title="Sign In">
    <form wire:submit="login">
        <!-- Champs du formulaire -->
        <x-form-input ... />
        
        <!-- Bouton de soumission -->
        <x-button type="submit" variant="primary">
            Sign In
        </x-button>
        
        <!-- Lien de navigation -->
        <x-form-link
            text="Don't have an account?"
            linkText="Register"
            :href="route('register')"
        />
    </form>
</x-form-card>
```

## Classes Tailwind Utilisées

### Structure
- `text-center` : Centrage du texte
- `mt-4` (par défaut) : Marge supérieure standard

### Texte
- `text-sm` : Taille de texte petite
- `text-gray-600 dark:text-gray-400` : Couleur du texte descriptif

### Lien
- `text-space-secondary` : Couleur du lien (secondary)
- `hover:text-space-secondary-light` : Couleur au survol
- `dark:text-space-secondary` : Couleur en mode sombre
- `dark:hover:text-space-secondary-light` : Couleur au survol en mode sombre
- `font-bold` : Poids de police en gras

## Accessibilité

- Le lien est correctement sémantique avec la balise `<a>`
- Le texte descriptif fournit un contexte clair
- Les couleurs respectent les standards de contraste WCAG 2.1
- Le lien est facilement identifiable grâce au style bold et à la couleur secondary

## Notes de Design

- **Cohérence** : Utilise les couleurs du design system (secondary) pour maintenir la cohérence visuelle
- **Lisibilité** : Le texte descriptif en gris et le lien en secondary créent une hiérarchie visuelle claire
- **Interactivité** : L'effet hover sur le lien indique clairement l'interactivité
- **Espacement** : La marge supérieure par défaut (`mt-4`) sépare visuellement le lien du contenu précédent

---

**Référence** : Voir **[DESIGN-SYSTEM.md](./DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

