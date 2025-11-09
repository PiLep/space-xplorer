# COMPONENT-Page Header

## Vue d'Ensemble

Le composant Page Header est un composant réutilisable pour créer des en-têtes de page standardisés avec titre et description optionnelle. Il assure une cohérence visuelle pour les en-têtes de page dans toute l'application.

**Quand l'utiliser** :
- En-têtes de page avec titre principal
- Pages nécessitant une description ou un sous-titre
- Sections principales nécessitant un header standardisé

## Design

### Apparence

Le composant Page Header utilise :
- Titre H1 avec taille `text-4xl` et poids `font-bold`
- Description optionnelle avec taille `text-lg`
- Espacement configurable via la prop `marginBottom`
- Couleurs adaptées au mode clair/sombre

### Structure

```
[Titre H1]
[Description optionnelle]
```

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `title` | string | **requis** | Titre principal de la page |
| `description` | string | `null` | Description ou sous-titre optionnel |
| `marginBottom` | string | `'mb-8'` | Classe Tailwind pour la marge inférieure (`mb-8`, `mb-6`, etc.) |

### Structure HTML

```blade
<x-page-header
    title="Profile Settings"
    description="Manage your account information and preferences."
/>
```

**Rendu** :
```html
<div class="mb-8">
    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
        Profile Settings
    </h1>
    <p class="text-lg text-gray-600 dark:text-gray-400">
        Manage your account information and preferences.
    </p>
</div>
```

## Code d'Implémentation

### Blade Component

**Fichier** : `resources/views/components/page-header.blade.php`

```blade
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
```

## Exemples d'Utilisation

### Header avec Titre et Description

```blade
<x-page-header
    title="Profile Settings"
    description="Manage your account information and preferences."
/>
```

### Header avec Titre Seulement

```blade
<x-page-header title="Dashboard" />
```

### Header avec Espacement Personnalisé

```blade
<x-page-header
    title="Settings"
    description="Configure your application settings"
    marginBottom="mb-6"
/>
```

## Classes Tailwind Utilisées

### Structure
- `mb-8` (par défaut) : Marge inférieure standard
- `mb-2` : Marge entre titre et description

### Titre
- `text-4xl` : Taille de texte très grande (2.25rem / 36px)
- `font-bold` : Poids de police en gras
- `text-gray-900 dark:text-white` : Couleur du texte (noir en mode clair, blanc en mode sombre)

### Description
- `text-lg` : Taille de texte grande (1.125rem / 18px)
- `text-gray-600 dark:text-gray-400` : Couleur du texte (gris moyen)

## Accessibilité

- Utilise la balise sémantique `<h1>` pour le titre principal
- Structure hiérarchique claire avec titre et description
- Les couleurs respectent les standards de contraste WCAG 2.1
- Support du mode sombre pour une meilleure lisibilité

## Notes de Design

- **Cohérence** : Assure une présentation uniforme des en-têtes de page
- **Hiérarchie** : Le titre H1 crée une hiérarchie claire pour les lecteurs d'écran
- **Flexibilité** : Description optionnelle pour s'adapter à différents contextes
- **Espacement** : Marge configurable pour s'adapter au layout de la page

---

**Référence** : Voir **[DESIGN-SYSTEM.md](./DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

