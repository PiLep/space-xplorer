# Design System - Space Xplorer

## Vue d'Ensemble

Le design system de Space Xplorer définit l'identité visuelle, les composants réutilisables, et les principes de design pour créer une expérience utilisateur cohérente et immersive dans l'univers spatial. Inspiré de l'esthétique rétro-futuriste des films Alien, le design system combine une ambiance sombre et industrielle avec des accents fluorescents pour créer une interface qui évoque les vaisseaux spatiaux et les stations spatiales.

**Objectifs** :
- Créer une identité visuelle forte et reconnaissable
- Assurer la cohérence à travers toute l'application
- Faciliter le développement avec des composants réutilisables
- Offrir une expérience utilisateur immersive et intuitive
- Maintenir l'accessibilité et la performance

## Identité Visuelle

### Influences Design

- **Style rétro-futuriste inspiré des films Alien** : Esthétique industrielle, interfaces monochromes avec accents fluorescents, écrans CRT, typographies techniques
- Ambiance sombre et immersive pour évoquer l'espace profond
- Design fonctionnel et industriel rappelant les vaisseaux spatiaux
- Combinaison de l'esthétique des années 70-80 avec des technologies modernes

### Principes Fondamentaux

1. **Cohérence** : Maintenir une cohérence visuelle à travers toute l'application
2. **Hiérarchie** : Utiliser la typographie et l'espacement pour créer une hiérarchie claire
3. **Rétro-futurisme** : Inspirer le design des interfaces de vaisseaux spatiaux tout en restant moderne et fonctionnel
4. **Immersion** : Créer une atmosphère qui transporte l'utilisateur dans l'univers spatial
5. **Accessibilité** : Assurer l'accessibilité visuelle pour tous les utilisateurs
6. **Performance** : Optimiser les performances visuelles

## Palette de Couleurs

Voir **[DESIGN-SYSTEM-COLORS.md](./DESIGN-SYSTEM-COLORS.md)** pour la documentation complète des couleurs.

### Couleurs Principales

- **Background** : `#0a0a0a` (Noir profond, ambiance spatiale)
- **Surface** : `#1a1a1a` (Gris très foncé, surfaces élevées)
- **Primary** : `#00ff88` (Vert fluorescent, inspiré des écrans CRT)
- **Secondary** : `#00aaff` (Bleu fluorescent, accents)
- **Accent** : `#ffaa00` (Orange/Ambre, alertes importantes)

### Couleurs Sémantiques

- **Success** : `#00ff88` (Vert fluorescent)
- **Error** : `#ff4444` (Rouge, alertes critiques)
- **Warning** : `#ffaa00` (Orange/Ambre)
- **Info** : `#00aaff` (Bleu fluorescent)

## Typographie

Voir **[DESIGN-SYSTEM-TYPOGRAPHY.md](./DESIGN-SYSTEM-TYPOGRAPHY.md)** pour la documentation complète de la typographie.

### Familles de Polices

- **Primary** : Instrument Sans - Moderne, lisible, technique
- **Monospace** : 'Courier New', monospace - Pour les données techniques et interfaces

### Hiérarchie

- **H1** : 2.5rem (40px), font-bold, tracking-tight
- **H2** : 2rem (32px), font-bold, tracking-tight
- **H3** : 1.5rem (24px), font-semibold
- **Body** : 1rem (16px), font-normal
- **Caption** : 0.875rem (14px), font-normal

## Espacements & Grilles

Voir **[DESIGN-SYSTEM-SPACING.md](./DESIGN-SYSTEM-SPACING.md)** pour la documentation complète des espacements.

### Système d'Espacement

- Base : 4px
- Échelle : 4, 8, 12, 16, 24, 32, 48, 64, 96, 128

### Grilles

- **Desktop** : 12 colonnes, gutter 24px
- **Tablet** : 8 colonnes, gutter 16px
- **Mobile** : 4 colonnes, gutter 12px

## Composants

Voir **[DESIGN-SYSTEM-COMPONENTS.md](./DESIGN-SYSTEM-COMPONENTS.md)** pour la documentation complète des composants.

### Composants de Base

- **Boutons** : Variantes primary, secondary, danger, ghost
- **Formulaires** : Champs de saisie avec validation visuelle
- **Cards** : Conteneurs pour afficher les informations
- **Navigation** : Barre de navigation responsive
- **Modals** : Dialogs pour les interactions importantes
- **Badges** : Indicateurs de statut et labels
- **Alerts** : Messages d'alerte et notifications

## Animations & Transitions

Voir **[DESIGN-SYSTEM-ANIMATIONS.md](./DESIGN-SYSTEM-ANIMATIONS.md)** pour la documentation complète des animations.

### Durées

- **Fast** : 150ms
- **Normal** : 300ms
- **Slow** : 500ms

### Easing

- **Default** : ease-in-out
- **Enter** : ease-out
- **Exit** : ease-in

### Effets Spéciaux

- **Scanlines** : Effet de scanlines subtil pour évoquer les écrans CRT
- **Glow** : Lueur subtile autour des éléments importants
- **Hover** : Transitions fluides au survol

## Responsive Design

### Breakpoints

- **Mobile** : < 640px
- **Tablet** : 640px - 1024px
- **Desktop** : > 1024px

### Principes

- Mobile-first approach
- Grilles adaptatives selon la taille d'écran
- Navigation responsive avec menu hamburger sur mobile
- Typographie responsive (tailles ajustées selon l'écran)

## Accessibilité

### Contraste

- Ratio minimum : 4.5:1 pour le texte normal
- Ratio minimum : 3:1 pour le texte large (18px+ ou 14px+ bold)

### Focus States

- Contour visible avec couleur primary (`#00ff88`)
- Épaisseur : 2px
- Offset : 2px

### ARIA

- Utiliser les attributs ARIA appropriés
- Labels pour les éléments interactifs
- États pour les composants dynamiques

## Règles de Design

### Interdiction des Emojis

**Règle** : Les emojis sont strictement interdits dans le design de l'application.

**Raison** :
- Incohérence avec l'esthétique rétro-futuriste et industrielle
- Problèmes d'accessibilité et de compatibilité
- Manque de professionnalisme dans une interface spatiale

**Alternatives** :
- Utiliser des icônes SVG pour les éléments visuels
- Utiliser des symboles textuels (ex: "→", "•", "|")
- Utiliser uniquement la typographie et la couleur pour créer la hiérarchie
- Utiliser des badges ou labels textuels pour les indicateurs

**Exemples d'alternatives** :
```html
<!-- Au lieu d'emojis -->
<span class="text-space-primary font-mono text-sm">→</span>
<span class="text-space-primary font-mono text-sm">•</span>
<span class="text-space-primary font-mono text-sm">|</span>

<!-- Ou simplement utiliser la typographie -->
<h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Size</h4>
```

## Utilisation avec Tailwind CSS

Le design system est implémenté via Tailwind CSS avec des classes personnalisées. Voir la configuration Tailwind pour les couleurs, espacements, et autres tokens du design system.

### Classes Utilitaires

- Couleurs : `bg-space-black`, `text-space-primary`, etc.
- Espacements : Utiliser l'échelle standardisée (4, 8, 12, 16, 24, 32, 48, 64, 96, 128)
- Typographie : Classes de hiérarchie (`text-h1`, `text-h2`, etc.)

## Structure des Fichiers

```
docs/design-system/
├── DESIGN-SYSTEM.md (ce fichier)
├── DESIGN-SYSTEM-COLORS.md
├── DESIGN-SYSTEM-TYPOGRAPHY.md
├── DESIGN-SYSTEM-COMPONENTS.md
├── DESIGN-SYSTEM-SPACING.md
├── DESIGN-SYSTEM-ANIMATIONS.md
└── components/
    ├── COMPONENT-button.md
    ├── COMPONENT-form.md
    ├── COMPONENT-card.md
    └── ...
```

## Version

**Version actuelle** : 1.0.0 (MVP)

**Dernière mise à jour** : 2025-01-XX

## Références

- **[DESIGNER.md](../agents/DESIGNER.md)** : Description complète de l'agent Designer
- **[PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md)** : Contexte métier et besoins utilisateurs
- **[STACK.md](../memory_bank/STACK.md)** : Stack technique (Tailwind CSS, Livewire)

---

**Note** : Ce design system est en évolution constante. Toute modification doit être documentée et validée par l'agent Designer (Riley).

