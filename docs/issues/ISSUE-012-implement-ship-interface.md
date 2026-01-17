# ISSUE-012 : Implémenter l'interface dédiée au vaisseau

## Type
Feature

## Priorité
Medium

## Description

Implémenter l'interface complète permettant aux joueurs de visualiser et gérer leur vaisseau d'exploration. L'interface doit afficher les statistiques du vaisseau, son état, et préparer les actions futures (réparation, ravitaillement, amélioration).

**MVP Phase 1** : Interface de visualisation avec toutes les statistiques du vaisseau, barres de progression visuelles, et état du vaisseau. Les actions de réparation et ravitaillement seront préparées mais la logique complète sera en Phase 2.

## Contexte Métier

L'interface du vaisseau permet de :
- **Visualiser le vaisseau** : Voir toutes les statistiques et l'état du vaisseau en un coup d'œil
- **Comprendre les capacités** : Comprendre les limites et capacités du vaisseau
- **Préparer les améliorations** : Voir ce qui peut être amélioré (Phase 2)
- **Immersion** : Créer une connexion personnelle avec le vaisseau du joueur

**Valeur utilisateur** :
- **Visibilité** : Le joueur peut voir l'état complet de son vaisseau
- **Compréhension** : Comprendre les statistiques et leur impact sur le gameplay
- **Engagement** : Le vaisseau devient un élément personnel et important
- **Préparation** : Préparer les améliorations futures

## Critères d'Acceptation

### 1. Page Vaisseau

- [ ] **Route** :
  - Route : `/ship` (middleware `auth`)
  - Nom de route : `ship`

- [ ] **Composant Livewire** :
  - `Ship` dans `app/Livewire/Ship.php`
  - Attributs :
    - `Ship $ship` - Vaisseau du joueur (chargé automatiquement)
  - Méthodes :
    - `mount()` - Charge le vaisseau du joueur connecté
    - `getShipProperty()` - Propriété calculée pour le vaisseau
  - Validation :
    - Vérifier que l'utilisateur a un vaisseau assigné
    - Rediriger vers le dashboard avec message si pas de vaisseau

- [ ] **Vue** :
  - `resources/views/livewire/ship.blade.php`
  - Layout : `layouts.app`
  - Style : Cohérent avec le design system terminal

### 2. Affichage des Informations du Vaisseau

- [ ] **En-tête** :
  - Nom du vaisseau (grand, visible)
  - Modèle du vaisseau (sous-titre)
  - Niveau du vaisseau (badge ou indicateur)

- [ ] **Statistiques principales** :
  - **Fuel (Carburant)** :
    - Barre de progression visuelle (fuel_current / fuel_capacity)
    - Pourcentage affiché
    - Indicateur visuel (vert si > 50%, orange si 25-50%, rouge si < 25%)
    - Texte : "X / Y unités"
  
  - **Intégrité de la coque** :
    - Barre de progression visuelle (hull_integrity / 100)
    - Pourcentage affiché
    - Indicateur visuel (vert si 100%, orange si 50-99%, rouge si < 50%)
    - Texte : "X / 100"
  
  - **Puissance du moteur** :
    - Barre de progression visuelle (engine_power / 100)
    - Pourcentage affiché
    - Texte : "X / 100"
  
  - **Qualité des scanners** :
    - Barre de progression visuelle (scanner_quality / 100)
    - Pourcentage affiché
    - Texte : "X / 100"
  
  - **Force des boucliers** :
    - Barre de progression visuelle (shield_strength / 100)
    - Pourcentage affiché
    - Texte : "X / 100"

- [ ] **Informations calculées** :
  - Distance maximale de voyage (calculée via `getMaxTravelDistance()`)
  - État du vaisseau (Opérationnel, Endommagé, Besoin de carburant)
  - Statut visuel (icônes ou badges)

### 3. Actions Disponibles (Préparation Phase 2)

- [ ] **Boutons d'action** (désactivés pour MVP, logique en Phase 2) :
  - Bouton "Réparer" (si hull_integrity < 100)
    - Afficher le coût en ressources (futur)
    - Désactivé avec message "Bientôt disponible"
  
  - Bouton "Ravitailler" (si fuel_current < fuel_capacity)
    - Afficher le coût en ressources (futur)
    - Désactivé avec message "Bientôt disponible"
  
  - Bouton "Améliorer" (futur, Phase 2)
    - Masqué pour le MVP

- [ ] **Messages informatifs** :
  - Si vaisseau endommagé : Message d'alerte avec conseil de réparation
  - Si carburant faible : Message d'alerte avec conseil de ravitaillement
  - Si vaisseau opérationnel : Message de confirmation

### 4. Design et Expérience Utilisateur

- [ ] **Style terminal** :
  - Cohérent avec le design system existant
  - Couleurs : Vert/cyan pour les barres de progression
  - Typographie : Monospace pour les données techniques
  - Effets visuels : Scanlines subtiles, lueurs (optionnel)

- [ ] **Responsive** :
  - Adapté aux différentes tailles d'écran
  - Layout qui s'adapte sur mobile/tablet/desktop

- [ ] **Accessibilité** :
  - Labels clairs pour les barres de progression
  - Contraste suffisant pour la lisibilité
  - Support clavier pour la navigation

### 5. Intégration dans la Navigation

- [ ] **Lien dans la navigation** :
  - Lien "Vaisseau" dans la navigation principale
  - Visible uniquement si l'utilisateur a un vaisseau assigné
  - Badge ou indicateur si le vaisseau est endommagé ou a besoin de carburant (optionnel)

- [ ] **Résumé dans le Dashboard** :
  - Afficher un résumé du vaisseau (nom, état, fuel, intégrité)
  - Lien vers la page vaisseau complète

## Détails Techniques

### Composant Livewire

```php
#[Layout('layouts.app')]
class Ship extends Component
{
    #[Computed]
    public function ship(): Ship
    {
        return Auth::user()->ship ?? throw new NotFoundHttpException('No ship assigned');
    }

    public function mount(): void
    {
        // Vérifier que l'utilisateur a un vaisseau
        if (!Auth::user()->ship) {
            session()->flash('error', 'Aucun vaisseau assigné.');
            redirect()->route('dashboard');
        }
    }

    public function render()
    {
        return view('livewire.ship');
    }
}
```

### Calculs et Méthodes Utilitaires

- Utiliser les méthodes du modèle `Ship` :
  - `isDamaged()` - Pour afficher l'état
  - `needsFuel()` - Pour afficher l'alerte carburant
  - `getMaxTravelDistance()` - Pour afficher la distance maximale
  - `getFuelPercentage()` - Pour la barre de progression

### Structure de la Vue

- Section en-tête (nom, modèle, niveau)
- Section statistiques (5 barres de progression)
- Section informations calculées (distance max, état)
- Section actions (boutons désactivés avec messages)
- Section métadonnées (optionnel, pour debug)

## Notes

### Scope MVP Phase 1

- **Actions fonctionnelles** : Non inclus (réparation, ravitaillement en Phase 2)
- **Amélioration** : Non inclus (Phase 2)
- **Historique** : Non inclus (Phase 2)
- **Modules** : Non inclus (Phase 2)
- **Crew** : Non inclus (Phase 2)

### Principes de Design

1. **Clarté** : Toutes les informations doivent être claires et compréhensibles
2. **Visuel** : Utiliser des barres de progression pour rendre les statistiques visuelles
3. **Cohérence** : Style cohérent avec le reste de l'application (terminal)
4. **Préparation** : Préparer l'interface pour les actions futures

### Extensibilité

L'interface doit être conçue pour faciliter l'ajout de :
- Nouvelles statistiques
- Actions de réparation/ravitaillement
- Système d'amélioration
- Modules et équipements
- Historique des actions

## Références

- **[ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md)** - Architecture technique générale
- **[ISSUE-011-implement-ship-system-mvp.md](./ISSUE-011-implement-ship-system-mvp.md)** - Système de vaisseau (modèle, attribution)
- **[DESIGN-SYSTEM.md](../design-system/DESIGN-SYSTEM.md)** - Design system pour l'interface
- **[DRAFT-02-management-system.md](../game-design/drafts/DRAFT-02-management-system.md)** - Système de gestion (vaisseau, améliorations)

## Suivi et Historique

### Statut

À faire

### Historique

#### 2025-01-27 - Alex (Product Manager) - Création de l'issue
**Statut** : À faire
**Détails** : Issue créée pour implémenter l'interface dédiée au vaisseau. Cette feature permet aux joueurs de visualiser et comprendre leur vaisseau, et prépare les actions futures (réparation, ravitaillement, amélioration). Le MVP Phase 1 se concentre sur la visualisation complète des statistiques et de l'état du vaisseau.
**GitHub** : [#21](https://github.com/PiLep/space-xplorer/issues/21)
**Notes** : Issue de priorité moyenne car dépend de ISSUE-011 (le vaisseau doit exister avant de créer l'interface). Les actions fonctionnelles (réparation, ravitaillement) seront implémentées en Phase 2.
