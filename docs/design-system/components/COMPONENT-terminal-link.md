# COMPONENT-Terminal Link

## Vue d'Ensemble

Le composant Terminal Link est un composant réutilisable pour créer des liens avec style terminal. Il est utilisé dans les interfaces terminal pour créer des liens d'action avec format de commande terminal (ex: `> REGISTER_NEW_USER`).

**Quand l'utiliser** :
- Liens dans les interfaces terminal
- Actions avec style de commande terminal
- Navigation dans les contextes terminal

## Design

### Apparence

Le composant Terminal Link utilise :
- Style monospace (`font-mono`)
- Couleur secondary (`text-space-secondary`) avec effet hover
- Soulignement pour indiquer le lien
- Bordure supérieure optionnelle pour séparer visuellement
- Format de commande terminal avec préfixe `>`

### États

#### Default
État par défaut avec couleur secondary et soulignement.

#### Hover
Le lien change de couleur vers `text-space-secondary-light` au survol pour indiquer l'interactivité.

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `href` | string | **requis** | URL du lien (ex: `route('register')`) |
| `text` | string | **requis** | Texte du lien avec format terminal (ex: `"> REGISTER_NEW_USER"`) |
| `marginTop` | string | `'mt-8'` | Classe Tailwind pour la marge supérieure (`mt-8`, `mt-2`, etc.) |
| `showBorder` | boolean | `true` | Afficher la bordure supérieure pour séparer visuellement |

### Structure HTML

```blade
<x-terminal-link
    href="{{ route('register') }}"
    text="> REGISTER_NEW_USER"
/>
```

**Rendu** :
```html
<div class="mt-8 pt-6 border-t border-gray-200 dark:border-border-dark">
    <a href="/register" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-mono text-sm underline">
        > REGISTER_NEW_USER
    </a>
</div>
```

## Code d'Implémentation

### Blade Component

**Fichier** : `resources/views/components/terminal-link.blade.php`

```blade
@props([
    'href', // URL du lien (ex: route('register'))
    'text', // Texte du lien (ex: "> REGISTER_NEW_USER")
    'marginTop' => 'mt-8', // Margin top personnalisable
    'showBorder' => true, // Afficher la bordure supérieure
])

<div class="{{ $marginTop }}@if($showBorder) pt-6 border-t border-gray-200 dark:border-border-dark@endif">
    <a href="{{ $href }}" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-mono text-sm underline">
        {{ $text }}
    </a>
</div>
```

## Exemples d'Utilisation

### Lien Terminal Standard

```blade
<x-terminal-link
    href="{{ route('register') }}"
    text="> REGISTER_NEW_USER"
/>
```

### Lien sans Bordure

```blade
<x-terminal-link
    href="{{ route('login') }}"
    text="> LOGIN_EXISTING_USER"
    :showBorder="false"
/>
```

### Lien avec Espacement Personnalisé

```blade
<x-terminal-link
    href="{{ route('dashboard') }}"
    text="> RETURN_TO_DASHBOARD"
    marginTop="mt-4"
/>
```

### Dans un Contexte Terminal Complet

```blade
<div class="font-mono">
    <x-terminal-message message="[INFO] New user? Create an account:" />
    <x-terminal-link
        href="{{ route('register') }}"
        text="> REGISTER_NEW_USER"
        marginTop="mt-2"
        :showBorder="false"
    />
</div>
```

## Classes Tailwind Utilisées

### Structure
- `mt-8` (par défaut) : Marge supérieure standard
- `pt-6` : Padding supérieur (si bordure activée)
- `border-t` : Bordure supérieure
- `border-gray-200 dark:border-border-dark` : Couleur de la bordure

### Lien
- `font-mono` : Police monospace pour le style terminal
- `text-sm` : Taille de texte petite
- `underline` : Soulignement pour indiquer le lien
- `text-space-secondary` : Couleur du lien (secondary)
- `hover:text-space-secondary-light` : Couleur au survol
- `dark:text-space-secondary` : Couleur en mode sombre
- `dark:hover:text-space-secondary-light` : Couleur au survol en mode sombre

## Accessibilité

- Le lien est correctement sémantique avec la balise `<a>`
- Le soulignement indique clairement que c'est un lien
- Les couleurs respectent les standards de contraste WCAG 2.1
- Le format terminal avec `>` facilite la reconnaissance visuelle

## Notes de Design

- **Style terminal** : Utilise la police monospace et le format de commande pour créer l'ambiance terminal
- **Cohérence** : Utilise les couleurs du design system (secondary) pour maintenir la cohérence visuelle
- **Flexibilité** : Bordure optionnelle pour s'adapter à différents contextes
- **Interactivité** : L'effet hover indique clairement l'interactivité du lien

---

**Référence** : Voir **[DESIGN-SYSTEM.md](./DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

