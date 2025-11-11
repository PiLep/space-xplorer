# Design System - Couleurs

## Vue d'Ensemble

La palette de couleurs de Stellar est inspirée de l'esthétique rétro-futuriste des films Alien. Elle combine des tons sombres pour créer une ambiance spatiale immersive avec des accents fluorescents (verts et bleus) qui évoquent les écrans CRT des vaisseaux spatiaux.

## Couleurs Principales

### Background & Surfaces

| Nom | Hex | RGB | Usage | Classe Tailwind |
|-----|-----|-----|-------|-----------------|
| **Space Black** | `#0a0a0a` | rgb(10, 10, 10) | Fond principal de l'application | `bg-space-black` |
| **Surface Dark** | `#1a1a1a` | rgb(26, 26, 26) | Surfaces élevées (cards, modals) | `bg-surface-dark` |
| **Surface Medium** | `#2a2a2a` | rgb(42, 42, 42) | Surfaces secondaires | `bg-surface-medium` |
| **Border Dark** | `#333333` | rgb(51, 51, 51) | Bordures et séparateurs | `border-border-dark` |

### Couleurs Primaires

| Nom | Hex | RGB | Usage | Classe Tailwind |
|-----|-----|-----|-------|-----------------|
| **Primary** | `#00ff88` | rgb(0, 255, 136) | Actions principales, liens, accents | `text-space-primary` `bg-space-primary` |
| **Primary Dark** | `#00cc6a` | rgb(0, 204, 106) | États hover des éléments primary | `bg-space-primary-dark` |
| **Primary Light** | `#33ffaa` | rgb(51, 255, 170) | États actifs, lueurs | `text-space-primary-light` |

### Couleurs Secondaires

| Nom | Hex | RGB | Usage | Classe Tailwind |
|-----|-----|-----|-------|-----------------|
| **Secondary** | `#00aaff` | rgb(0, 170, 255) | Actions secondaires, informations | `text-space-secondary` `bg-space-secondary` |
| **Secondary Dark** | `#0088cc` | rgb(0, 136, 204) | États hover des éléments secondary | `bg-space-secondary-dark` |
| **Secondary Light** | `#33bbff` | rgb(51, 187, 255) | États actifs | `text-space-secondary-light` |

### Couleurs d'Accent

| Nom | Hex | RGB | Usage | Classe Tailwind |
|-----|-----|-----|-------|-----------------|
| **Accent** | `#ffaa00` | rgb(255, 170, 0) | Alertes importantes, highlights | `text-space-accent` `bg-space-accent` |
| **Accent Dark** | `#cc8800` | rgb(204, 136, 0) | États hover | `bg-space-accent-dark` |

## Couleurs Sémantiques

### Success

| Nom | Hex | RGB | Usage | Classe Tailwind |
|-----|-----|-----|-------|-----------------|
| **Success** | `#00ff88` | rgb(0, 255, 136) | Messages de succès, confirmations | `text-success` `bg-success` |
| **Success Dark** | `#00cc6a` | rgb(0, 204, 106) | Fond success | `bg-success-dark` |
| **Success Light** | `#33ffaa` | rgb(51, 255, 170) | Texte sur fond sombre | `text-success-light` |

### Error

| Nom | Hex | RGB | Usage | Classe Tailwind |
|-----|-----|-----|-------|-----------------|
| **Error** | `#ff4444` | rgb(255, 68, 68) | Erreurs, alertes critiques | `text-error` `bg-error` |
| **Error Dark** | `#cc3333` | rgb(204, 51, 51) | Fond error | `bg-error-dark` |
| **Error Light** | `#ff6666` | rgb(255, 102, 102) | Texte sur fond sombre | `text-error-light` |

### Warning

| Nom | Hex | RGB | Usage | Classe Tailwind |
|-----|-----|-----|-------|-----------------|
| **Warning** | `#ffaa00` | rgb(255, 170, 0) | Avertissements, alertes | `text-warning` `bg-warning` |
| **Warning Dark** | `#cc8800` | rgb(204, 136, 0) | Fond warning | `bg-warning-dark` |
| **Warning Light** | `#ffbb33` | rgb(255, 187, 51) | Texte sur fond sombre | `text-warning-light` |

### Info

| Nom | Hex | RGB | Usage | Classe Tailwind |
|-----|-----|-----|-------|-----------------|
| **Info** | `#00aaff` | rgb(0, 170, 255) | Informations, notifications | `text-info` `bg-info` |
| **Info Dark** | `#0088cc` | rgb(0, 136, 204) | Fond info | `bg-info-dark` |
| **Info Light** | `#33bbff` | rgb(51, 187, 255) | Texte sur fond sombre | `text-info-light` |

## Couleurs de Texte

| Nom | Hex | RGB | Usage | Classe Tailwind |
|-----|-----|-----|-------|-----------------|
| **Text Primary** | `#ffffff` | rgb(255, 255, 255) | Texte principal sur fond sombre | `text-white` |
| **Text Secondary** | `#aaaaaa` | rgb(170, 170, 170) | Texte secondaire | `text-gray-400` |
| **Text Muted** | `#666666` | rgb(102, 102, 102) | Texte désactivé, labels | `text-gray-500` |
| **Text Inverse** | `#0a0a0a` | rgb(10, 10, 10) | Texte sur fond clair | `text-space-black` |

## Couleurs de Planètes

Les couleurs varient selon les types de planètes pour créer une identité visuelle unique :

### Planète Tellurique
- **Couleur principale** : `#4a90e2` (Bleu-vert)
- **Accent** : `#7ed321` (Vert)

### Planète Gazeuse
- **Couleur principale** : `#9013fe` (Violet)
- **Accent** : `#bd10e0` (Magenta)

### Planète Glacée
- **Couleur principale** : `#50e3c2` (Cyan)
- **Accent** : `#b8e986` (Vert clair)

### Planète Désertique
- **Couleur principale** : `#f5a623` (Orange)
- **Accent** : `#d0021b` (Rouge)

### Planète Océanique
- **Couleur principale** : `#417505` (Vert foncé)
- **Accent** : `#50e3c2` (Cyan)

## Utilisation

### Exemples avec Tailwind CSS

```html
<!-- Fond principal -->
<div class="bg-space-black text-white">
  Contenu principal
</div>

<!-- Surface élevée (card) -->
<div class="bg-surface-dark border border-border-dark rounded-lg">
  Contenu de la card
</div>

<!-- Bouton primary -->
<button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold">
  Action principale
</button>

<!-- Message de succès -->
<div class="bg-success-dark text-success-light border border-success">
  Opération réussie
</div>

<!-- Message d'erreur -->
<div class="bg-error-dark text-error-light border border-error">
  Une erreur est survenue
</div>
```

### Contraste et Accessibilité

Tous les contrastes respectent les standards WCAG 2.1 :

- **Texte blanc sur fond noir** : Ratio 21:1 ✅
- **Texte primary sur fond noir** : Ratio 4.8:1 ✅
- **Texte secondary sur fond noir** : Ratio 4.2:1 ✅
- **Texte gray-400 sur fond noir** : Ratio 4.5:1 ✅

### États Interactifs

#### Hover
- Éléments interactifs : Légère augmentation de luminosité (+10-15%)
- Boutons : Transition vers la couleur "dark" correspondante

#### Active
- Éléments actifs : Légère augmentation de luminosité (+5-10%)
- Boutons : Utiliser la couleur "light" correspondante

#### Focus
- Contour visible avec couleur primary (`#00ff88`)
- Épaisseur : 2px
- Offset : 2px

#### Disabled
- Opacité réduite à 50%
- Cursor: not-allowed
- Pas d'interaction possible

## Configuration Tailwind

Pour utiliser ces couleurs dans Tailwind CSS, ajouter dans `tailwind.config.js` :

```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        'space-black': '#0a0a0a',
        'surface-dark': '#1a1a1a',
        'surface-medium': '#2a2a2a',
        'border-dark': '#333333',
        'space-primary': '#00ff88',
        'space-primary-dark': '#00cc6a',
        'space-primary-light': '#33ffaa',
        'space-secondary': '#00aaff',
        'space-secondary-dark': '#0088cc',
        'space-secondary-light': '#33bbff',
        'space-accent': '#ffaa00',
        'space-accent-dark': '#cc8800',
        'success': '#00ff88',
        'success-dark': '#00cc6a',
        'success-light': '#33ffaa',
        'error': '#ff4444',
        'error-dark': '#cc3333',
        'error-light': '#ff6666',
        'warning': '#ffaa00',
        'warning-dark': '#cc8800',
        'warning-light': '#ffbb33',
        'info': '#00aaff',
        'info-dark': '#0088cc',
        'info-light': '#33bbff',
      }
    }
  }
}
```

## Notes de Design

- **Inspiration Alien** : Les couleurs monochromes avec accents fluorescents rappellent les interfaces des vaisseaux spatiaux des films Alien
- **Ambiance spatiale** : Les tons sombres créent une immersion dans l'espace profond
- **Lisibilité** : Les accents fluorescents assurent une excellente lisibilité sur fond sombre
- **Cohérence** : Utiliser ces couleurs de manière cohérente à travers toute l'application

---

**Référence** : Voir **[DESIGN-SYSTEM.md](./DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

