# ISSUE-011 : Implémenter le système de vaisseau MVP

## Type
Feature

## Priorité
High

## Description

Implémenter le système de vaisseau permettant à chaque joueur de recevoir un vaisseau d'exploration lors de son arrivée dans le jeu. Le vaisseau est d'abord reçu comme objet dans l'inventaire (`starter_ship`), puis peut être activé pour être assemblé et devenir le vaisseau actif du joueur.

**MVP Phase 1** : Système de base avec modèle de données, réception du vaisseau dans l'inventaire, activation/assemblage du vaisseau, message dans l'inbox, et notification. L'interface dédiée au vaisseau sera implémentée dans une issue séparée (ISSUE-012).

**Important** : Le vaisseau est d'abord un objet activable dans l'inventaire. Une fois activé et assemblé, il devient le vaisseau actif du joueur (lié directement au joueur, retiré de l'inventaire).

## Contexte Métier

Lors de l'arrivée dans le jeu, le joueur est sur sa planète de départ. Il faut créer une expérience immersive qui explique :
- Que le joueur a été recruté sur un avant-poste Stellar
- Qu'un vaisseau d'exploration lui sera attribué (reçu dans l'inventaire)
- Comment activer et assembler le vaisseau pour commencer l'exploration

**Valeur utilisateur** :
- **Immersion** : Créer une connexion narrative avec l'univers Stellar dès l'arrivée
- **Progression** : Donner un objectif clair (recevoir le vaisseau, l'activer, explorer)
- **Engagement** : Le vaisseau devient un élément personnel et important pour le joueur
- **Fondation** : Préparer le terrain pour les systèmes futurs (amélioration, modules, crew)

**Moment de réception** : Le vaisseau doit être reçu dans l'inventaire automatiquement lors de l'onboarding (ou juste après) pour créer une expérience fluide et immersive. Le joueur peut ensuite l'activer quand il le souhaite.

**Flux** :
1. Réception du vaisseau dans l'inventaire (objet `starter_ship`)
2. Fenêtre de réception affichée au joueur
3. Le joueur peut activer le vaisseau depuis l'inventaire
4. Assemblage du vaisseau et liaison au joueur
5. Le vaisseau devient actif (retiré de l'inventaire, lié au joueur)

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
  - `generateStarterShipData(): array` - Génère les données du vaisseau de départ (nom, modèle, stats)
  - `assembleShipFromItem(User $user, InventoryItem $item): Ship` - Assemble le vaisseau depuis un objet inventaire
    - Extrait les métadonnées du vaisseau depuis `$item->metadata`
    - Crée le Ship avec les données extraites
    - Lie le vaisseau au joueur (`user.ship_id`)
    - Retire l'objet de l'inventaire
    - Retourne le Ship créé
  - `createStarterShip(User $user): Ship` - Crée directement un vaisseau (pour migration/compatibilité)
  - `assignShipToUser(User $user, Ship $ship): void` - Assigne un vaisseau à un utilisateur
  - `repairShip(Ship $ship, int $amount): void` - Répare le vaisseau (futur, Phase 2)
  - `refuelShip(Ship $ship, int $amount): void` - Recharge le carburant (futur, Phase 2)
  - `upgradeShip(Ship $ship, string $stat, int $amount): void` - Améliore une statistique (futur, Phase 2)
  - `generateShipName(): string` - Génère un nom aléatoire pour le vaisseau

### 3. Réception du Vaisseau dans l'Inventaire

- [ ] **Événement** :
  - `ShipReceived` : Déclenché lors de la réception du vaisseau dans l'inventaire
    - Données : User, InventoryItem (starter_ship)
  - `ShipAssembled` : Déclenché lors de l'assemblage du vaisseau (après activation)
    - Données : User, Ship

- [ ] **Listener - Réception** :
  - `AddStarterShipToInventory` : Écoute un événement approprié (fin d'onboarding ou après vérification email)
    - Génère les données du vaisseau via `ShipService::generateStarterShipData()`
    - Ajoute l'objet `starter_ship` à l'inventaire via `InventoryService::addItem()`
      - `item_type` : `ship`
      - `item_code` : `starter_ship`
      - `quantity` : 1
      - `is_activable` : true
      - `metadata` : Contient toutes les données du vaisseau (nom, modèle, stats)
    - Dispatch l'événement `ShipReceived`
    - Affiche la fenêtre de réception d'objets (via `InventoryService`)

- [ ] **Listener - Assemblage** :
  - `AssembleShipFromInventory` : Écoute l'activation de `starter_ship` depuis l'inventaire
    - Appelle `ShipService::assembleShipFromItem()` pour assembler le vaisseau
    - Dispatch l'événement `ShipAssembled`
    - Crée le message dans l'inbox
    - Crée la notification

- [ ] **Intégration avec l'onboarding** :
  - Option 1 : Recevoir le vaisseau dans l'inventaire à la fin de l'onboarding (étape 4)
  - Option 2 : Recevoir le vaisseau après vérification de l'email (si onboarding pas encore implémenté)
  - Décision à prendre selon l'état de l'onboarding (ISSUE-005)

- [ ] **Dépendance** : Nécessite ISSUE-010 (système d'inventaire) - ✅ **Priorisé** (High)

### 4. Message de Recrutement et Réception

- [ ] **Message automatique dans l'inbox** :
  - Créé automatiquement lors de la réception du vaisseau dans l'inventaire (via listener)
  - Type : `mission` ou `system`
  - Sujet : "Votre vaisseau d'exploration vous attend"
  - Contenu narratif expliquant :
    - Le recrutement sur l'avant-poste Stellar
    - La réception du vaisseau d'exploration dans l'inventaire
    - Comment activer et assembler le vaisseau
    - Les caractéristiques de base du vaisseau
    - Les prochaines étapes (assemblage, exploration, amélioration)
  - Métadonnées incluant l'ID de l'objet inventaire (`starter_ship`)

- [ ] **Message après assemblage** :
  - Créé automatiquement lors de l'assemblage du vaisseau (via listener)
  - Type : `mission` ou `system`
  - Sujet : "Votre vaisseau [nom] est prêt"
  - Contenu narratif expliquant :
    - Le vaisseau a été assemblé avec succès
    - Le vaisseau est maintenant actif et prêt pour l'exploration
    - Les prochaines étapes (décollage, exploration)
  - Métadonnées incluant l'ID du vaisseau assemblé

- [ ] **Template de message** :
  - Utiliser `MessageService::createMissionMessage()` ou `createSystemMessage()`
  - Template narratif cohérent avec l'univers Stellar (ambiance Alien, compagnie mystérieuse)
  - Inclure le nom du vaisseau et ses statistiques de base

### 5. Notifications

- [ ] **Notification de réception** :
  - Créée automatiquement lors de la réception du vaisseau dans l'inventaire (via listener)
  - Type : `ship_received`
  - Titre : "Vaisseau reçu"
  - Message : "Votre vaisseau d'exploration {ship_name} vous attend dans l'inventaire."
  - Données JSON : `item_id`, `ship_name`, `inventory_url` (vers `/inventory`)

- [ ] **Notification d'assemblage** :
  - Créée automatiquement lors de l'assemblage du vaisseau (via listener)
  - Type : `ship_assembled`
  - Titre : "Vaisseau assemblé"
  - Message : "Votre vaisseau {ship_name} a été assemblé et est prêt pour l'exploration."
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

**Flux de réception et assemblage** :
1. Fin d'onboarding (ou après vérification email) → Événement déclenché
2. Listener `AddStarterShipToInventory` :
   - Génère les données du vaisseau via `ShipService::generateStarterShipData()`
   - Ajoute l'objet `starter_ship` à l'inventaire via `InventoryService::addItem()`
   - Dispatch `ShipReceived`
3. Listener `CreateShipReceivedMessage` :
   - Crée le message dans l'inbox via `MessageService`
4. Listener `CreateShipReceivedNotification` :
   - Crée la notification via `NotificationService` (ISSUE-009 - ✅ Complété)
5. Listener `ShowReceiveItemModal` :
   - Affiche la fenêtre de réception d'objets (via `InventoryService`)

**Flux d'activation/assemblage** :
1. Joueur active `starter_ship` depuis l'inventaire → `InventoryService::activateItem()`
2. `InventoryService` appelle `ShipService::assembleShipFromItem()`
3. `ShipService::assembleShipFromItem()` :
   - Extrait les métadonnées du vaisseau depuis `$item->metadata`
   - Crée le Ship avec les données extraites
   - Lie le vaisseau au joueur (`user.ship_id`)
   - Retire l'objet de l'inventaire
   - Dispatch `ShipAssembled`
4. Listener `CreateShipAssembledMessage` :
   - Crée le message dans l'inbox via `MessageService`
5. Listener `CreateShipAssembledNotification` :
   - Crée la notification via `NotificationService`

### Intégration avec l'Existant

- **InventoryService** : Utiliser pour ajouter le vaisseau à l'inventaire et gérer l'activation (ISSUE-010 - ✅ Priorisé)
- **MessageService** : Utiliser pour créer les messages de réception et d'assemblage
- **Inbox** : Les messages apparaîtront dans l'inbox existante
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

## Dépendances

- **ISSUE-010** : Système d'inventaire MVP (nécessaire pour recevoir le vaisseau comme objet) - ✅ **Priorisé** (High)
  - Le vaisseau est d'abord un objet activable dans l'inventaire (`starter_ship`)
  - L'activation déclenche l'assemblage du vaisseau
  - Après assemblage, le vaisseau est retiré de l'inventaire et lié au joueur

## Références

- **[ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md)** - Architecture technique générale
- **[PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md)** - Vision métier et personas
- **[DRAFT-02-management-system.md](../game-design/drafts/DRAFT-02-management-system.md)** - Système de gestion (vaisseau, modules, crew)
- **[ISSUE-005-implement-onboarding-mvp.md](./ISSUE-005-implement-onboarding-mvp.md)** - Système d'onboarding
- **[ISSUE-010-implement-inventory-system.md](./ISSUE-010-implement-inventory-system.md)** - Système d'inventaire (intégration avec vaisseau)
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

#### 2025-01-27 - Alex (Product Manager) - Mise à jour intégration inventaire
**Statut** : À faire
**Détails** : Mise à jour pour intégrer le concept que le vaisseau est d'abord un objet dans l'inventaire (`starter_ship`) avant d'être assemblé. Le vaisseau est reçu dans l'inventaire comme objet activable, puis peut être activé pour être assemblé et devenir le vaisseau actif du joueur. Après activation, le vaisseau est retiré de l'inventaire (pas d'utilité de le garder car c'est le vaisseau actif). Dépend maintenant de ISSUE-010 (inventaire - ✅ Priorisé High).
**Notes** : Cette mise à jour permet d'intégrer le vaisseau dans le système d'inventaire, permettant de recevoir et activer des objets de manière cohérente. Le système doit être extensible pour gérer d'autres objets activables dans le futur.
