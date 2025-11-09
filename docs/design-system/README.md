# Design System - Space Xplorer

Bienvenue dans le design system de Space Xplorer ! Ce système définit l'identité visuelle, les composants réutilisables, et les principes de design pour créer une expérience utilisateur cohérente et immersive dans l'univers spatial.

## Documentation

### Vue d'Ensemble

- **[DESIGN-SYSTEM.md](./DESIGN-SYSTEM.md)** - Vue d'ensemble complète du design system

### Documentation Détaillée

- **[DESIGN-SYSTEM-COLORS.md](./DESIGN-SYSTEM-COLORS.md)** - Palette de couleurs complète
- **[DESIGN-SYSTEM-TYPOGRAPHY.md](./DESIGN-SYSTEM-TYPOGRAPHY.md)** - Typographie et hiérarchie
- **[DESIGN-SYSTEM-SPACING.md](./DESIGN-SYSTEM-SPACING.md)** - Espacements et grilles
- **[DESIGN-SYSTEM-ANIMATIONS.md](./DESIGN-SYSTEM-ANIMATIONS.md)** - Animations et transitions
- **[DESIGN-SYSTEM-COMPONENTS.md](./DESIGN-SYSTEM-COMPONENTS.md)** - Vue d'ensemble des composants

### Composants

Tous les composants sont documentés dans le dossier `components/` :

- **[COMPONENT-button.md](./components/COMPONENT-button.md)** - Boutons avec variantes
- **[COMPONENT-form.md](./components/COMPONENT-form.md)** - Formulaires et champs de saisie
- **[COMPONENT-card.md](./components/COMPONENT-card.md)** - Cards et conteneurs
- **[COMPONENT-planet-card.md](./components/COMPONENT-planet-card.md)** - Card spécialisée pour les planètes

## Identité Visuelle

### Style Rétro-Futuriste

Le design system est inspiré de l'esthétique rétro-futuriste des films Alien :
- Ambiance sombre et immersive
- Interfaces monochromes avec accents fluorescents
- Esthétique industrielle des vaisseaux spatiaux
- Effets subtils (scanlines, lueurs) pour évoquer les écrans CRT

### Couleurs Principales

- **Background** : `#0a0a0a` (Noir profond)
- **Primary** : `#00ff88` (Vert fluorescent)
- **Secondary** : `#00aaff` (Bleu fluorescent)
- **Accent** : `#ffaa00` (Orange/Ambre)

Voir **[DESIGN-SYSTEM-COLORS.md](./DESIGN-SYSTEM-COLORS.md)** pour la palette complète.

### Règles Importantes

**Interdiction des Emojis** : Les emojis sont strictement interdits dans le design. Utiliser des icônes SVG, des symboles textuels, ou uniquement la typographie pour créer la hiérarchie visuelle.

## Composants Disponibles

### Composants de Base

- **Button** : Actions principales, secondaires, danger, ghost
- **Form** : Champs de saisie avec validation
- **Card** : Conteneurs pour afficher des informations
- **Planet Card** : Card spécialisée pour les planètes

### Composants à Venir

- Navigation
- Modal
- Badge
- Alert
- Loading Spinner
- Empty State

## Utilisation Rapide

### Couleurs

```html
<!-- Fond principal -->
<div class="bg-space-black text-white">Contenu</div>

<!-- Bouton primary -->
<button class="bg-space-primary hover:bg-space-primary-dark text-space-black">
  Action
</button>
```

### Typographie

```html
<!-- Titre principal -->
<h1 class="text-4xl font-bold tracking-tight text-white mb-4">Titre</h1>

<!-- Texte secondaire -->
<p class="text-lg text-gray-400">Description</p>
```

### Espacements

```html
<!-- Padding standard -->
<div class="p-6">Contenu</div>

<!-- Margin bottom -->
<div class="mb-8">Section</div>

<!-- Gap dans une grille -->
<div class="grid grid-cols-3 gap-6">Items</div>
```

### Composants

```html
<!-- Card -->
<div class="bg-surface-dark border border-border-dark rounded-lg p-6">
  <h3 class="text-2xl font-semibold text-white mb-4">Titre</h3>
  <p class="text-gray-300">Contenu</p>
</div>

<!-- Bouton -->
<button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors duration-150">
  Action
</button>
```

## Guide de Contribution

### Créer un Nouveau Composant

1. Créer le fichier de documentation dans `components/`
2. Suivre la structure standardisée
3. Documenter les variantes, états, et exemples
4. Ajouter la référence dans `DESIGN-SYSTEM-COMPONENTS.md`

### Modifier le Design System

1. Mettre à jour la documentation correspondante
2. Vérifier la cohérence avec les autres éléments
3. Documenter les changements
4. Valider avec l'agent Designer (Riley)

## Références

- **[DESIGNER.md](../agents/DESIGNER.md)** - Description de l'agent Designer
- **[PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md)** - Contexte métier
- **[STACK.md](../memory_bank/STACK.md)** - Stack technique

## Notes

- **Version** : 1.0.0 (MVP)
- **Dernière mise à jour** : 2025-01-XX
- **Maintenu par** : Agent Designer (Riley)

---

**Pour toute question ou suggestion, consulter l'agent Designer (Riley) ou la documentation complète.**

