# ISSUE-011 : Implémenter le système de vaisseau MVP

## Type
Feature

## Priorité
High

## Description

Implémenter le système de vaisseau permettant à chaque joueur de recevoir un vaisseau d'exploration lors de son arrivée dans le jeu. Le système comprend la création automatique d'un vaisseau de départ, l'attribution au joueur, et un message narratif expliquant le recrutement sur l'avant-poste Stellar.

**MVP Phase 1** : Système de base avec modèle de données, attribution automatique d'un vaisseau de départ, message dans l'inbox, et notification. L'interface dédiée au vaisseau sera implémentée dans une issue séparée (ISSUE-012).

## Contexte Métier

Lors de l'arrivée dans le jeu, le joueur est sur sa planète de départ. Il faut créer une expérience immersive qui explique :
- Que le joueur a été recruté sur un avant-poste Stellar
- Qu'un vaisseau d'exploration lui sera attribué
- Comment ce vaisseau sera utilisé pour explorer l'univers

**Valeur utilisateur** :
- **Immersion** : Créer une connexion narrative avec l'univers Stellar dès l'arrivée
- **Progression** : Donner un objectif clair (explorer avec son vaisseau)
- **Engagement** : Le vaisseau devient un élément personnel et important pour le joueur
- **Fondation** : Préparer le terrain pour les systèmes futurs (amélioration, modules, crew)

**Moment d'attribution** : Le vaisseau doit être attribué automatiquement lors de l'onboarding (ou juste après) pour créer une expérience fluide et immersive.

## Critères d'Acceptation

### 1. Modèle de Données

- [ ] **Table `ships`** :
  - `id` (ULID) - Identifiant unique
  - `user_id` (ULID, FK → users.id, unique) - Propriétaire (un joueur = un vaisseau)
  - `name` (string) - Nom du vaisseau (généré ou personnalisable)
  - `model` (string) - Modèle du vaisseau (ex: "Stellar Explorer", "Deep Space Scout")
  - `level` (integer, default: 1) - Niveau du vaisseau (1-10)
  - `fuel_capacity` (integer, default: 100) - Capacité de carburant maximale
  - `fuel_current` (integer, default: 100) - Carburant actuel
  - `hull_integrity` (integer, default: 100) - Intégrité de la coque (0-100)
  - `engine_power` (integer, default: 50) - Puissance du moteur (1-100)
  - `scanner_quality` (integer, default: 50) - Qualité des scanners (1-100)
  - `shield_strength` (integer, default: 50) - Force des boucliers (1-100)
  - `metadata` (JSON, nullable) - Métadonnées additionnelles
  - `created_at`, `updated_at` - Timestamps
  - Index sur `user_id` (unique)

- [ ] **Migration** :
  - Créer la migration avec tous les champs et index
  - Foreign key vers `users.id` avec `onDelete('cascade')`
  - Contrainte unique sur `user_id`

- [ ] **Modification table `users`** :
  - Ajouter `ship_id` (ULID, nullable, FK → ships.id)
  - Index sur `ship_id`

- [ ] **Modèle Eloquent** :
  - `Ship` dans `app/Models/Ship.php`
  - Relations :
    - `user()` : BelongsTo → User
  - Méthodes utilitaires :
    - `isDamaged(): bool` - Vérifie si le vaisseau est endommagé (hull_integrity < 100)
    - `needsFuel(): bool` - Vérifie si le vaisseau a besoin de carburant (fuel_current < fuel_capacity)
    - `canTravel(float $distance): bool` - Vérifie si le vaisseau peut parcourir une distance
    - `getMaxTravelDistance(): float` - Calcule la distance maximale selon le fuel et engine_power
    - `getFuelPercentage(): float` - Retourne le pourcentage de carburant

### 2. Service de Vaisseau

- [ ] **ShipService** dans `app/Services/ShipService.php` :
  - `createStarterShip(User $user): Ship` - Crée un vaisseau de départ avec statistiques de base
  - `assignShipToUser(User $user, Ship $ship): void` - Assigne un vaisseau à un utilisateur
  - `repairShip(Ship $ship, int $amount): void` - Répare le vaisseau (futur, Phase 2)
  - `refuelShip(Ship $ship, int $amount): void` - Recharge le carburant (futur, Phase 2)
  - `upgradeShip(Ship $ship, string $stat, int $amount): void` - Améliore une statistique (futur, Phase 2)
  - `generateShipName(): string` - Génère un nom aléatoire pour le vaisseau

### 3. Attribution Automatique du Vaisseau

- [ ] **Événement** :
  - `ShipAssigned` : Déclenché lors de l'attribution d'un vaisseau
    - Données : User, Ship

- [ ] **Listener** :
  - `AssignStarterShip` : Écoute un événement approprié (fin d'onboarding ou après vérification email)
    - Crée un vaisseau de départ avec statistiques de base
    - Assigne le vaisseau au joueur (`user.ship_id`)
    - Dispatch l'événement `ShipAssigned`

- [ ] **Intégration avec l'onboarding** :
  - Option 1 : Attribuer le vaisseau à la fin de l'onboarding (étape 4)
  - Option 2 : Attribuer le vaisseau après vérification de l'email (si onboarding pas encore implémenté)
  - Décision à prendre selon l'état de l'onboarding (ISSUE-005)

### 4. Message de Recrutement et Attribution

- [ ] **Message automatique dans l'inbox** :
  - Créé automatiquement lors de l'attribution du vaisseau (via listener)
  - Type : `mission` ou `system`
  - Sujet : "Attribution de votre vaisseau d'exploration"
  - Contenu narratif expliquant :
    - Le recrutement sur l'avant-poste Stellar
    - L'attribution du vaisseau d'exploration
    - Les caractéristiques de base du vaisseau
    - Les prochaines étapes (exploration, amélioration)
  - Métadonnées incluant l'ID du vaisseau attribué

- [ ] **Template de message** :
  - Utiliser `MessageService::createMissionMessage()` ou `createSystemMessage()`
  - Template narratif cohérent avec l'univers Stellar (ambiance Alien, compagnie mystérieuse)
  - Inclure le nom du vaisseau et ses statistiques de base

### 5. Notification d'Attribution

- [ ] **Notification automatique** :
  - Créée automatiquement lors de l'attribution du vaisseau (via listener)
  - Type : `ship_assigned`
  - Titre : "Vaisseau attribué"
  - Message : "Votre vaisseau d'exploration {ship_name} vous a été attribué."
  - Données JSON : `ship_id`, `ship_name`, `ship_url` (vers `/ship`)

- [ ] **Dépendance** : Nécessite ISSUE-009 (système de notifications) - ✅ **Complété** (voir `docs/issues/closed/ISSUE-009-implement-in-app-notifications.md`)

### 6. Statistiques de Base du Vaisseau de Départ

- [ ] **Vaisseau de départ** :
  - Nom : Généré aléatoirement (ex: "Stellar Explorer #1234", "Deep Space Scout #5678")
  - Modèle : "Stellar Explorer" (modèle de base)
  - Niveau : 1
  - Fuel : 100/100 (plein)
  - Intégrité : 100/100 (neuf)
  - Moteur : 50/100 (performance de base)
  - Scanners : 50/100 (qualité de base)
  - Boucliers : 50/100 (protection de base)

## Détails Techniques

### Génération du Nom du Vaisseau

**Format suggéré** :
- Préfixes : "Stellar Explorer", "Deep Space Scout", "Orbital Runner", "Nebula Seeker"
- Suffixes : Numéro aléatoire (4 chiffres) ou matricule
- Exemples : "Stellar Explorer #1234", "Deep Space Scout #5678"

**Implémentation** :
- Pool de préfixes dans `config/ships.php`
- Sélection aléatoire pondérée
- Génération du numéro unique (vérifier l'unicité)

### Calcul de la Distance Maximale

**Formule** :
```php
$maxDistance = ($fuelCurrent / $fuelCapacity) * $enginePower * $baseRange;
```

Où `$baseRange` est une constante (ex: 200 unités pour un vaisseau niveau 1).

### Événements et Listeners

**Flux d'attribution** :
1. Fin d'onboarding (ou après vérification email) → Événement déclenché
2. Listener `AssignStarterShip` :
   - Crée le vaisseau via `ShipService::createStarterShip()`
   - Assigne au joueur via `ShipService::assignShipToUser()`
   - Dispatch `ShipAssigned`
3. Listener `CreateShipAssignmentMessage` :
   - Crée le message dans l'inbox via `MessageService`
4. Listener `CreateShipAssignmentNotification` :
   - Crée la notification via `NotificationService` (ISSUE-009 - ✅ Complété)

### Intégration avec l'Existant

- **MessageService** : Utiliser pour créer le message de recrutement
- **Inbox** : Le message apparaîtra dans l'inbox existante
- **Notifications** : Utiliser le système de notifications (ISSUE-009 - ✅ Complété)
- **Onboarding** : Intégrer dans le flux d'onboarding (ISSUE-005)

## Notes

### Scope MVP Phase 1

- **Interface dédiée au vaisseau** : Non inclus (ISSUE-012)
- **Amélioration de vaisseau** : Non inclus (Phase 2)
- **Réparation/Ravitaillement** : Modèle de données prêt, logique en Phase 2
- **Modules** : Non inclus (Phase 2)
- **Crew** : Non inclus (Phase 2)

### Principes de Design

1. **Simplicité** : Statistiques claires et compréhensibles
2. **Immersion** : Messages narratifs cohérents avec l'univers Stellar
3. **Progression visible** : Les statistiques du vaisseau sont visibles (via interface future)
4. **Fondation** : Architecture extensible pour les améliorations futures

### Extensibilité

L'architecture doit permettre facilement :
- L'ajout de nouvelles statistiques au vaisseau
- L'intégration avec les systèmes futurs (amélioration, modules, crew)
- Le calcul de capacités basées sur les statistiques
- La personnalisation du vaisseau (nom, apparence, etc.)

## Références

- **[ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md)** - Architecture technique générale
- **[PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md)** - Vision métier et personas
- **[DRAFT-02-management-system.md](../game-design/drafts/DRAFT-02-management-system.md)** - Système de gestion (vaisseau, modules, crew)
- **[ISSUE-005-implement-onboarding-mvp.md](./ISSUE-005-implement-onboarding-mvp.md)** - Système d'onboarding
- **[ISSUE-009-implement-in-app-notifications.md](./closed/ISSUE-009-implement-in-app-notifications.md)** - Système de notifications (✅ Complété)
- **[ISSUE-006-implement-inbox-system.md](./closed/ISSUE-006-implement-inbox-system.md)** - Système d'inbox existant

## Suivi et Historique

### Statut

À faire

### Historique

#### 2025-01-27 - Alex (Product Manager) - Création de l'issue
**Statut** : À faire
**Détails** : Issue créée pour implémenter le système de vaisseau MVP. Cette feature est essentielle pour créer une expérience immersive dès l'arrivée dans le jeu et préparer les systèmes futurs d'amélioration et de gestion. Le MVP Phase 1 se concentre sur les fonctionnalités de base : modèle de données, attribution automatique, message narratif, et notification.
**GitHub** : [#20](https://github.com/PiLep/space-xplorer/issues/20)
**Notes** : Issue prioritaire car core feature du jeu. Dépend de ISSUE-009 (notifications - ✅ Complété) pour la notification d'attribution. L'interface dédiée au vaisseau sera implémentée dans ISSUE-012.
