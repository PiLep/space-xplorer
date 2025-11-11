# Design System - Animations & Transitions

## Vue d'Ensemble

Les animations et transitions de Stellar sont subtiles et fonctionnelles, créant une expérience utilisateur fluide sans distraire de l'immersion dans l'univers spatial. Des effets spéciaux comme les scanlines et les lueurs évoquent l'esthétique des écrans CRT des vaisseaux spatiaux.

## Durées

### Fast (150ms)

**Usage** : Interactions rapides, hover, focus

**Classe Tailwind** : `duration-150`

**Exemples** :
- Hover sur les boutons
- Focus sur les inputs
- Changements d'état rapides

```html
<button class="transition-colors duration-150 hover:bg-space-primary-dark">
  Action
</button>
```

### Normal (300ms)

**Usage** : Transitions standard, animations courantes

**Classe Tailwind** : `duration-300`

**Exemples** :
- Transitions de couleur
- Animations de fade
- Transitions de position

```html
<div class="transition-opacity duration-300 hover:opacity-80">
  Contenu
</div>
```

### Slow (500ms)

**Usage** : Animations importantes, transitions majeures

**Classe Tailwind** : `duration-500`

**Exemples** :
- Apparition de modals
- Transitions de page
- Animations complexes

```html
<div class="transition-all duration-500 ease-in-out">
  Contenu animé
</div>
```

## Easing Functions

### Default (ease-in-out)

**Usage** : Transitions standard

**Classe Tailwind** : `ease-in-out`

**Caractéristiques** :
- Démarrage et fin doux
- Parfait pour la plupart des transitions

```html
<div class="transition-all duration-300 ease-in-out">
  Contenu
</div>
```

### Enter (ease-out)

**Usage** : Animations d'entrée

**Classe Tailwind** : `ease-out`

**Caractéristiques** :
- Démarrage rapide, fin douce
- Parfait pour les éléments qui apparaissent

```html
<div class="transition-all duration-300 ease-out">
  Modal qui apparaît
</div>
```

### Exit (ease-in)

**Usage** : Animations de sortie

**Classe Tailwind** : `ease-in`

**Caractéristiques** :
- Démarrage doux, fin rapide
- Parfait pour les éléments qui disparaissent

```html
<div class="transition-all duration-300 ease-in">
  Élément qui disparaît
</div>
```

## Transitions Communes

### Couleur

**Usage** : Changements de couleur au hover/focus

```html
<!-- Bouton -->
<button class="bg-space-primary hover:bg-space-primary-dark transition-colors duration-150">
  Action
</button>

<!-- Lien -->
<a class="text-gray-400 hover:text-white transition-colors duration-150">
  Lien
</a>
```

### Opacité

**Usage** : Apparition/disparition, états disabled

```html
<!-- Disabled -->
<button class="opacity-50 hover:opacity-75 transition-opacity duration-150 disabled:opacity-50">
  Action
</button>

<!-- Fade -->
<div class="opacity-0 hover:opacity-100 transition-opacity duration-300">
  Contenu qui apparaît au hover
</div>
```

### Transform

**Usage** : Déplacements, rotations, scale

```html
<!-- Scale au hover -->
<button class="hover:scale-105 transition-transform duration-150">
  Bouton
</button>

<!-- Translation -->
<div class="hover:-translate-y-1 transition-transform duration-150">
  Card qui se soulève
</div>
```

### Toutes les Propriétés

**Usage** : Animations complexes

```html
<div class="transition-all duration-300 ease-in-out hover:bg-surface-medium hover:scale-105">
  Contenu avec plusieurs transitions
</div>
```

## Effets Spéciaux

### Scanlines (Effet CRT)

**Usage** : Évoquer les écrans CRT des vaisseaux spatiaux

**Implémentation CSS** :

```css
.scanlines {
  position: relative;
}

.scanlines::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: repeating-linear-gradient(
    0deg,
    rgba(0, 0, 0, 0.15),
    rgba(0, 0, 0, 0.15) 1px,
    transparent 1px,
    transparent 2px
  );
  pointer-events: none;
  z-index: 1;
}
```

**Usage** :
```html
<div class="scanlines bg-surface-dark p-6">
  Interface de vaisseau spatial
</div>
```

### Glow (Lueur)

**Usage** : Mettre en évidence des éléments importants

**Implémentation CSS** :

```css
.glow-primary {
  box-shadow: 0 0 10px rgba(0, 255, 136, 0.3),
              0 0 20px rgba(0, 255, 136, 0.2),
              0 0 30px rgba(0, 255, 136, 0.1);
}

.glow-secondary {
  box-shadow: 0 0 10px rgba(0, 170, 255, 0.3),
              0 0 20px rgba(0, 170, 255, 0.2),
              0 0 30px rgba(0, 170, 255, 0.1);
}
```

**Usage** :
```html
<button class="bg-space-primary glow-primary">
  Action importante
</button>
```

### Text Glow

**Usage** : Texte avec lueur

**Implémentation CSS** :

```css
.text-glow-primary {
  text-shadow: 0 0 10px rgba(0, 255, 136, 0.5),
               0 0 20px rgba(0, 255, 136, 0.3),
               0 0 30px rgba(0, 255, 136, 0.2);
}
```

**Usage** :
```html
<h1 class="text-space-primary text-glow-primary">
  Titre avec lueur
</h1>
```

## Animations de Chargement

### Spinner

**Usage** : Indicateur de chargement

```html
<div class="flex justify-center items-center">
  <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-space-primary"></div>
</div>
```

**Variantes** :
```html
<!-- Petit -->
<div class="animate-spin rounded-full h-6 w-6 border-b-2 border-space-primary"></div>

<!-- Moyen -->
<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-space-primary"></div>

<!-- Grand -->
<div class="animate-spin rounded-full h-12 w-12 border-b-2 border-space-primary"></div>
```

### Pulse

**Usage** : Animation de pulsation

```html
<div class="animate-pulse bg-surface-medium rounded-lg h-4 w-32"></div>
```

### Fade In

**Usage** : Apparition en fondu

```html
<div class="animate-fade-in opacity-0">
  Contenu qui apparaît
</div>
```

**CSS personnalisé** :
```css
@keyframes fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out forwards;
}
```

## États Interactifs

### Hover

**Spécifications** :
- Durée : 150ms
- Easing : ease-in-out
- Propriétés : couleur, opacité, transform

**Exemples** :
```html
<!-- Bouton -->
<button class="bg-space-primary hover:bg-space-primary-dark transition-colors duration-150">
  Action
</button>

<!-- Card -->
<div class="bg-surface-dark hover:bg-surface-medium transition-colors duration-150 cursor-pointer">
  Card interactive
</div>

<!-- Lien -->
<a class="text-gray-400 hover:text-white transition-colors duration-150">
  Lien
</a>
```

### Focus

**Spécifications** :
- Contour visible avec couleur primary
- Épaisseur : 2px
- Offset : 2px
- Durée : 150ms

**Exemples** :
```html
<input class="focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black transition-all duration-150">
```

### Active

**Spécifications** :
- Scale légèrement réduit (0.98)
- Durée : 100ms

**Exemples** :
```html
<button class="active:scale-98 transition-transform duration-100">
  Bouton
</button>
```

### Disabled

**Spécifications** :
- Opacité : 50%
- Cursor : not-allowed
- Pas d'interaction

**Exemples** :
```html
<button class="disabled:opacity-50 disabled:cursor-not-allowed">
  Bouton désactivé
</button>
```

## Animations de Page

### Page Transition

**Usage** : Transitions entre pages

```html
<div class="transition-opacity duration-300 ease-out">
  Contenu de la page
</div>
```

### Modal Apparition

**Usage** : Ouverture de modals

```html
<!-- Overlay -->
<div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300">
  <!-- Modal -->
  <div class="bg-surface-dark rounded-lg p-6 transform transition-all duration-300 ease-out">
    Contenu du modal
  </div>
</div>
```

## Performance

### Bonnes Pratiques

1. **Utiliser transform et opacity** : Ces propriétés sont optimisées par le GPU
2. **Éviter les animations sur width/height** : Utiliser transform à la place
3. **Limiter les animations** : Ne pas surcharger avec trop d'animations
4. **Respecter prefers-reduced-motion** : Désactiver les animations si nécessaire

### Prefers Reduced Motion

```css
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
```

## Exemples Complets

### Bouton avec Animations

```html
<button class="
  bg-space-primary 
  hover:bg-space-primary-dark 
  active:scale-98
  focus:outline-none 
  focus:ring-2 
  focus:ring-space-primary 
  focus:ring-offset-2 
  focus:ring-offset-space-black
  disabled:opacity-50 
  disabled:cursor-not-allowed
  transition-all 
  duration-150 
  ease-in-out
  px-6 
  py-3 
  rounded-lg
">
  Action
</button>
```

### Card Interactive

```html
<div class="
  bg-surface-dark 
  hover:bg-surface-medium 
  hover:-translate-y-1
  hover:shadow-lg
  transition-all 
  duration-300 
  ease-in-out
  rounded-lg 
  p-6 
  cursor-pointer
">
  <h3 class="text-white mb-4">Titre</h3>
  <p class="text-gray-400">Description</p>
</div>
```

## Notes de Design

- **Subtilité** : Les animations doivent être subtiles et ne pas distraire
- **Fonctionnalité** : Chaque animation a un but (feedback utilisateur, hiérarchie)
- **Performance** : Optimiser pour la performance (GPU, prefers-reduced-motion)
- **Cohérence** : Utiliser les durées et easing de manière cohérente

---

**Référence** : Voir **[DESIGN-SYSTEM.md](./DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

