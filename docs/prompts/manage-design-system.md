# Action: Manage Design System

## Description

Cette action permet à l'agent Designer (Riley) de créer, reviewer, et maintenir le design system de Stellar, ainsi que de créer et modifier des composants visuels. Le design system garantit la cohérence visuelle et l'identité rétro-futuriste inspirée des films Alien à travers toute l'application.

## Quand Utiliser Cette Action

L'agent Designer doit utiliser cette action quand :
- Un nouveau design system doit être créé ou documenté
- Le design system existant doit être reviewé et amélioré
- De nouveaux composants doivent être créés
- Des composants existants doivent être modifiés ou améliorés
- Il faut garantir la cohérence visuelle à travers l'application
- Une nouvelle fonctionnalité nécessite des composants spécifiques

## Format de Documentation

### Design System

Le design system doit être documenté dans `docs/design-system/` avec le format suivant :

**Nom du fichier principal** : `DESIGN-SYSTEM.md`

**Fichiers additionnels** :
- `DESIGN-SYSTEM-COLORS.md` : Palette de couleurs
- `DESIGN-SYSTEM-TYPOGRAPHY.md` : Typographie
- `DESIGN-SYSTEM-COMPONENTS.md` : Composants
- `DESIGN-SYSTEM-SPACING.md` : Espacements et grilles
- `DESIGN-SYSTEM-ANIMATIONS.md` : Animations et transitions

### Composants

Les composants doivent être documentés dans `docs/design-system/components/` :

**Nom du fichier** : `COMPONENT-{nom-du-composant}.md`

## Structure du Design System

```markdown
# Design System - Stellar

## Vue d'Ensemble

{Description générale du design system, philosophie, et objectifs}

## Identité Visuelle

### Influences Design

- **Style rétro-futuriste inspiré des films Alien** : Esthétique industrielle, interfaces monochromes avec accents fluorescents, écrans CRT, typographies techniques
- Ambiance sombre et immersive
- Design fonctionnel et industriel

### Principes Fondamentaux

1. **Cohérence** : Maintenir une cohérence visuelle à travers toute l'application
2. **Hiérarchie** : Utiliser la typographie et l'espacement pour créer une hiérarchie claire
3. **Rétro-futurisme** : Inspirer le design des interfaces de vaisseaux spatiaux tout en restant moderne
4. **Accessibilité** : Assurer l'accessibilité visuelle pour tous les utilisateurs
5. **Performance** : Optimiser les performances visuelles

## Palette de Couleurs

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

### Couleurs de Texte

- **Primary Text** : `#ffffff` (Blanc, texte principal)
- **Secondary Text** : `#aaaaaa` (Gris clair, texte secondaire)
- **Muted Text** : `#666666` (Gris moyen, texte désactivé)

### Utilisation

{Exemples d'utilisation des couleurs avec code Tailwind CSS}

## Typographie

### Familles de Polices

- **Primary** : [Police principale] - Moderne, lisible, technique
- **Monospace** : [Police monospace] - Pour les données techniques et interfaces

### Hiérarchie

- **H1** : Taille, poids, espacement
- **H2** : Taille, poids, espacement
- **H3** : Taille, poids, espacement
- **Body** : Taille, poids, espacement
- **Caption** : Taille, poids, espacement

### Utilisation

{Exemples d'utilisation de la typographie avec code Tailwind CSS}

## Espacements & Grilles

### Système d'Espacement

- Base : 4px
- Échelle : 4, 8, 12, 16, 24, 32, 48, 64, 96, 128

### Grilles

- **Desktop** : 12 colonnes, gutter 24px
- **Tablet** : 8 colonnes, gutter 16px
- **Mobile** : 4 colonnes, gutter 12px

## Composants

### Boutons

{Documentation des variantes de boutons}

### Formulaires

{Documentation des champs de formulaire}

### Cards

{Documentation des cards}

### Navigation

{Documentation de la navigation}

### Modals

{Documentation des modals}

## Animations & Transitions

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

{Principes de design responsive}

## Accessibilité

### Contraste

- Ratio minimum : 4.5:1 pour le texte normal
- Ratio minimum : 3:1 pour le texte large

### Focus States

{Documentation des états de focus}

## Utilisation avec Tailwind CSS

{Exemples de configuration Tailwind et utilisation des classes}
```

## Structure d'un Composant

```markdown
# COMPONENT-{nom-du-composant}

## Vue d'Ensemble

{Description du composant, son rôle, et quand l'utiliser}

## Design

### Apparence

{Description visuelle du composant}

### Variantes

- **Variante 1** : Description et usage
- **Variante 2** : Description et usage

### États

- **Default** : État par défaut
- **Hover** : État au survol
- **Active** : État actif
- **Disabled** : État désactivé
- **Loading** : État de chargement

### Responsive

{Comportement responsive du composant}

## Spécifications Techniques

### Classes Tailwind

```html
<!-- Exemple d'utilisation -->
<div class="...">
  ...
</div>
```

### Props (si composant Livewire)

- `prop1` : Type - Description
- `prop2` : Type - Description

### Structure HTML

```html
{Structure HTML du composant}
```

## Code d'Implémentation

### Livewire Component

```php
{Code PHP du composant Livewire si applicable}
```

### Blade Template

```blade
{Code Blade du template}
```

### CSS/Tailwind

```css
{Classes Tailwind ou CSS personnalisé}
```

## Exemples d'Utilisation

### Exemple 1 : Usage Basique

{Exemple simple}

### Exemple 2 : Usage Avancé

{Exemple avancé}

## Accessibilité

- Contraste : Ratio de contraste
- Focus : Gestion du focus
- ARIA : Attributs ARIA si nécessaire

## Notes de Design

{Notes sur les choix de design, inspirations, etc.}
```

## Processus de Création du Design System

### 1. Analyse et Recherche

- Analyser les besoins de l'application
- Identifier les composants récurrents
- Définir l'identité visuelle (inspirée d'Alien)
- Rechercher les meilleures pratiques

### 2. Définition des Fondamentaux

- Palette de couleurs (rétro-futuriste, monochrome avec accents fluorescents)
- Typographie (technique, moderne)
- Espacements et grilles
- Principes d'animation

### 3. Création des Composants de Base

- Boutons
- Formulaires
- Cards
- Navigation
- Modals

### 4. Documentation

- Documenter chaque élément du design system
- Créer des exemples d'utilisation
- Fournir du code réutilisable

### 5. Review et Amélioration

- Review avec l'équipe
- Tester sur différents appareils
- Ajuster selon les retours

## Processus de Création d'un Composant

### 1. Analyse du Besoin

- Identifier le besoin du composant
- Analyser les cas d'usage
- Vérifier si un composant similaire existe

### 2. Design

- Créer le design du composant
- Définir les variantes et états
- S'assurer de la cohérence avec le design system
- Vérifier le responsive

### 3. Spécifications Techniques

- Définir la structure HTML
- Définir les classes Tailwind
- Définir les props si composant Livewire
- Définir les animations

### 4. Implémentation

- Créer le composant Livewire (si nécessaire)
- Créer le template Blade
- Ajouter les styles Tailwind
- Tester le composant

### 5. Documentation

- Documenter le composant
- Créer des exemples d'utilisation
- Ajouter des notes de design

### 6. Review

- Review visuelle du composant
- Vérifier la cohérence avec le design system
- Tester l'accessibilité
- Valider avec l'équipe

## Processus de Modification d'un Composant

### 1. Identification du Problème

- Identifier ce qui doit être modifié
- Analyser l'impact sur le design system
- Vérifier les dépendances

### 2. Conception de la Modification

- Concevoir la modification
- S'assurer de la cohérence avec le design system
- Vérifier l'impact sur les autres composants

### 3. Implémentation

- Modifier le composant
- Mettre à jour la documentation
- Tester les modifications

### 4. Review

- Review visuelle des modifications
- Vérifier que tout fonctionne correctement
- Valider avec l'équipe

## Review du Design System

### Critères de Review

#### Cohérence

- ✅ Le design system est-il cohérent à travers tous les composants ?
- ✅ Les couleurs sont-elles utilisées de manière cohérente ?
- ✅ La typographie est-elle appliquée uniformément ?
- ✅ Les espacements sont-ils cohérents ?

#### Identité Visuelle

- ✅ L'identité rétro-futuriste est-elle bien présente ?
- ✅ Les références aux films Alien sont-elles subtiles et modernes ?
- ✅ L'ambiance spatiale est-elle créée efficacement ?

#### Accessibilité

- ✅ Les contrastes sont-ils suffisants ?
- ✅ Les états de focus sont-ils visibles ?
- ✅ Le design est-il accessible pour tous les utilisateurs ?

#### Performance

- ✅ Les animations sont-elles optimisées ?
- ✅ Les styles sont-ils efficaces ?
- ✅ Le design impacte-t-il la performance ?

#### Documentation

- ✅ La documentation est-elle complète ?
- ✅ Les exemples sont-ils clairs ?
- ✅ Le code est-il bien documenté ?

### Format de Review

```markdown
# DESIGN-SYSTEM-REVIEW-{date}

## Vue d'Ensemble

{Résumé de la review}

## Statut

[✅ Approuvé | ⚠️ Approuvé avec améliorations | ❌ Retour pour modifications]

## Points Positifs

- Point positif 1
- Point positif 2

## Points à Améliorer

- Point à améliorer 1 avec explication
- Point à améliorer 2 avec explication

## Problèmes Identifiés

- Problème 1 avec explication et impact
- Problème 2 avec explication et impact

## Recommandations

### Recommandation 1 : [Titre]

**Problème** : Description
**Impact** : Impact sur le design system
**Recommandation** : Ce qui doit être modifié
**Priorité** : [High | Medium | Low]

## Conclusion

{Résumé final et prochaines étapes}
```

## Instructions pour l'Agent Designer

Quand tu crées ou modifies le design system ou des composants :

1. **Respecter l'identité visuelle** : Toujours garder en tête l'esthétique rétro-futuriste inspirée d'Alien
2. **Documenter** : Documenter chaque élément du design system et chaque composant
3. **Créer des exemples** : Fournir des exemples d'utilisation clairs
4. **Tester** : Tester sur différents appareils et navigateurs
5. **Reviewer** : Faire une review visuelle complète avant validation
6. **Collaborer** : Travailler avec Sam et Jordan pour l'implémentation technique
7. **Mettre à jour** : Mettre à jour la documentation quand nécessaire

### Checklist de Création de Composant

- [ ] Le composant respecte le design system
- [ ] Les variantes et états sont définis
- [ ] Le composant est responsive
- [ ] L'accessibilité est assurée
- [ ] La documentation est complète
- [ ] Des exemples d'utilisation sont fournis
- [ ] Le code est propre et réutilisable

### Checklist de Review du Design System

- [ ] Cohérence visuelle vérifiée
- [ ] Identité rétro-futuriste présente
- [ ] Accessibilité assurée
- [ ] Performance optimisée
- [ ] Documentation complète
- [ ] Exemples clairs et utiles

## Localisation des Fichiers

### Design System

- **Documentation principale** : `docs/design-system/DESIGN-SYSTEM.md`
- **Composants** : `docs/design-system/components/`
- **Exemples** : `docs/design-system/examples/`

### Composants Livewire

- **Composants** : `app/Livewire/`
- **Templates** : `resources/views/livewire/`
- **Styles** : `resources/css/` ou classes Tailwind

## Références

- **[DESIGNER.md](../agents/DESIGNER.md)** : Description complète de l'agent Designer
- **[PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md)** : Contexte métier et besoins utilisateurs
- **[STACK.md](../memory_bank/STACK.md)** : Stack technique (Tailwind CSS, Livewire)

---

**Rappel** : En tant qu'agent Designer, tu es responsable du design system et des composants. Tu crées des designs cohérents, documentés, et inspirés de l'esthétique rétro-futuriste des films Alien. Tu t'assures que chaque composant respecte l'identité visuelle et offre une excellente expérience utilisateur.

