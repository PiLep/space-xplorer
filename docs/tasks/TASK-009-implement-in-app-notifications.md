# TASK-009 : Implémenter le système de notifications in-app (MVP)

## Issue Associée

[ISSUE-009-implement-in-app-notifications.md](../issues/ISSUE-009-implement-in-app-notifications.md)

## Vue d'Ensemble

Implémenter un système de notifications in-app permettant d'informer les joueurs des événements importants du jeu et de les rediriger vers les systèmes concernés (vaisseau, inventaire, missions, inbox). Le système doit être léger, performant et intégré dans l'interface existante. Le MVP Phase 1 se concentre sur les fonctionnalités de base : badge de notifications dans la navigation, dropdown de notifications récentes, et génération automatique de notifications pour les messages importants.

**Architecture** : Le système utilise une table `notifications` dédiée, un service `NotificationService` pour la gestion, et des listeners pour générer automatiquement des notifications lors d'événements importants. L'interface utilisateur s'intègre dans le composant de navigation existant avec un badge et un dropdown Livewire.

**Distinction clé** :
- **Inbox** : Boîte mail Stellar (employé) - Messages narratifs de la compagnie
- **Notifications** : Système d'événements du jeu - Alertes qui pointent vers les systèmes (vaisseau, inventaire, missions, inbox)

## Suivi et Historique

### Statut

En cours (Review architecturale approuvée)

### Historique

#### 2025-01-27 - Sam (Lead Developer) - Création du plan
**Statut** : À faire
**Détails** : Plan de développement créé pour implémenter le système de notifications in-app. Le plan décompose le travail en phases logiques : modèle de données, service, génération automatique, interface utilisateur, et intégration avec l'existant.
**Notes** : Le système doit être performant et extensible pour permettre l'ajout facile de nouveaux types de notifications dans le futur.

#### 2025-01-27 - Sam (Lead Developer) - Clarification distinction Notifications vs Inbox
**Statut** : À faire
**Détails** : Ajout d'une section importante dans les "Notes Techniques" pour clarifier la distinction entre notifications et inbox. Les notifications sont des alertes courtes qui pointent vers les éléments (messages, vaisseaux, etc.) mais ne les remplacent pas. Les messages restent dans l'inbox indépendamment des notifications, avec des statuts de lecture séparés. Mise à jour des tâches et tests pour refléter cette séparation.
**Notes** : Les notifications ne doivent pas dupliquer le contenu des messages. Une notification pour un message important doit être un résumé court qui redirige vers le message dans l'inbox via `/inbox?message={id}`.

#### 2025-01-27 - Sam (Lead Developer) - Clarification vision Inbox vs Notifications
**Statut** : À faire
**Détails** : Clarification importante de la vision des deux systèmes :
- **Inbox** : Boîte mail Stellar (employé) - Messages narratifs de la compagnie Stellar, communication narrative
- **Notifications** : Système d'événements du jeu - Alertes qui informent des événements et redirigent vers les systèmes du jeu (vaisseau, inventaire, missions, inbox)

Les notifications sont le système central pour connaître les événements du jeu et accéder aux différents systèmes. Chaque notification pointe vers le système concerné (vaisseau → `/ships/{id}`, inventaire → `/inventory`, inbox → `/inbox?message={id}`). Mise à jour de la section "Distinction Notifications vs Inbox", des types de notifications, et des tâches pour refléter cette vision.
**Notes** : Les notifications sont le point d'entrée vers les systèmes du jeu. L'inbox est un système parmi d'autres (vaisseau, inventaire, missions) vers lequel les notifications peuvent pointer.

#### 2025-01-27 - Morgan (Architect) - Review architecturale
**Statut** : En cours (Review architecturale approuvée)
**Détails** : Review architecturale complète effectuée. Le plan est approuvé avec recommandations. Architecture cohérente avec le projet (ULIDs, Events & Listeners, Services, Livewire 3.6). Distinction claire entre notifications et inbox. Recommandations principales : clarifier le choix de configuration (`config/notifications.php`), détailler la gestion d'erreurs dans le listener, validation stricte des types de notifications (High priority), ajouter un index sur `(user_id, created_at DESC)` pour les performances.
**Review** : [ARCHITECT-REVIEW-009-implement-in-app-notifications.md](../reviews/ARCHITECT-REVIEW-009-implement-in-app-notifications.md)
**Notes** : Le plan peut être implémenté tel quel, en tenant compte des recommandations. Aucune modification majeure demandée.

## Objectifs Techniques

- Créer le modèle de données `Notification` avec support pour différents types de notifications
- Développer le service `NotificationService` pour créer et gérer les notifications
- Intégrer la génération automatique de notifications avec les événements Laravel existants
- Créer le composant Livewire `NotificationBadge` pour le badge et dropdown dans la navigation
- Développer la page de notifications complète (optionnel pour MVP)
- Intégrer le système dans la navigation existante

## Architecture & Design

- **Modèle** : `Notification` dans `app/Models/Notification.php` avec relation vers `User`
- **Migration** : Table `notifications` avec champs ULID, relations, types, statuts, données JSON
- **Service** : `NotificationService` dans `app/Services/NotificationService.php` pour créer et gérer les notifications
- **Listeners** : `CreateNotificationOnImportantMessage` pour créer automatiquement des notifications lors de messages importants
- **Composant Livewire** : `NotificationBadge` dans `app/Livewire/NotificationBadge.php` pour le badge et dropdown
- **Composant Livewire** : `Notifications` dans `app/Livewire/Notifications.php` pour la page complète (optionnel MVP)
- **Intégration** : Ajout du badge dans le composant de navigation existant

## Tâches de Développement

### Phase 1 : Modèle de Données et Migrations

#### Tâche 1.1 : Créer la migration pour la table notifications
- **Description** : Créer la migration avec tous les champs nécessaires :
  - `id` (ULID, primary)
  - `user_id` (ULID, FK → users.id, onDelete cascade)
  - `type` (string) - Type de notification (`message_important`, `ship_assigned`, `resource_added`, etc.)
  - `title` (string) - Titre court de la notification
  - `message` (text) - Message de la notification (max 200 caractères)
  - `data` (JSON, nullable) - Données additionnelles (ID vaisseau, quantité ressources, lien vers message, etc.)
  - `is_read` (boolean, default: false) - Statut de lecture
  - `read_at` (timestamp, nullable) - Date de lecture
  - `created_at`, `updated_at` - Timestamps
  - Index sur `user_id`, `is_read`, `type`, `created_at`
  - Index composite sur `(user_id, is_read)` pour requêtes fréquentes
- **Fichiers concernés** : `database/migrations/YYYY_MM_DD_HHMMSS_create_notifications_table.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Vérifier la structure de la table et les index

#### Tâche 1.2 : Créer le modèle Notification
- **Description** : Créer le modèle Eloquent `Notification` avec :
  - Trait `HasUlids` pour les identifiants ULID
  - Relations : `user()` : BelongsTo → User
  - Scopes :
    - `scopeUnread(Builder $query)` - Notifications non lues
    - `scopeRead(Builder $query)` - Notifications lues
    - `scopeForUser(Builder $query, User $user)` - Notifications d'un utilisateur
    - `scopeByType(Builder $query, string $type)` - Filtrer par type
  - Méthodes :
    - `markAsRead(): bool` - Marquer comme lue
    - `markAsUnread(): bool` - Marquer comme non lue
  - Casts : `is_read` (boolean), `read_at` (datetime), `data` (array)
- **Fichiers concernés** : `app/Models/Notification.php`
- **Estimation** : 45 min
- **Dépendances** : Tâche 1.1
- **Tests** : Tests unitaires du modèle (relations, scopes, méthodes)

### Phase 2 : Service de Notifications

#### Tâche 2.1 : Créer NotificationService
- **Description** : Créer le service `NotificationService` avec les méthodes suivantes :
  - `create(User $user, string $type, string $title, string $message, array $data = []): Notification` - Créer une notification
  - `markAsRead(Notification $notification): bool` - Marquer comme lue
  - `markAllAsRead(User $user): int` - Marquer toutes comme lues
  - `getUnreadCount(User $user): int` - Compter les notifications non lues
  - `getNotificationsForUser(User $user, int $limit = 20): Collection` - Récupérer les notifications récentes
  - `getUnreadNotificationsForUser(User $user, int $limit = 10): Collection` - Récupérer les non lues
  - Validation des types de notifications autorisés
  - Utilisation du scope `forUser()` pour sécurité
- **Fichiers concernés** : `app/Services/NotificationService.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 1.2
- **Tests** : Tests unitaires du service (création, marquage, comptage, récupération)

#### Tâche 2.2 : Créer la configuration des types de notifications
- **Description** : Créer un fichier de configuration ou une classe pour définir les types de notifications autorisés et leurs métadonnées :
  - Types MVP : `message_important`, `ship_assigned` (futur), `resource_added` (futur)
  - Structure des données JSON pour chaque type
  - Validation des types dans `NotificationService`
- **Fichiers concernés** : `config/notifications.php` ou `app/Data/NotificationTypes.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Tests de la configuration et validation

### Phase 3 : Génération Automatique de Notifications

#### Tâche 3.1 : Créer le listener CreateNotificationOnImportantMessage
- **Description** : Créer le listener qui écoute l'événement `MessageReceived` et crée une notification si le message est important (`is_important = true`) :
  - Vérifier si `$message->is_important === true`
  - Créer une notification de type `message_important` via `NotificationService`
  - **Important** : La notification est une alerte courte qui pointe vers l'inbox (boîte mail Stellar), pas une duplication du message :
    - `title` : "Nouveau message important" ou similaire
    - `message` : Résumé court avec le sujet du message (ex: "Nouveau message : {subject}") - max 200 caractères
    - Ne pas inclure le contenu narratif complet du message dans la notification
  - Inclure dans `data` : `message_id`, `inbox_url` avec paramètre pour ouvrir directement le message (`/inbox?message={message_id}`)
  - **Rôle** : Cette notification est un cas particulier où une notification pointe vers l'inbox (boîte mail Stellar) pour informer d'un message important
  - Gérer les erreurs avec try-catch pour ne pas bloquer l'événement
  - **Séparation** : La notification et le message sont indépendants (statuts de lecture séparés)
- **Fichiers concernés** : `app/Listeners/CreateNotificationOnImportantMessage.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 2.1
- **Tests** : Tests du listener (création de notification pour message important, pas de notification pour message normal, vérifier que le contenu du message n'est pas dupliqué, vérifier que la notification pointe vers l'inbox)

#### Tâche 3.2 : Enregistrer le listener dans EventServiceProvider
- **Description** : Enregistrer le listener `CreateNotificationOnImportantMessage` pour l'événement `MessageReceived` dans `EventServiceProvider` :
  - Ajouter dans `$listen` : `MessageReceived::class => [CreateNotificationOnImportantMessage::class]`
- **Fichiers concernés** : `app/Providers/EventServiceProvider.php`
- **Estimation** : 10 min
- **Dépendances** : Tâche 3.1
- **Tests** : Vérifier que le listener est bien enregistré

### Phase 4 : Interface Utilisateur - Badge et Dropdown

#### Tâche 4.1 : Créer le composant Livewire NotificationBadge
- **Description** : Créer le composant Livewire `NotificationBadge` pour le badge et dropdown :
  - Propriété `#[Computed]` pour `unreadCount()` - Utiliser `NotificationService::getUnreadCount()`
  - Propriété `#[Computed]` pour `recentNotifications()` - Utiliser `NotificationService::getUnreadNotificationsForUser()` avec limite 10
  - Méthode `markAsRead(string $notificationId)` - Marquer une notification comme lue
  - Méthode `markAllAsRead()` - Marquer toutes comme lues
  - Méthode `refresh()` - Rafraîchir les notifications (pour polling optionnel)
  - Utiliser `#[Layout('layouts.app')]` si nécessaire (ou intégration dans navigation)
- **Fichiers concernés** : `app/Livewire/NotificationBadge.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 2.1
- **Tests** : Tests du composant (compteur, récupération, marquage)

#### Tâche 4.2 : Créer la vue Blade pour NotificationBadge
- **Description** : Créer la vue Blade pour le badge et dropdown :
  - Badge avec icône de cloche et compteur de notifications non lues (rouge si > 0)
  - Dropdown avec les 10 dernières notifications non lues
  - Format compact : titre + message court + timestamp relatif
  - **Lien depuis notification** : Le clic sur une notification doit rediriger vers le système concerné selon le type :
    - `message_important` → `/inbox?message={message_id}` (ouvrir le message dans l'inbox)
    - `ship_assigned` → `/ships/{ship_id}` (voir le vaisseau)
    - `resource_added` → `/inventory` (voir l'inventaire)
    - Utiliser le champ `data` de la notification pour déterminer l'URL de redirection
  - Bouton "Marquer tout comme lu"
  - Lien "Voir toutes les notifications" vers `/notifications` (futur)
  - Fermeture automatique après clic sur une notification
  - Style cohérent avec le design system terminal
  - Utiliser Alpine.js pour l'ouverture/fermeture du dropdown
  - **Important** : Le clic sur une notification doit rediriger vers le système du jeu concerné (vaisseau, inventaire, missions, inbox), pas afficher le contenu dans le dropdown
- **Fichiers concernés** : `resources/views/livewire/notification-badge.blade.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 4.1
- **Tests** : Vérifier l'affichage et les interactions, vérifier que le clic redirige vers le bon système selon le type de notification

#### Tâche 4.3 : Intégrer le badge dans la navigation
- **Description** : Intégrer le composant `NotificationBadge` dans le composant de navigation existant :
  - Ajouter le badge dans la navigation principale (variant `top` ou `terminal`)
  - Positionner le badge à côté des autres éléments de navigation
  - S'assurer que le badge est visible uniquement pour les utilisateurs authentifiés
  - Style cohérent avec le design system
- **Fichiers concernés** : `resources/views/components/navigation.blade.php`, `resources/views/layouts/app.blade.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 4.2
- **Tests** : Vérifier l'intégration visuelle et fonctionnelle

#### Tâche 4.4 : Ajouter le polling optionnel pour mise à jour automatique
- **Description** : Ajouter le polling optionnel pour mettre à jour automatiquement le compteur et les notifications :
  - Utiliser `wire:poll.30s` sur le composant `NotificationBadge` pour polling toutes les 30 secondes
  - Ou utiliser Alpine.js avec `setInterval` pour rafraîchir via Livewire
  - Optionnel : désactiver le polling si l'utilisateur n'est pas sur la page active
- **Fichiers concernés** : `resources/views/livewire/notification-badge.blade.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 4.2
- **Tests** : Vérifier que le polling fonctionne correctement

### Phase 5 : Page de Notifications (Optionnel MVP)

#### Tâche 5.1 : Créer le composant Livewire Notifications
- **Description** : Créer le composant Livewire `Notifications` pour la page complète :
  - Propriété `#[Computed]` pour `notifications()` - Pagination avec `NotificationService::getNotificationsForUser()`
  - Filtres : Toutes / Non lues / Par type
  - Méthode `markAsRead(string $notificationId)` - Marquer comme lue
  - Méthode `markAllAsRead()` - Marquer toutes comme lues
  - Méthode `filter(string $filter)` - Changer le filtre
  - Utiliser `#[Layout('layouts.app')]`
- **Fichiers concernés** : `app/Livewire/Notifications.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 2.1
- **Tests** : Tests du composant (pagination, filtres, marquage)

#### Tâche 5.2 : Créer la vue Blade pour Notifications
- **Description** : Créer la vue Blade pour la page de notifications :
  - Liste complète des notifications avec pagination
  - Filtres : Toutes / Non lues / Par type (dropdown)
  - Actions : Marquer comme lue / Marquer toutes comme lues
  - Format détaillé : titre + message + timestamp + données additionnelles
  - Liens vers les actions associées (inbox, vaisseau, etc.)
  - Style cohérent avec le design system terminal
- **Fichiers concernés** : `resources/views/livewire/notifications.blade.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 5.1
- **Tests** : Vérifier l'affichage et les interactions

#### Tâche 5.3 : Ajouter la route pour la page de notifications
- **Description** : Ajouter la route `/notifications` avec middleware `auth` :
  - Route : `Route::get('/notifications', Notifications::class)->middleware('auth')->name('notifications');`
- **Fichiers concernés** : `routes/web.php`
- **Estimation** : 10 min
- **Dépendances** : Tâche 5.1
- **Tests** : Vérifier que la route fonctionne et nécessite l'authentification

## Ordre d'Exécution

1. Phase 1 : Modèle de Données et Migrations (Tâches 1.1, 1.2)
2. Phase 2 : Service de Notifications (Tâches 2.1, 2.2)
3. Phase 3 : Génération Automatique de Notifications (Tâches 3.1, 3.2)
4. Phase 4 : Interface Utilisateur - Badge et Dropdown (Tâches 4.1, 4.2, 4.3, 4.4)
5. Phase 5 : Page de Notifications (Optionnel MVP) (Tâches 5.1, 5.2, 5.3)

## Migrations de Base de Données

- [ ] Migration : Créer la table notifications avec tous les champs et index

## Endpoints API

Aucun endpoint API nécessaire pour le MVP Phase 1. Le système utilise directement Livewire et les services Laravel.

**Endpoints futurs (Phase 2)** :
- `GET /api/notifications` - Liste des notifications avec pagination
- `GET /api/notifications/unread` - Notifications non lues
- `PUT /api/notifications/{id}/read` - Marquer comme lue
- `PUT /api/notifications/read-all` - Marquer toutes comme lues

## Événements & Listeners

### Nouveaux Listeners

- `CreateNotificationOnImportantMessage` : Crée une notification lorsqu'un message important est reçu
  - Écoute : `MessageReceived`
  - Action : Vérifie si `is_important = true`, crée une notification via `NotificationService`

### Événements Existants Utilisés

- `MessageReceived` : Déjà existant, utilisé pour déclencher la création de notifications

## Services & Classes

### Nouveaux Services

- `NotificationService` : Service de gestion des notifications
  - Méthodes :
    - `create()` : Créer une notification
    - `markAsRead()` : Marquer comme lue
    - `markAllAsRead()` : Marquer toutes comme lues
    - `getUnreadCount()` : Compter les notifications non lues
    - `getNotificationsForUser()` : Récupérer les notifications récentes
    - `getUnreadNotificationsForUser()` : Récupérer les non lues

### Classes Modifiées

- `EventServiceProvider` : Ajout du listener `CreateNotificationOnImportantMessage` pour `MessageReceived`
- `navigation.blade.php` : Intégration du composant `NotificationBadge`

## Tests

### Tests Unitaires

- [ ] Test : NotificationService crée une notification valide
- [ ] Test : NotificationService marque une notification comme lue
- [ ] Test : NotificationService marque toutes les notifications comme lues
- [ ] Test : NotificationService compte correctement les notifications non lues
- [ ] Test : NotificationService récupère les notifications pour un utilisateur
- [ ] Test : Notification model scopes fonctionnent correctement (unread, read, forUser, byType)
- [ ] Test : Notification model markAsRead() et markAsUnread() fonctionnent

### Tests d'Intégration

- [ ] Test : CreateNotificationOnImportantMessage crée une notification pour un message important
- [ ] Test : CreateNotificationOnImportantMessage ne crée pas de notification pour un message normal
- [ ] Test : CreateNotificationOnImportantMessage ne duplique pas le contenu du message dans la notification
- [ ] Test : La notification créée contient un lien vers le message dans l'inbox (`/inbox?message={id}`)
- [ ] Test : Marquer une notification comme lue n'affecte pas le statut de lecture du message dans l'inbox
- [ ] Test : Le listener est bien enregistré dans EventServiceProvider
- [ ] Test : NotificationBadge affiche le bon compteur de notifications non lues
- [ ] Test : NotificationBadge récupère les notifications récentes
- [ ] Test : NotificationBadge marque une notification comme lue
- [ ] Test : NotificationBadge marque toutes les notifications comme lues
- [ ] Test : Clic sur une notification de type `message_important` redirige vers `/inbox?message={id}`

### Tests Fonctionnels

- [ ] Test : Badge de notifications s'affiche dans la navigation
- [ ] Test : Dropdown de notifications s'ouvre et affiche les notifications
- [ ] Test : Clic sur une notification marque comme lue et ferme le dropdown
- [ ] Test : Clic sur une notification de type `message_important` redirige vers l'inbox avec le message ouvert
- [ ] Test : Le message reste dans l'inbox après création de la notification
- [ ] Test : Marquer une notification comme lue n'affecte pas le statut du message dans l'inbox
- [ ] Test : Bouton "Marquer tout comme lu" fonctionne
- [ ] Test : Polling met à jour automatiquement le compteur (si implémenté)
- [ ] Test : Page de notifications affiche toutes les notifications avec pagination (si implémentée)
- [ ] Test : Filtres de la page de notifications fonctionnent (si implémentée)

## Documentation

- [ ] Mettre à jour ARCHITECTURE.md avec le système de notifications
- [ ] Documenter NotificationService dans le code
- [ ] Ajouter des commentaires dans le code
- [ ] Documenter les types de notifications dans la configuration

## Notes Techniques

### Performance

- **Compteur** : Utiliser `#[Computed]` dans Livewire pour cache automatique du compteur
- **Polling** : Optionnel, maximum 30 secondes pour éviter la surcharge
- **Index** : Index optimisés pour requêtes fréquentes (`user_id`, `is_read`, index composite)
- **Limite** : Limiter les notifications récupérées (10 pour dropdown, 20 pour page)

### Sécurité

- **Scope `forUser()`** : Toujours utiliser le scope pour garantir qu'un utilisateur ne peut voir que ses notifications
- **Validation** : Valider les types de notifications autorisés dans `NotificationService`
- **Rate limiting** : Limiter le nombre de notifications créées par événement (futur)

### Extensibilité

- **Types de notifications** : Architecture extensible pour ajouter facilement de nouveaux types
- **Listeners** : Facile d'ajouter de nouveaux listeners pour d'autres événements
- **Données JSON** : Structure flexible pour stocker des données additionnelles

### Distinction Notifications vs Inbox

**⚠️ IMPORTANT** : Les notifications et l'inbox sont deux systèmes complémentaires mais avec des rôles distincts :

#### Inbox : Boîte mail Stellar (Employé)
- **Rôle** : Communication narrative avec la compagnie Stellar
- **Contenu** : Messages détaillés, narratifs, consultables à tout moment
- **Types** : Messages de bienvenue, découvertes, missions, alertes système de Stellar
- **Usage** : Lire les communications de la compagnie, suivre l'histoire narrative
- **Persistance** : Les messages restent dans l'inbox indéfiniment (sauf suppression manuelle)

#### Notifications : Système d'événements du jeu
- **Rôle** : Informer des événements importants du jeu et rediriger vers les systèmes concernés
- **Contenu** : Alertes courtes (max 200 caractères) qui pointent vers les systèmes du jeu
- **Types** : Vaisseau attribué, ressources ajoutées, missions complétées, messages importants, etc.
- **Usage** : Connaître rapidement les événements et accéder aux systèmes concernés
- **Persistance** : Notifications temporaires, peuvent être archivées automatiquement

**Règles de séparation** :

1. **Notifications pour événements du jeu** :
   - Les notifications pointent vers les différents systèmes du jeu (vaisseau, inventaire, missions, inbox)
   - Chaque notification redirige vers le système concerné avec l'élément spécifique
   - Exemples :
     - `ship_assigned` → `/ships/{ship_id}` (système vaisseau)
     - `resource_added` → `/inventory` (système inventaire)
     - `mission_completed` → `/missions/{mission_id}` (système missions)
     - `message_important` → `/inbox?message={message_id}` (système inbox)

2. **Notification pour message important (cas particulier)** :
   - La notification est une alerte courte qui dit "Nouveau message important : [sujet]"
   - Le message reste dans l'inbox avec son contenu narratif complet
   - La notification redirige vers l'inbox (`/inbox?message={id}`) pour lire le message
   - C'est le seul cas où une notification pointe vers l'inbox
   - Marquer la notification comme lue n'affecte pas le statut de lecture du message dans l'inbox

3. **Pas de duplication de contenu** :
   - La notification ne doit pas contenir le contenu complet (message, détails vaisseau, etc.)
   - La notification doit être un résumé court qui informe de l'événement
   - Le contenu détaillé reste dans le système concerné (inbox, vaisseau, inventaire, etc.)

4. **Indépendance des statuts** :
   - `Notification.is_read` est indépendant des autres systèmes
   - Un message peut être lu dans l'inbox sans que la notification soit marquée comme lue
   - Une notification peut être marquée comme lue sans affecter les autres systèmes

### Intégration avec l'Existant

- **Messages importants** : Utilise le champ `is_important` existant dans le modèle `Message`
- **Événement MessageReceived** : Utilise l'événement existant sans modification
- **Navigation** : Intègre dans le composant de navigation existant sans modification majeure
- **Lien vers inbox** : Les notifications de type `message_important` doivent rediriger vers `/inbox?message={message_id}` pour ouvrir directement le message dans l'inbox

### Types de Notifications MVP Phase 1

Les notifications sont des alertes pour les événements du jeu qui redirigent vers les systèmes concernés :

- `message_important` : Message important reçu dans la boîte mail Stellar (inbox)
  - **Rôle** : Alerte pour un message important de Stellar
  - **Contenu** : Résumé court (ex: "Nouveau message important : [sujet]") - max 200 caractères
  - **Données JSON** : `{ "message_id": "...", "inbox_url": "/inbox?message={message_id}" }`
  - **Action** : Redirige vers `/inbox?message={message_id}` pour ouvrir le message dans l'inbox
  - **Système cible** : Inbox (boîte mail Stellar)
  - **Indépendance** : La notification et le message ont des statuts de lecture séparés

- `ship_assigned` : Vaisseau attribué (futur, ISSUE-011)
  - **Rôle** : Alerte pour l'attribution d'un vaisseau
  - **Contenu** : Résumé court (ex: "Vaisseau attribué : [nom]") - max 200 caractères
  - **Données JSON** : `{ "ship_id": "...", "ship_name": "...", "ship_url": "/ships/{ship_id}" }`
  - **Action** : Redirige vers `/ships/{ship_id}` pour voir le vaisseau
  - **Système cible** : Système vaisseau

- `resource_added` : Nouvelles ressources ajoutées à l'inventaire (futur, ISSUE-010)
  - **Rôle** : Alerte pour l'ajout de ressources à l'inventaire
  - **Contenu** : Résumé court (ex: "Nouvelles ressources : [quantité] [type]") - max 200 caractères
  - **Données JSON** : `{ "resource_type": "...", "quantity": 10, "inventory_url": "/inventory" }`
  - **Action** : Redirige vers `/inventory` pour voir l'inventaire
  - **Système cible** : Système inventaire

**Principe général** : Chaque notification pointe vers le système du jeu concerné (vaisseau, inventaire, missions, inbox) pour permettre au joueur d'accéder rapidement à l'événement.

## Références

- [ISSUE-009-implement-in-app-notifications.md](../issues/ISSUE-009-implement-in-app-notifications.md)
- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Architecture technique générale, système d'événements
- [STACK.md](../memory_bank/STACK.md) - Stack technique (Laravel, Livewire)
- [ISSUE-006-implement-inbox-system.md](../issues/closed/ISSUE-006-implement-inbox-system.md) - Système d'inbox existant
- [DESIGN-SYSTEM.md](../design-system/DESIGN-SYSTEM.md) - Design system pour l'interface
