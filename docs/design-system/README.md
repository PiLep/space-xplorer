# Design System - Stellar

Bienvenue dans le design system de Stellar ! Ce système définit l'identité visuelle, les composants réutilisables, et les principes de design pour créer une expérience utilisateur cohérente et immersive dans l'univers spatial.

## Documentation

### Vue d'Ensemble

- **[DESIGN-SYSTEM.md](./DESIGN-SYSTEM.md)** - Vue d'ensemble complète du design system

### Documentation Détaillée

- **[DESIGN-SYSTEM-COLORS.md](./DESIGN-SYSTEM-COLORS.md)** - Palette de couleurs complète
- **[DESIGN-SYSTEM-TYPOGRAPHY.md](./DESIGN-SYSTEM-TYPOGRAPHY.md)** - Typographie et hiérarchie
- **[DESIGN-SYSTEM-SPACING.md](./DESIGN-SYSTEM-SPACING.md)** - Espacements et grilles
- **[DESIGN-SYSTEM-ANIMATIONS.md](./DESIGN-SYSTEM-ANIMATIONS.md)** - Animations et transitions
- **[DESIGN-SYSTEM-COMPONENTS.md](./DESIGN-SYSTEM-COMPONENTS.md)** - Vue d'ensemble des composants

### Branding

- **[BRANDING-FAVICONS.md](./BRANDING-FAVICONS.md)** - Favicons et icônes de l'application (PWA, iOS, Android)

### Accessibilité

- **[ACCESSIBILITY-AUDIT.md](./ACCESSIBILITY-AUDIT.md)** - Audit d'accessibilité des composants (WCAG 2.1)
- **[ACCESSIBILITY-IMPROVEMENTS.md](./ACCESSIBILITY-IMPROVEMENTS.md)** - Détails des améliorations d'accessibilité apportées

### Composants

Tous les composants sont documentés dans le dossier `components/` et organisés par catégories :

#### Composants de Base
- **[COMPONENT-button.md](./components/COMPONENT-button.md)** - Boutons avec variantes (Primary, Secondary, Danger, Ghost)
- **[COMPONENT-form.md](./components/COMPONENT-form.md)** - Formulaires et champs de saisie avec validation
- **[COMPONENT-form-input.md](./components/COMPONENT-form-input.md)** - Composant réutilisable pour les champs de formulaire (Classic, Terminal)
- **[COMPONENT-form-select.md](./components/COMPONENT-form-select.md)** - Champ select avec label et validation
- **[COMPONENT-form-card.md](./components/COMPONENT-form-card.md)** - Conteneur standardisé pour les formulaires (Standard, Header Séparé)
- **[COMPONENT-page-header.md](./components/COMPONENT-page-header.md)** - En-tête de page standardisé avec titre et description
- **[COMPONENT-logo.md](./components/COMPONENT-logo.md)** - Logo "STELLAR" avec style terminal/sci-fi (5 tailles: xs, sm, md, lg, xl, scanlines optionnelles)
- **[COMPONENT-card.md](./components/COMPONENT-card.md)** - Cards et conteneurs génériques
- **[COMPONENT-alert.md](./components/COMPONENT-alert.md)** - Messages d'alerte et notifications
- **[COMPONENT-badge.md](./components/COMPONENT-badge.md)** - Indicateurs de statut et labels avec variantes sémantiques

#### Composants Terminal
- **[COMPONENT-terminal-prompt.md](./components/COMPONENT-terminal-prompt.md)** - Ligne de commande terminal avec prompt système
- **[COMPONENT-terminal-boot.md](./components/COMPONENT-terminal-boot.md)** - Séquence de messages de démarrage système avec animations

#### Composants Spécialisés
- **[COMPONENT-planet-card.md](./components/COMPONENT-planet-card.md)** - Card spécialisée pour l'affichage des planètes
- **[COMPONENT-loading-spinner.md](./components/COMPONENT-loading-spinner.md)** - Indicateur de chargement avec style terminal
- **[COMPONENT-stat-card.md](./components/COMPONENT-stat-card.md)** - Carte de statistique avec icône optionnelle

#### Composants Utilitaires
- **[COMPONENT-button-group.md](./components/COMPONENT-button-group.md)** - Groupe de boutons avec layout flexible
- **[COMPONENT-navigation.md](./components/COMPONENT-navigation.md)** - Navigation principale avec variantes (Sidebar, Top, Terminal)
- **[COMPONENT-modal.md](./components/COMPONENT-modal.md)** - Dialogs pour interactions importantes (Standard, Confirmation, Form)
- **[COMPONENT-filter-card.md](./components/COMPONENT-filter-card.md)** - Conteneur standardisé pour les sections de filtres
- **[COMPONENT-description-list.md](./components/COMPONENT-description-list.md)** - Liste de descriptions pour pages de détails
- **[COMPONENT-empty-state.md](./components/COMPONENT-empty-state.md)** - État vide avec icône et action optionnelles

Voir **[DESIGN-SYSTEM-COMPONENTS.md](./DESIGN-SYSTEM-COMPONENTS.md)** pour la documentation complète de tous les composants.

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
- **Form** : Formulaires et styles de base
- **Form Input** : Composant réutilisable pour les champs de formulaire (Classic, Terminal)
- **Form Select** : Champ select avec label et validation
- **Form Card** : Conteneur standardisé pour les formulaires (Standard, Header Séparé)
- **Page Header** : En-tête de page standardisé avec titre et description
- **Logo** : Logo "STELLAR" avec style terminal/sci-fi (5 tailles: xs, sm, md, lg, xl, scanlines optionnelles)
- **Card** : Conteneurs pour afficher des informations
- **Alert** : Messages d'alerte et notifications (Success, Error, Warning, Info)
- **Badge** : Indicateurs de statut et labels avec variantes sémantiques (Success, Warning, Error, Info, Generating, Default)
- **Container** : Composant utilitaire pour la mise en page avec largeurs maximales standardisées (Standard, Compact, Full)

### Composants Terminal

- **Terminal Prompt** : Ligne de commande terminal avec prompt système
- **Terminal Boot** : Séquence de messages de démarrage système avec animations

### Composants Spécialisés

- **Planet Card** : Card spécialisée pour l'affichage des planètes
- **Loading Spinner** : Indicateur de chargement avec style terminal (Small, Medium, Large)
- **Stat Card** : Carte de statistique avec icône optionnelle pour afficher des métriques

### Composants Utilitaires

- **Button Group** : Groupe de boutons avec layout flexible (alignement, espacement, largeur complète)
- **Navigation** : Navigation principale avec variantes (Sidebar, Top Menu, Terminal Command Bar)
- **Modal** : Dialogs pour interactions importantes (Standard, Confirmation, Form)
- **Filter Card** : Conteneur standardisé pour les sections de filtres
- **Description List** : Liste de descriptions pour pages de détails avec grille responsive
- **Empty State** : État vide avec icône et action optionnelles pour guider l'utilisateur
- **Table** : Composant complet pour afficher des données tabulaires avec headers, rows, pagination et variantes
- **Progress Bar** : Indicateur visuel de progression avec pourcentage et couleurs personnalisables

### Templates Email

- **Email Templates** : Templates d'email avec style terminal pour maintenir la cohérence du design system (Reset Password Notification, Password Reset Confirmation)

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
- **Maintenu par** : Agent Designer (Riley)

---

**Pour toute question ou suggestion, consulter l'agent Designer (Riley) ou la documentation complète.**

