# COMPONENT-Scan Placeholder

## Vue d'Ensemble

Le composant Scan Placeholder affiche un indicateur visuel de scan en cours dans le style Alien/sci-fi de Space Xplorer. Il est utilisé pour indiquer qu'une image, vidéo ou avatar est en cours de génération par le système.

**Quand l'utiliser** :
- Génération d'image de planète en cours
- Génération de vidéo de planète en cours
- Génération d'avatar utilisateur en cours
- Toute opération de scan/génération visuelle asynchrone

## Design

### Apparence

Le Scan Placeholder combine :
- Fond sombre avec dégradé (gray-900 → gray-800 → gray-900)
- Lignes de scan animées (effet de balayage vertical)
- Grille de fond subtile (pattern de grille sci-fi)
- Icône de scan centrale animée (spinner avec point pulsant)
- Texte en style terminal (vert avec message "SCANNING_*")
- Points de progression animés
- Coins avec brackets décoratifs (style terminal)

### Structure

```
┌─────────────────────────┐
│ ┌─┐                   ┌─┐│
│ │ │  [Spinner animé]   │ ││
│ │ │  SCANNING_IMAGE    │ ││
│ │ │  PLEASE_WAIT...    │ ││
│ │ │  [•] [•] [•]       │ ││
│ └─┘                   └─┘│
│  [Lignes de scan animées] │
│  [Grille de fond]         │
└─────────────────────────┘
```

### Variantes

#### Image (Défaut)
**Usage** : Génération d'image de planète

**Label** : `SCANNING_IMAGE` ou personnalisé

**Exemple** :
```blade
<x-scan-placeholder type="image" />
```

#### Video
**Usage** : Génération de vidéo de planète

**Label** : `SCANNING_VIDEO` ou personnalisé

**Exemple** :
```blade
<x-scan-placeholder type="video" />
```

#### Avatar
**Usage** : Génération d'avatar utilisateur

**Label** : `SCANNING_AVATAR` ou personnalisé

**Exemple** :
```blade
<x-scan-placeholder type="avatar" />
```

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `type` | string | `'image'` | Type de scan : `image`, `video`, `avatar` |
| `label` | string | `null` | Label personnalisé (remplace le label par défaut selon le type) |

### Labels par Défaut

| Type | Label par Défaut |
|------|------------------|
| `image` | `SCANNING_IMAGE` |
| `video` | `SCANNING_VIDEO` |
| `avatar` | `SCANNING_AVATAR` |
| Autre | `SCANNING` |

### Classes Tailwind

```blade
<div class="relative flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 overflow-hidden">
    <!-- Lignes de scan animées -->
    <div class="absolute inset-0 opacity-20">
        <div class="scan-line h-full w-full bg-gradient-to-b from-transparent via-green-400 to-transparent animate-scan"></div>
    </div>
    
    <!-- Grille de fond -->
    <div class="absolute inset-0 opacity-10" style="background-image: ..."></div>
    
    <!-- Contenu central -->
    <div class="relative z-10 flex flex-col items-center justify-center gap-4 p-8 text-center">
        <!-- Spinner et texte -->
    </div>
    
    <!-- Coins avec brackets -->
</div>
```

### Animations CSS

```css
@keyframes scan {
    0% {
        transform: translateY(-100%);
    }
    100% {
        transform: translateY(100%);
    }
}

.animate-scan {
    animation: scan 2s linear infinite;
}
```

## Code d'Implémentation

### Composant Blade

**Fichier** : `resources/views/components/scan-placeholder.blade.php`

```blade
@props(['type' => 'image', 'label' => null])

@php
    $defaultLabel = match($type) {
        'image' => 'SCANNING_IMAGE',
        'video' => 'SCANNING_VIDEO',
        'avatar' => 'SCANNING_AVATAR',
        default => 'SCANNING',
    };
    $displayLabel = $label ?? $defaultLabel;
@endphp

<div class="relative flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 overflow-hidden">
    <!-- Animated scan lines -->
    <div class="absolute inset-0 opacity-20">
        <div class="scan-line h-full w-full bg-gradient-to-b from-transparent via-green-400 to-transparent animate-scan"></div>
    </div>
    
    <!-- Grid pattern -->
    <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(rgba(0, 255, 136, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 255, 136, 0.1) 1px, transparent 1px); background-size: 20px 20px;"></div>
    
    <!-- Center content -->
    <div class="relative z-10 flex flex-col items-center justify-center gap-4 p-8 text-center">
        <!-- Animated scanning icon -->
        <div class="relative">
            <div class="h-16 w-16 rounded-full border-4 border-green-400 border-t-transparent animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="h-8 w-8 rounded-full border-2 border-green-400 animate-pulse"></div>
            </div>
        </div>
        
        <!-- Text -->
        <div class="font-mono text-sm uppercase tracking-wider text-green-400">
            <div class="mb-1">{{ $displayLabel }}</div>
            <div class="text-xs text-gray-400">PLEASE_WAIT...</div>
        </div>
        
        <!-- Progress dots -->
        <div class="flex gap-1">
            <div class="h-1 w-1 rounded-full bg-green-400 animate-pulse" style="animation-delay: 0s;"></div>
            <div class="h-1 w-1 rounded-full bg-green-400 animate-pulse" style="animation-delay: 0.2s;"></div>
            <div class="h-1 w-1 rounded-full bg-green-400 animate-pulse" style="animation-delay: 0.4s;"></div>
        </div>
    </div>
    
    <!-- Corner brackets -->
    <div class="absolute top-2 left-2 h-4 w-4 border-l-2 border-t-2 border-green-400"></div>
    <div class="absolute top-2 right-2 h-4 w-4 border-r-2 border-t-2 border-green-400"></div>
    <div class="absolute bottom-2 left-2 h-4 w-4 border-l-2 border-b-2 border-green-400"></div>
    <div class="absolute bottom-2 right-2 h-4 w-4 border-r-2 border-b-2 border-green-400"></div>
</div>
```

## Exemples d'Utilisation

### Exemple 1 : Image de Planète en Cours

```blade
@if ($planet->isImageGenerating())
    <x-scan-placeholder type="image" :label="'SCANNING_IMAGE: ' . strtoupper($planet->name)" class="h-64 w-full" />
@else
    <img src="{{ $planet->image_url }}" alt="{{ $planet->name }}" />
@endif
```

**Résultat** : Affiche le placeholder avec le label "SCANNING_IMAGE: PLANET_NAME"

### Exemple 2 : Vidéo de Planète en Cours

```blade
@if ($planet->isVideoGenerating())
    <x-scan-placeholder type="video" :label="'SCANNING_VIDEO: ' . strtoupper($planet->name)" class="h-64 w-full" />
@elseif ($planet->video_url)
    <video src="{{ $planet->video_url }}" autoplay loop muted></video>
@endif
```

**Résultat** : Affiche le placeholder avec le label "SCANNING_VIDEO: PLANET_NAME"

### Exemple 3 : Avatar Utilisateur en Cours

```blade
@if ($user->isAvatarGenerating())
    <div class="h-24 w-24 rounded-lg overflow-hidden">
        <x-scan-placeholder type="avatar" class="h-full w-full" />
    </div>
@elseif ($user->avatar_url)
    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" />
@endif
```

**Résultat** : Affiche le placeholder avec le label "SCANNING_AVATAR"

### Exemple 4 : Label Personnalisé

```blade
<x-scan-placeholder type="image" label="SCANNING_PLANETARY_DATA" />
```

**Résultat** : Affiche le placeholder avec le label personnalisé "SCANNING_PLANETARY_DATA"

### Exemple 5 : Dans Planet Card

```blade
@if ($planet->isVideoGenerating())
    <!-- Video is being generated -->
    <x-scan-placeholder type="video" :label="'SCANNING_VIDEO: ' . strtoupper($planet->name)" class="h-64 w-full md:h-full md:min-h-0 md:flex-1" />
@elseif ($planet->isImageGenerating() && !$videoUrl)
    <!-- Image is being generated (and no video available) -->
    <x-scan-placeholder type="image" :label="'SCANNING_IMAGE: ' . strtoupper($planet->name)" class="h-64 w-full md:h-full md:min-h-0 md:flex-1" />
@elseif ($videoUrl)
    <!-- Video available -->
    <video src="{{ $videoUrl }}" autoplay loop muted></video>
@else
    <!-- Image available -->
    <img src="{{ $finalImageUrl }}" alt="{{ $planet->name }}" />
@endif
```

## Intégration avec les Modèles

Le composant est utilisé en conjonction avec les méthodes des modèles :

### Planet Model

```php
// Vérifier si l'image est en cours de génération
$planet->isImageGenerating(); // bool

// Vérifier si la vidéo est en cours de génération
$planet->isVideoGenerating(); // bool

// Vérifier si l'image est disponible
$planet->hasImage(); // bool

// Vérifier si la vidéo est disponible
$planet->hasVideo(); // bool
```

### User Model

```php
// Vérifier si l'avatar est en cours de génération
$user->isAvatarGenerating(); // bool

// Vérifier si l'avatar est disponible
$user->hasAvatar(); // bool
```

## Responsive

Le composant s'adapte automatiquement :
- **Mobile** : Padding réduit (`p-4`)
- **Tablet** : Padding standard (`p-6`)
- **Desktop** : Padding généreux (`p-8`)

Les classes peuvent être personnalisées via la prop `class` pour s'adapter à différents contextes.

## Accessibilité

- Le texte permet aux lecteurs d'écran de comprendre l'état de génération
- Contraste de couleurs respecté (vert sur fond sombre)
- Support du mode sombre (couleurs adaptées)
- Animations CSS pures (pas de JavaScript requis)
- Indicateur visuel clair pour les utilisateurs

## Notes de Design

- **Style Alien** : Inspiré de l'esthétique Alien (1979) avec lignes de scan et grille
- **Couleurs** : Vert (`green-400`) pour l'indicateur de scan, cohérent avec le thème space
- **Animation** : Lignes de scan animées pour créer l'effet de balayage
- **Performance** : Animations CSS pures, pas de JavaScript requis
- **Cohérence** : Style terminal cohérent avec le reste de l'application
- **Flexibilité** : Labels personnalisables selon le contexte

## États de Génération

Le composant est affiché lorsque :
1. `image_generating` est `true` pour les planètes
2. `video_generating` est `true` pour les planètes
3. `avatar_generating` est `true` pour les utilisateurs

Ces statuts sont automatiquement gérés par les listeners d'événements :
- `GeneratePlanetImage` : Met à jour `image_generating`
- `GeneratePlanetVideo` : Met à jour `video_generating`
- `GenerateAvatar` : Met à jour `avatar_generating`

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

