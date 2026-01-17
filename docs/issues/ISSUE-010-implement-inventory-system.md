# ISSUE-010 : Implémenter le système d'inventaire MVP

## Type
Feature

## Priorité
High

## Description

Implémenter un système d'inventaire permettant aux joueurs de gérer leurs ressources, matériaux, équipements et objets spéciaux (comme le vaisseau). Le système doit être simple, extensible et intégré avec les systèmes existants (scientific_data, ressources de planètes, etc.).

**MVP Phase 1** : Système de base avec gestion des items de base (ressources, matériaux), objets activables (vaisseau), interface de visualisation, fenêtre de réception d'objets, et synchronisation avec les ressources existantes.

## Contexte Métier

L'inventaire permet de :
- **Centraliser les ressources** : Unifier la gestion de toutes les ressources du joueur
- **Recevoir des objets** : Système de réception d'objets (vaisseau, équipements, etc.)
- **Objets activables** : Certains objets peuvent être activés pour être assemblés/utilisés (ex: vaisseau)
- **Progression visible** : Permettre au joueur de voir ses ressources accumulées
- **Fondation** : Préparer le terrain pour les systèmes futurs (amélioration de vaisseau, modules, craft)
- **Gestion simple** : Interface claire pour consulter et gérer ses ressources

**Valeur utilisateur** :
- **Visibilité** : Le joueur peut voir toutes ses ressources en un seul endroit
- **Réception d'objets** : Le joueur peut recevoir des objets (vaisseau, équipements) via une interface dédiée
- **Activation d'objets** : Le joueur peut activer des objets spéciaux (comme le vaisseau) pour les assembler/utiliser
- **Progression** : Comprendre l'accumulation de ressources pour les améliorations futures
- **Préparation** : Préparer les ressources nécessaires pour les améliorations de vaisseau

**Cas d'usage principal** : Le vaisseau d'exploration est d'abord reçu comme objet dans l'inventaire, puis activé pour être assemblé et devenir le vaisseau actif du joueur (lié au joueur, retiré de l'inventaire).

## Critères d'Acceptation

### 1. Modèle de Données

- [ ] **Table `inventory_items`** :
  - `id` (ULID) - Identifiant unique
  - `user_id` (ULID, FK → users.id) - Propriétaire de l'item
  - `item_type` (string) - Type d'item (`resource`, `material`, `equipment`, `ship`, `module`, etc.)
  - `item_code` (string) - Code unique de l'item (ex: "scientific_data", "metal_scrap", "energy_cells", "starter_ship")
  - `quantity` (integer, default: 0) - Quantité possédée (pour objets activables comme vaisseau, quantity = 1)
  - `is_activable` (boolean, default: false) - Indique si l'objet peut être activé/assemblé
  - `is_active` (boolean, default: false) - Indique si l'objet est actuellement activé (pour objets activables)
  - `metadata` (JSON, nullable) - Métadonnées additionnelles (qualité, niveau, propriétés spéciales, données du vaisseau avant assemblage)
  - `created_at`, `updated_at` - Timestamps
  - Index sur `user_id`, `item_type`, `item_code`, `is_activable`
  - Index composite unique sur `(user_id, item_code)` pour éviter les doublons

- [ ] **Migration** :
  - Créer la migration avec tous les champs et index
  - Foreign key vers `users.id` avec `onDelete('cascade')`
  - Contrainte unique sur `(user_id, item_code)`

- [ ] **Modèle Eloquent** :
  - `InventoryItem` dans `app/Models/InventoryItem.php`
  - Relations : `user()` : BelongsTo → User
  - Scopes :
    - `scopeForUser(Builder $query, User $user)` - Items d'un utilisateur
    - `scopeByType(Builder $query, string $type)` - Filtrer par type
    - `scopeByCode(Builder $query, string $code)` - Filtrer par code
  - Méthodes :
    - `addQuantity(int $amount): bool` - Ajouter de la quantité
    - `removeQuantity(int $amount): bool` - Retirer de la quantité
    - `hasEnough(int $required): bool` - Vérifier si quantité suffisante
    - `canActivate(): bool` - Vérifier si l'objet peut être activé (is_activable = true et is_active = false)
    - `activate(): bool` - Activer l'objet (is_active = true)

### 2. Service d'Inventaire

- [ ] **InventoryService** dans `app/Services/InventoryService.php` :
  - `addItem(User $user, string $itemCode, int $quantity, array $metadata = [], bool $isActivable = false): InventoryItem` - Ajouter un item
  - `removeItem(User $user, string $itemCode, int $quantity): bool` - Retirer un item
  - `getItem(User $user, string $itemCode): ?InventoryItem` - Récupérer un item
  - `getAllItems(User $user): Collection` - Récupérer tous les items
  - `getItemsByType(User $user, string $type): Collection` - Récupérer par type
  - `getActivableItems(User $user): Collection` - Récupérer les objets activables
  - `hasItem(User $user, string $itemCode, int $minQuantity = 1): bool` - Vérifier possession
  - `getTotalQuantity(User $user, string $itemCode): int` - Obtenir la quantité totale
  - `activateItem(User $user, string $itemCode): bool` - Activer un objet activable (déclenche l'action spécifique selon le type)
  - `transferItem(User $from, User $to, string $itemCode, int $quantity): bool` - Transférer (futur)

### 3. Types d'Items de Base (MVP Phase 1)

- [ ] **Ressources** :
  - `scientific_data` : Données scientifiques (synchroniser avec `users.scientific_data`)
  - `exploration_data` : Données d'exploration (futur)

- [ ] **Matériaux** :
  - `metal_scrap` : Métaux de récupération (pour améliorations)
  - `energy_cells` : Cellules d'énergie (pour le vaisseau)
  - `repair_kits` : Kits de réparation (pour réparer le vaisseau)
  - `crystal_fragments` : Fragments de cristaux (pour modules, futur)

- [ ] **Objets activables** (MVP Phase 1) :
  - `starter_ship` : Vaisseau d'exploration de départ (type: `ship`)
    - `is_activable` : true
    - `quantity` : 1
    - `metadata` : Contient les données du vaisseau (nom, modèle, stats) avant assemblage
    - Activation : Assemble le vaisseau et le lie au joueur (retiré de l'inventaire)

- [ ] **Équipements** (futur, Phase 2) :
  - Modules, équipements spéciaux, etc.

### 4. Synchronisation avec les Ressources Existantes

- [ ] **Migration des données** :
  - Créer un item `scientific_data` pour chaque utilisateur avec la valeur de `users.scientific_data`
  - Commande Artisan : `inventory:migrate-scientific-data` pour migrer les données existantes

- [ ] **Synchronisation bidirectionnelle** (optionnel pour MVP) :
  - Garder `users.scientific_data` comme source de vérité pour compatibilité
  - Synchroniser automatiquement avec `inventory_items` lors des modifications
  - Ou migrer complètement vers l'inventaire et supprimer `users.scientific_data` (décision à prendre)

### 5. Interface Utilisateur

- [ ] **Page Inventaire** :
  - Route : `/inventory` (middleware `auth`)
  - Composant Livewire : `Inventory` dans `app/Livewire/Inventory.php`
  - Vue : `resources/views/livewire/inventory.blade.php`
  - Affichage :
    - Liste des items groupés par type (Ressources, Matériaux, Objets activables, Équipements)
    - Pour chaque item : nom, quantité, icône/visuel
    - Pour objets activables : Bouton "Activer" ou "Assembler" visible si `canActivate()`
    - Recherche par nom/code
    - Filtres par type
    - Détails de l'item au clic (description, utilisation, etc.)

- [ ] **Fenêtre de Réception d'Objets** :
  - Composant Livewire : `ReceiveItem` dans `app/Livewire/ReceiveItem.php` (modal)
  - Vue : `resources/views/livewire/receive-item.blade.php`
  - Affichage :
    - Modal/Overlay avec animation d'ouverture
    - Titre : "Nouvel objet reçu"
    - Affichage de l'objet : nom, description, visuel/icône
    - Bouton "Ajouter à l'inventaire" ou "Recevoir"
    - Animation de réception (particules, effet visuel)
  - Utilisation :
    - Appelée automatiquement lors de la réception d'un objet (vaisseau, équipement)
    - Peut être déclenchée manuellement via `InventoryService::showReceiveItemModal()`
  - Intégration :
    - Notification visuelle si objets en attente de réception
    - Badge sur l'icône inventaire si objets non récupérés

- [ ] **Action d'Activation d'Objet** :
  - Méthode Livewire : `activateItem(string $itemCode)` dans `Inventory`
  - Confirmation : Modal de confirmation pour objets importants (comme vaisseau)
  - Traitement :
    - Appelle `InventoryService::activateItem()`
    - Selon le type d'objet, déclenche l'action appropriée :
      - `starter_ship` : Assemble le vaisseau via `ShipService::assembleShipFromItem()`
    - Retire l'objet de l'inventaire après activation réussie
    - Affiche un message de succès avec animation

- [ ] **Intégration dans la navigation** :
  - Lien "Inventaire" dans la navigation principale
  - Badge avec nombre total d'items (optionnel)
  - Badge avec nombre d'objets activables disponibles (optionnel)

- [ ] **Résumé dans le Dashboard** :
  - Afficher les ressources principales (scientific_data, matériaux de base)
  - Afficher les objets activables disponibles (ex: "Vaisseau prêt à assembler")
  - Lien vers la page inventaire complète

### 6. Génération Automatique d'Items

- [ ] **Événements déclencheurs** :
  - `MiniGameCompleted` (ISSUE-007) → Ajouter `scientific_data` à l'inventaire
  - `ResourceAdded` (futur) → Ajouter les ressources à l'inventaire
  - `ShipRepaired` (futur) → Consommer `repair_kits` de l'inventaire

- [ ] **Listeners** :
  - `AddScientificDataToInventory` - Ajouter données scientifiques après mini-jeu
  - `AddStarterShipToInventory` - Ajouter le vaisseau de départ à l'inventaire (déclenché lors de l'onboarding ou après vérification email)
  - Architecture extensible pour ajouter facilement de nouveaux listeners

- [ ] **Gestion de l'Activation du Vaisseau** :
  - Lors de l'activation de `starter_ship` :
    - Créer le vaisseau via `ShipService::assembleShipFromItem(InventoryItem $item)`
    - Extraire les métadonnées du vaisseau depuis `$item->metadata`
    - Créer le Ship avec les données extraites
    - Lier le vaisseau au joueur (`user.ship_id`)
    - Retirer l'objet de l'inventaire
    - Dispatch événement `ShipAssembled` (au lieu de `ShipAssigned`)
    - Créer message dans l'inbox : "Votre vaisseau [nom] a été assemblé et est prêt"
    - Créer notification : "Votre vaisseau est prêt"

## Détails Techniques

### Structure des Métadonnées JSON

**Exemple pour matériaux** :
```json
{
  "quality": "standard",
  "source": "scavenging",
  "obtained_at": "2025-01-27T10:00:00Z"
}
```

**Exemple pour équipements** (futur) :
```json
{
  "level": 1,
  "rarity": "common",
  "properties": {
    "bonus_scanner": 5
  }
}
```

**Exemple pour vaisseau (starter_ship)** :
```json
{
  "ship_name": "Stellar Explorer #1234",
  "ship_model": "Stellar Explorer",
  "level": 1,
  "fuel_capacity": 100,
  "fuel_current": 100,
  "hull_integrity": 100,
  "engine_power": 50,
  "scanner_quality": 50,
  "shield_strength": 50,
  "received_at": "2025-01-27T10:00:00Z"
}
```

### Performance

- **Index** : Index optimisés pour requêtes fréquentes (`user_id`, `item_code`)
- **Cache** : Utiliser `#[Computed]` dans Livewire pour cache automatique
- **Pagination** : Paginer les listes d'items si nécessaire (au-delà de 50 items)

### Sécurité

- **Scope `forUser()`** : Toujours utiliser le scope pour garantir qu'un utilisateur ne peut voir que ses items
- **Validation** : Valider les codes d'items autorisés
- **Quantités** : Empêcher les quantités négatives

## Notes

### Scope MVP Phase 1

- **Réception d'objets** : ✅ Inclus (fenêtre de réception, système de base)
- **Objets activables** : ✅ Inclus (vaisseau de départ)
- **Activation de vaisseau** : ✅ Inclus (assemblage du vaisseau depuis l'inventaire)
- **Crafting** : Non inclus (Phase 2)
- **Échange entre joueurs** : Non inclus (Phase 2)
- **Équipements** : Non inclus (Phase 2)
- **Modules** : Non inclus (Phase 2)
- **Amélioration via inventaire** : Interface de base, logique complète en Phase 2

### Principes de Design

1. **Simplicité** : Interface claire et intuitive
2. **Groupement** : Items groupés par type pour faciliter la navigation
3. **Visibilité** : Quantités visibles et compréhensibles
4. **Extensibilité** : Architecture permettant d'ajouter facilement de nouveaux types d'items

### Extensibilité

L'architecture doit permettre facilement :
- L'ajout de nouveaux types d'items
- L'intégration avec les systèmes futurs (vaisseau, modules, craft)
- La personnalisation des métadonnées selon le type d'item
- L'ajout de propriétés spéciales aux items

## Dépendances

- **ISSUE-011** : Système de vaisseau MVP (nécessaire pour l'assemblage du vaisseau depuis l'inventaire)
  - Le vaisseau est d'abord un objet dans l'inventaire (`starter_ship`)
  - L'activation assemble le vaisseau et le lie au joueur
  - Après activation, le vaisseau n'est plus dans l'inventaire (lié directement au joueur)

## Références

- **[ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md)** - Architecture technique générale
- **[ISSUE-007-implement-minigame-base-system.md](./ISSUE-007-implement-minigame-base-system.md)** - Système de mini-jeux (scientific_data)
- **[ISSUE-011-implement-ship-system-mvp.md](./ISSUE-011-implement-ship-system-mvp.md)** - Système de vaisseau MVP (intégration avec inventaire)
- **[DRAFT-02-management-system.md](../game-design/drafts/DRAFT-02-management-system.md)** - Système de gestion (ressources, matériaux)

## Suivi et Historique

### Statut

À faire

### Historique

#### 2025-01-27 - Alex (Product Manager) - Création de l'issue
**Statut** : À faire
**Détails** : Issue créée pour implémenter le système d'inventaire MVP. Cette feature permet de centraliser la gestion des ressources et de préparer les systèmes futurs (amélioration de vaisseau, modules, craft). Le MVP Phase 1 se concentre sur les fonctionnalités de base : gestion des items, interface de visualisation, et synchronisation avec les ressources existantes.
**GitHub** : [#19](https://github.com/PiLep/space-xplorer/issues/19)
**Notes** : Issue de priorité moyenne car peut être développée en parallèle avec les autres features. Le système doit être extensible pour faciliter l'ajout de nouveaux types d'items dans le futur.

#### 2025-01-27 - Alex (Product Manager) - Mise à jour priorité et objets activables
**Statut** : À faire
**Détails** : Priorité changée de Medium à High. Ajout du concept d'objets activables (comme le vaisseau). Le vaisseau est maintenant reçu comme objet dans l'inventaire (`starter_ship`), puis activé pour être assemblé et devenir le vaisseau actif du joueur. Ajout de la fenêtre de réception d'objets. Le système doit permettre de recevoir et activer des objets, avec le vaisseau comme premier cas d'usage.
**Notes** : Cette mise à jour permet de prioriser l'inventaire car il est nécessaire pour recevoir le vaisseau. Le vaisseau devient un objet activable dans l'inventaire avant d'être assemblé et lié au joueur. Après activation, le vaisseau n'est plus dans l'inventaire (pas d'utilité de le garder car c'est le vaisseau actif).
