# TASK-006 : Implémenter le système d'inbox (messages système)

## Issue Associée

[ISSUE-006-implement-inbox-system.md](../issues/ISSUE-006-implement-inbox-system.md)

## Vue d'Ensemble

Implémenter un système d'inbox permettant aux joueurs de recevoir et consulter des messages système provenant de Stellar (la compagnie mystérieuse). Le système doit s'intégrer dans l'interface terminal et créer une expérience immersive. L'inbox génère automatiquement des messages lors d'événements importants (inscription, découvertes, etc.) via le système d'événements Laravel existant.

**MVP** : Messages système uniquement (pas de messagerie entre joueurs). Types de messages : bienvenue, découvertes, missions, alertes système.

## Recommandations Architecturales Intégrées

Ce plan intègre toutes les recommandations de la review architecturale ([ARCHITECT-REVIEW-006](../reviews/ARCHITECT-REVIEW-006-implement-inbox-system.md)). Les recommandations suivantes sont explicitement intégrées dans les tâches :

### 🔴 High Priority
- **Index explicites en base de données** (Tâche 1.1) : Index sur `recipient_id`, `is_read`, `type`, index composite `(recipient_id, is_read)`, et `created_at` pour optimiser les performances
- **Scope `forUser()` pour sécurité** (Tâche 1.2, 4.2) : Scope Eloquent pour garantir que les utilisateurs ne peuvent accéder qu'à leurs propres messages
- **Tests de sécurité** (Tâche 6.4) : Tests pour vérifier qu'un utilisateur ne peut pas accéder aux messages d'un autre utilisateur

### 🟡 Medium Priority
- **Gestion d'erreurs dans les listeners** (Tâches 3.1-3.4) : Try-catch pour gérer les erreurs de génération de messages sans bloquer les événements métier
- **Injection de dépendances dans Livewire** (Tâche 5.1) : Utiliser l'injection de dépendances dans les méthodes pour injecter `MessageService`
- **Attributs PHP 8 de Livewire 3.6** (Tâche 5.1) : Utiliser `#[Layout]` et `#[Computed]` pour une syntaxe moderne
- **Optimisation du compteur de messages non lus** (Tâche 5.4) : Utiliser `unreadMessagesCount()` et `#[Computed]` pour le cache automatique
- **Tests de pagination et filtres** (Tâche 6.4) : Tests complets pour la pagination et les filtres

### 🟢 Low Priority
- **Templates intégrés dans MessageService** (Tâche 2.2) : Intégrer les templates directement dans `MessageService` pour le MVP (simplicité)

## Suivi et Historique

### Statut

Approuvé - En review (PR créée)

### Historique

#### 2025-01-27 - Sam (Lead Dev) - Création de la Pull Request
**Statut** : En review
**Détails** : Pull Request #13 créée vers `develop`. Améliorations finales apportées : correction des types de messages (welcome/discovery), intégration des FormRequests, mise à jour des tests. Tous les tests passent (90 tests, 246 assertions). Code prêt pour review et merge.
**Pull Request** : [#13](https://github.com/PiLep/space-xplorer/pull/13)

#### 2025-01-27 - Sam (Lead Dev) - Review de code
**Statut** : Approuvé
**Détails** : Review de code complète effectuée. Code approuvé. L'implémentation est excellente et respecte parfaitement le plan ainsi que toutes les recommandations architecturales. Tous les tests passent avec succès (48+ tests au total : 14 unitaires Message, tests MessageService, 4 listeners, 16 API, 18 fonctionnels Livewire). Toutes les vérifications Medium Priority ont été complétées (tests fonctionnels vérifiés, lien navigation vérifié). Code prêt pour la production.
**Fichiers modifiés** : `docs/reviews/CODE-REVIEW-006-implement-inbox-system.md` (nouveau)
**Review** : [CODE-REVIEW-006-implement-inbox-system.md](../reviews/CODE-REVIEW-006-implement-inbox-system.md)

#### 2025-01-27 - Sam (Lead Dev) - Mise à jour du plan avec recommandations architecturales
**Statut** : À faire
**Détails** : Plan mis à jour pour intégrer explicitement toutes les recommandations de la review architecturale. Modifications apportées : index explicites en base de données (Tâche 1.1), scope `forUser()` pour sécurité (Tâche 1.2), gestion d'erreurs dans les listeners (Tâches 3.1-3.4), injection de dépendances et attributs PHP 8 dans Livewire (Tâche 5.1), optimisation du compteur de messages non lus (Tâche 5.4), tests de sécurité et pagination (Tâche 6.4), templates intégrés dans MessageService (Tâche 2.2). Le plan est maintenant prêt pour l'implémentation avec toutes les recommandations intégrées.
**Fichiers modifiés** : `docs/tasks/TASK-006-implement-inbox-system.md`
**Review** : [ARCHITECT-REVIEW-006-implement-inbox-system.md](../reviews/ARCHITECT-REVIEW-006-implement-inbox-system.md)

#### 2025-01-27 - Morgan (Architect) - Review architecturale
**Statut** : À faire
**Détails** : Review architecturale complète effectuée. Plan approuvé avec recommandations. Points principaux : ajout d'index explicites en base de données, scope Eloquent pour la sécurité, injection de dépendances dans Livewire, utilisation des attributs PHP 8 de Livewire 3.6, optimisation du compteur de messages non lus. Aucune modification majeure demandée, le plan peut être implémenté en tenant compte des recommandations.
**Fichiers modifiés** : `docs/reviews/ARCHITECT-REVIEW-006-implement-inbox-system.md` (nouveau)
**Review** : [ARCHITECT-REVIEW-006-implement-inbox-system.md](../reviews/ARCHITECT-REVIEW-006-implement-inbox-system.md)

#### 2025-01-27 - Sam (Lead Dev) - Création du plan de développement
**Statut** : À faire
**Détails** : Plan de développement créé pour implémenter le système d'inbox. Décomposition en phases : modèles et migrations, service de messages, intégration événementielle, API endpoints, composant Livewire, et tests.
**Fichiers modifiés** : `docs/tasks/TASK-006-implement-inbox-system.md`

## Objectifs Techniques

- Créer le modèle de données `Message` avec support pour messages système
- Développer le service `MessageService` pour créer et gérer les messages
- Intégrer la génération automatique de messages avec les événements Laravel existants
- Créer les endpoints API pour la gestion des messages
- Développer le composant Livewire `Inbox` avec interface terminal immersive
- Implémenter les listeners pour générer des messages lors d'événements importants

## Architecture & Design

- **Modèle** : `Message` dans `app/Models/Message.php` avec relations vers `User` (sender et recipient)
- **Migration** : Table `messages` avec champs ULID, relations, types, statuts, métadonnées JSON
- **Service** : `MessageService` dans `app/Services/MessageService.php` pour créer et gérer les messages
- **Listeners** : Créer des listeners pour générer automatiquement des messages lors d'événements
- **Composant Livewire** : `Inbox` dans `app/Livewire/Inbox.php` avec vue terminal immersive
- **Controller API** : `MessageController` dans `app/Http/Controllers/Api/MessageController.php`
- **Form Requests** : Validation pour les actions sur les messages

## Tâches de Développement

### Phase 1 : Modèle de Données et Migrations

#### Tâche 1.1 : Créer la migration pour la table messages
- **Description** : Créer la migration avec tous les champs nécessaires (id ULID, sender_id nullable, recipient_id, type, subject, content, is_read, read_at, is_important, metadata JSON, timestamps). Ajouter les foreign keys et **index explicites** pour optimiser les performances :
  - Index sur `recipient_id` (pour les requêtes par destinataire)
  - Index sur `is_read` (pour les filtres unread/read)
  - Index sur `type` (pour les filtres par type)
  - Index composite sur `(recipient_id, is_read)` (pour les requêtes combinées fréquentes)
  - Index sur `created_at` (pour le tri chronologique)
- **Fichiers concernés** : `database/migrations/YYYY_MM_DD_HHMMSS_create_messages_table.php`
- **Estimation** : 45 min
- **Dépendances** : Aucune
- **Tests** : Vérifier la structure de la table, les foreign keys, et les index
- **Note** : Recommandation architecturale (High Priority) - Index explicites pour performance

#### Tâche 1.2 : Créer le modèle Message
- **Description** : Créer le modèle Eloquent Message avec HasUlids, relations (sender, recipient), casts (metadata en array), scopes (unread, read, important, byType, **forUser**), et méthodes helper (markAsRead, markAsUnread).
  - **Scope `forUser(User $user)`** : Scope de sécurité pour filtrer les messages par destinataire. Utiliser ce scope dans le contrôleur API pour garantir la sécurité.
  ```php
  public function scopeForUser($query, User $user)
  {
      return $query->where('recipient_id', $user->id);
  }
  ```
- **Fichiers concernés** : `app/Models/Message.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 1.1
- **Tests** : Tests unitaires du modèle, relations, scopes (incluant forUser), et méthodes helper
- **Note** : Recommandation architecturale (High Priority) - Scope pour sécurité et maintenabilité

#### Tâche 1.3 : Ajouter les relations dans le modèle User
- **Description** : Ajouter les relations `sentMessages()` et `receivedMessages()` dans le modèle User. Ajouter une méthode helper `unreadMessagesCount()` pour obtenir le nombre de messages non lus.
- **Fichiers concernés** : `app/Models/User.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 1.2
- **Tests** : Tests des relations et de la méthode helper

### Phase 2 : Service de Messages

#### Tâche 2.1 : Créer MessageService
- **Description** : Service pour créer et gérer les messages système. Méthodes principales : `createSystemMessage()`, `createWelcomeMessage()`, `createDiscoveryMessage()`, `createMissionMessage()`, `createAlertMessage()`. Gestion des templates de messages selon le type.
- **Fichiers concernés** : `app/Services/MessageService.php`
- **Estimation** : 2h30
- **Dépendances** : Tâche 1.2
- **Tests** : Tests unitaires du service avec tous les types de messages

#### Tâche 2.2 : Créer les templates de messages
- **Description** : Intégrer les templates de messages directement dans `MessageService` pour le MVP (simplicité). Créer une méthode privée `getTemplate(string $type, array $variables): string` dans `MessageService` pour gérer les templates. Templates pour messages de bienvenue, découvertes, missions, alertes. Support pour variables dynamiques (nom du joueur, nom de planète, etc.). Possibilité d'extraire vers un service dédié plus tard si nécessaire.
- **Fichiers concernés** : `app/Services/MessageService.php` (méthode privée `getTemplate()`)
- **Estimation** : 1h30
- **Dépendances** : Tâche 2.1
- **Tests** : Tests des templates avec différentes variables
- **Note** : Recommandation architecturale (Low Priority) - Intégration dans MessageService pour MVP

### Phase 3 : Intégration Événementielle

#### Tâche 3.1 : Créer le listener SendWelcomeMessage
- **Description** : Listener pour l'événement `UserRegistered` qui génère un message de bienvenue de Stellar. Appelle `MessageService::createWelcomeMessage()`. **Gestion d'erreurs** : Utiliser un try-catch pour gérer les erreurs de génération de messages (logger l'erreur mais ne pas bloquer l'événement métier).
- **Fichiers concernés** : `app/Listeners/SendWelcomeMessage.php`
- **Estimation** : 45 min
- **Dépendances** : Tâche 2.1
- **Tests** : Tests du listener avec événement UserRegistered, tests de gestion d'erreurs
- **Note** : Recommandation architecturale (Medium Priority) - Gestion d'erreurs pour robustesse

#### Tâche 3.2 : Créer le listener SendPlanetDiscoveryMessage
- **Description** : Listener pour l'événement `PlanetExplored` qui génère un message de découverte de planète. Appelle `MessageService::createDiscoveryMessage()` avec les détails de la planète. **Gestion d'erreurs** : Utiliser un try-catch pour gérer les erreurs de génération de messages (logger l'erreur mais ne pas bloquer l'événement métier).
- **Fichiers concernés** : `app/Listeners/SendPlanetDiscoveryMessage.php`
- **Estimation** : 45 min
- **Dépendances** : Tâche 2.1
- **Tests** : Tests du listener avec événement PlanetExplored, tests de gestion d'erreurs
- **Note** : Recommandation architecturale (Medium Priority) - Gestion d'erreurs pour robustesse

#### Tâche 3.3 : Créer le listener SendSpecialDiscoveryMessage
- **Description** : Listener pour l'événement `DiscoveryMade` qui génère un message de découverte spéciale. Appelle `MessageService::createDiscoveryMessage()` avec les détails de la découverte. **Gestion d'erreurs** : Utiliser un try-catch pour gérer les erreurs de génération de messages (logger l'erreur mais ne pas bloquer l'événement métier).
- **Fichiers concernés** : `app/Listeners/SendSpecialDiscoveryMessage.php`
- **Estimation** : 45 min
- **Dépendances** : Tâche 2.1
- **Tests** : Tests du listener avec événement DiscoveryMade, tests de gestion d'erreurs
- **Note** : Recommandation architecturale (Medium Priority) - Gestion d'erreurs pour robustesse

#### Tâche 3.4 : Créer le listener SendHomePlanetMessage (optionnel)
- **Description** : Listener pour l'événement `PlanetCreated` qui génère un message de présentation de la planète d'origine si c'est une planète d'origine (vérifier via `home_planet_id`). Appelle `MessageService::createDiscoveryMessage()`. **Gestion d'erreurs** : Utiliser un try-catch pour gérer les erreurs de génération de messages (logger l'erreur mais ne pas bloquer l'événement métier).
- **Fichiers concernés** : `app/Listeners/SendHomePlanetMessage.php`
- **Estimation** : 45 min
- **Dépendances** : Tâche 2.1
- **Tests** : Tests du listener avec événement PlanetCreated (uniquement pour planètes d'origine), tests de gestion d'erreurs
- **Note** : Recommandation architecturale (Medium Priority) - Gestion d'erreurs pour robustesse

#### Tâche 3.5 : Enregistrer les listeners dans EventServiceProvider
- **Description** : Enregistrer tous les nouveaux listeners dans `app/Providers/EventServiceProvider.php` pour les événements correspondants.
- **Fichiers concernés** : `app/Providers/EventServiceProvider.php`
- **Estimation** : 20 min
- **Dépendances** : Tâches 3.1, 3.2, 3.3, 3.4
- **Tests** : Vérifier que les listeners sont bien enregistrés

### Phase 4 : API Endpoints

#### Tâche 4.1 : Créer les FormRequests pour les messages
- **Description** : Créer les FormRequests pour valider les actions sur les messages : `MarkMessageReadRequest` (validation optionnelle), `DeleteMessageRequest` (validation de l'ID).
- **Fichiers concernés** : `app/Http/Requests/MarkMessageReadRequest.php`, `app/Http/Requests/DeleteMessageRequest.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Tests de validation

#### Tâche 4.2 : Créer MessageController API
- **Description** : Créer le contrôleur API avec les méthodes : `index()` (liste paginée avec filtres), `show()` (détails d'un message, marque comme lu), `markAsRead()` (marquer comme lu), `markAsUnread()` (marquer comme non lu), `destroy()` (supprimer un message). **Sécurité** : Utiliser le scope `forUser()` du modèle Message pour garantir que l'utilisateur ne peut accéder qu'à ses propres messages :
  ```php
  $message = Message::forUser(auth()->user())->findOrFail($id);
  ```
- **Fichiers concernés** : `app/Http/Controllers/Api/MessageController.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 1.2, Tâche 4.1
- **Tests** : Tests d'intégration de tous les endpoints, tests de sécurité (vérifier qu'un utilisateur ne peut pas accéder aux messages d'un autre utilisateur)
- **Note** : Recommandation architecturale (High Priority) - Scope forUser() pour sécurité

#### Tâche 4.3 : Ajouter les routes API
- **Description** : Ajouter les routes API dans `routes/api.php` avec middleware `auth:sanctum` : `GET /api/messages`, `GET /api/messages/{id}`, `PATCH /api/messages/{id}/read`, `PATCH /api/messages/{id}/unread`, `DELETE /api/messages/{id}`.
- **Fichiers concernés** : `routes/api.php`
- **Estimation** : 15 min
- **Dépendances** : Tâche 4.2
- **Tests** : Vérifier les routes et le middleware

### Phase 5 : Composant Livewire

#### Tâche 5.1 : Créer le composant Livewire Inbox
- **Description** : Créer le composant Livewire avec gestion de l'état (messages, filtres, pagination, message sélectionné). Méthodes : `loadMessages()`, `filterMessages()`, `selectMessage()`, `markAsRead()`, `markAsUnread()`, `deleteMessage()`. 
  - **Injection de dépendances** : Utiliser l'injection de dépendances dans les méthodes (`mount()`, méthodes publiques) pour injecter `MessageService` :
    ```php
    public function mount(MessageService $messageService)
    {
        $this->messageService = $messageService;
        $this->loadMessages();
    }
    
    public function loadMessages(MessageService $messageService)
    {
        $this->messages = $messageService->getMessagesForUser(auth()->user(), $this->filter);
    }
    ```
  - **Attributs PHP 8** : Utiliser `#[Layout('layouts.app')]` et considérer `#[Computed]` pour les propriétés calculées (comme le compteur de messages non lus) :
    ```php
    use Livewire\Attributes\Layout;
    use Livewire\Attributes\Computed;
    
    #[Layout('layouts.app')]
    class Inbox extends Component
    {
        #[Computed]
        public function unreadCount(): int
        {
            return auth()->user()->unreadMessagesCount();
        }
    }
    ```
- **Fichiers concernés** : `app/Livewire/Inbox.php`
- **Estimation** : 2h30
- **Dépendances** : Tâche 1.2, Tâche 2.1
- **Tests** : Tests du composant avec différentes actions
- **Note** : Recommandations architecturales (Medium Priority) - Injection de dépendances et attributs PHP 8

#### Tâche 5.2 : Créer la vue Blade pour Inbox
- **Description** : Créer la vue avec style terminal immersif cohérent avec le reste du jeu. Liste des messages avec indicateurs visuels (non lus, importants), filtres (tous, non lus, lus), affichage du message sélectionné, actions (marquer comme lu/non lu, supprimer). Utiliser Tailwind CSS et le design system existant.
- **Fichiers concernés** : `resources/views/livewire/inbox.blade.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 5.1
- **Tests** : Vérification visuelle et fonctionnelle

#### Tâche 5.3 : Ajouter la route web pour l'inbox
- **Description** : Ajouter la route web `GET /inbox` dans `routes/web.php` avec middleware `auth` et layout `layouts.app`. Le composant Livewire doit utiliser `#[Layout('layouts.app')]`.
- **Fichiers concernés** : `routes/web.php`
- **Estimation** : 10 min
- **Dépendances** : Tâche 5.1
- **Tests** : Vérifier la route et l'authentification

#### Tâche 5.4 : Ajouter le lien vers l'inbox dans le dashboard/navigation
- **Description** : Ajouter un lien ou une icône vers l'inbox dans la navigation principale ou le dashboard. Afficher un badge avec le nombre de messages non lus. **Optimisation** : Utiliser la méthode helper `unreadMessagesCount()` du modèle User (déjà prévue dans la tâche 1.3) et considérer `#[Computed]` dans le composant Livewire pour le cache automatique si le compteur est utilisé dans le composant.
- **Fichiers concernés** : `resources/views/layouts/app.blade.php` ou `resources/views/livewire/dashboard.blade.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 5.3
- **Tests** : Vérification visuelle et fonctionnelle
- **Note** : Recommandation architecturale (Medium Priority) - Optimisation du compteur de messages non lus

### Phase 6 : Tests

#### Tâche 6.1 : Tests unitaires du modèle Message
- **Description** : Tests pour les relations, scopes, méthodes helper, casts, et validation des données.
- **Fichiers concernés** : `tests/Unit/MessageTest.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 1.2
- **Tests** : Exécuter les tests unitaires

#### Tâche 6.2 : Tests unitaires du MessageService
- **Description** : Tests pour toutes les méthodes du service avec différents types de messages et scénarios.
- **Fichiers concernés** : `tests/Unit/MessageServiceTest.php`
- **Estimation** : 1h30
- **Dépendances** : Tâche 2.1
- **Tests** : Exécuter les tests unitaires

#### Tâche 6.3 : Tests des listeners
- **Description** : Tests pour chaque listener avec leurs événements respectifs. Vérifier que les messages sont créés correctement.
- **Fichiers concernés** : `tests/Unit/SendWelcomeMessageTest.php`, `tests/Unit/SendPlanetDiscoveryMessageTest.php`, `tests/Unit/SendSpecialDiscoveryMessageTest.php`
- **Estimation** : 1h30
- **Dépendances** : Tâches 3.1, 3.2, 3.3
- **Tests** : Exécuter les tests unitaires

#### Tâche 6.4 : Tests d'intégration des endpoints API
- **Description** : Tests pour tous les endpoints API avec différents scénarios (authentification, autorisation, validation, erreurs). **Tests de sécurité** : Ajouter des tests pour vérifier qu'un utilisateur ne peut pas accéder aux messages d'un autre utilisateur (retourne 403 ou 404). **Tests de pagination et filtres** : Tester la pagination (20 par page) et les différents filtres (unread, read, type).
- **Fichiers concernés** : `tests/Feature/MessageApiTest.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 4.2
- **Tests** : Exécuter les tests d'intégration
- **Note** : Recommandations architecturales (High/Medium Priority) - Tests de sécurité et pagination

#### Tâche 6.5 : Tests fonctionnels du composant Livewire
- **Description** : Tests pour le composant Livewire avec différentes actions utilisateur (chargement, filtres, sélection, marquer comme lu, supprimer).
- **Fichiers concernés** : `tests/Feature/InboxTest.php`
- **Estimation** : 1h30
- **Dépendances** : Tâche 5.1
- **Tests** : Exécuter les tests fonctionnels

## Ordre d'Exécution

1. Phase 1 : Modèle de Données et Migrations (Tâches 1.1, 1.2, 1.3)
2. Phase 2 : Service de Messages (Tâches 2.1, 2.2)
3. Phase 3 : Intégration Événementielle (Tâches 3.1, 3.2, 3.3, 3.4, 3.5)
4. Phase 4 : API Endpoints (Tâches 4.1, 4.2, 4.3)
5. Phase 5 : Composant Livewire (Tâches 5.1, 5.2, 5.3, 5.4)
6. Phase 6 : Tests (Tâches 6.1, 6.2, 6.3, 6.4, 6.5)

## Migrations de Base de Données

- [ ] Migration : Créer la table `messages` avec tous les champs nécessaires

## Endpoints API

### Nouveaux Endpoints

- `GET /api/messages` - Liste des messages de l'utilisateur connecté
  - Query parameters : `filter` (all|unread|read), `type` (system|discovery|mission|alert|welcome), `page` (pagination)
  - Response : 
    ```json
    {
      "data": {
        "messages": [...],
        "pagination": {...}
      },
      "status": "success"
    }
    ```
  - Validation : Aucune (authentification requise via middleware)

- `GET /api/messages/{id}` - Détails d'un message spécifique
  - Response : 
    ```json
    {
      "data": {
        "message": {
          "id": "string",
          "sender_id": "string|null",
          "recipient_id": "string",
          "type": "string",
          "subject": "string",
          "content": "string",
          "is_read": "boolean",
          "read_at": "timestamp|null",
          "is_important": "boolean",
          "metadata": "object|null",
          "created_at": "timestamp"
        }
      },
      "status": "success"
    }
    ```
  - Validation : Vérifier que l'utilisateur est le destinataire
  - Action : Marque automatiquement le message comme lu lors de l'ouverture

- `PATCH /api/messages/{id}/read` - Marquer un message comme lu
  - Response : 
    ```json
    {
      "data": {
        "message": {...}
      },
      "message": "Message marked as read",
      "status": "success"
    }
    ```
  - Validation : Vérifier que l'utilisateur est le destinataire

- `PATCH /api/messages/{id}/unread` - Marquer un message comme non lu
  - Response : 
    ```json
    {
      "data": {
        "message": {...}
      },
      "message": "Message marked as unread",
      "status": "success"
    }
    ```
  - Validation : Vérifier que l'utilisateur est le destinataire

- `DELETE /api/messages/{id}` - Supprimer un message
  - Response : 
    ```json
    {
      "message": "Message deleted successfully",
      "status": "success"
    }
    ```
  - Validation : Vérifier que l'utilisateur est le destinataire

## Événements & Listeners

### Nouveaux Listeners

- `SendWelcomeMessage` : Génère un message de bienvenue lors de l'inscription
  - Écoute : `UserRegistered`
  - Action : Appelle `MessageService::createWelcomeMessage()` avec l'utilisateur

- `SendPlanetDiscoveryMessage` : Génère un message de découverte de planète
  - Écoute : `PlanetExplored`
  - Action : Appelle `MessageService::createDiscoveryMessage()` avec l'utilisateur et la planète

- `SendSpecialDiscoveryMessage` : Génère un message de découverte spéciale
  - Écoute : `DiscoveryMade`
  - Action : Appelle `MessageService::createDiscoveryMessage()` avec l'utilisateur et les détails de la découverte

- `SendHomePlanetMessage` : Génère un message de présentation de la planète d'origine (optionnel)
  - Écoute : `PlanetCreated`
  - Action : Vérifie si c'est une planète d'origine et appelle `MessageService::createDiscoveryMessage()`

## Services & Classes

### Nouveaux Services

- `MessageService` : Service de gestion des messages système
  - Méthodes : 
    - `createSystemMessage(User $recipient, string $subject, string $content, array $metadata = [])` : Crée un message système générique
    - `createWelcomeMessage(User $recipient)` : Crée un message de bienvenue de Stellar
    - `createDiscoveryMessage(User $recipient, Planet|array $discovery, string $subject = null, string $content = null)` : Crée un message de découverte
    - `createMissionMessage(User $recipient, string $subject, string $content, array $metadata = [])` : Crée un message de mission
    - `createAlertMessage(User $recipient, string $subject, string $content, array $metadata = [])` : Crée un message d'alerte système

### Classes Modifiées

- `User` : Ajout des relations `sentMessages()` et `receivedMessages()`, méthode helper `unreadMessagesCount()`
- `EventServiceProvider` : Enregistrement des nouveaux listeners

## Tests

### Tests Unitaires

- [ ] Test : Modèle Message - Relations sender et recipient
- [ ] Test : Modèle Message - Scopes (unread, read, important, byType)
- [ ] Test : Modèle Message - Méthodes helper (markAsRead, markAsUnread)
- [ ] Test : Modèle Message - Cast metadata en array
- [ ] Test : MessageService - Création de messages système
- [ ] Test : MessageService - Création de messages de bienvenue
- [ ] Test : MessageService - Création de messages de découverte
- [ ] Test : MessageService - Création de messages de mission
- [ ] Test : MessageService - Création de messages d'alerte
- [ ] Test : SendWelcomeMessage - Génération de message lors de UserRegistered
- [ ] Test : SendPlanetDiscoveryMessage - Génération de message lors de PlanetExplored
- [ ] Test : SendSpecialDiscoveryMessage - Génération de message lors de DiscoveryMade

### Tests d'Intégration

- [ ] Test : GET /api/messages retourne la liste des messages de l'utilisateur
- [ ] Test : GET /api/messages avec filtres (unread, read, type)
- [ ] Test : GET /api/messages/{id} retourne les détails et marque comme lu
- [ ] Test : GET /api/messages/{id} vérifie l'autorisation (destinataire uniquement)
- [ ] Test : PATCH /api/messages/{id}/read marque le message comme lu
- [ ] Test : PATCH /api/messages/{id}/unread marque le message comme non lu
- [ ] Test : DELETE /api/messages/{id} supprime le message
- [ ] Test : DELETE /api/messages/{id} vérifie l'autorisation (destinataire uniquement)
- [ ] Test : Pagination des messages (20 par page)

### Tests Fonctionnels

- [ ] Test : Composant Livewire Inbox charge les messages
- [ ] Test : Composant Livewire Inbox filtre les messages (tous, non lus, lus)
- [ ] Test : Composant Livewire Inbox sélectionne un message
- [ ] Test : Composant Livewire Inbox marque un message comme lu
- [ ] Test : Composant Livewire Inbox marque un message comme non lu
- [ ] Test : Composant Livewire Inbox supprime un message
- [ ] Test : Message de bienvenue généré lors de l'inscription
- [ ] Test : Message de découverte généré lors de l'exploration d'une planète

## Documentation

- [ ] Mettre à jour ARCHITECTURE.md avec le modèle Message et les endpoints API
- [ ] Documenter MessageService dans le code
- [ ] Ajouter des commentaires dans le code pour les listeners
- [ ] Documenter les types de messages dans le code

## Notes Techniques

- **ULIDs** : Utiliser `HasUlids` trait pour le modèle Message (cohérent avec le reste du projet)
- **Messages système** : `sender_id` est `null` pour les messages système (expéditeur = "Stellar")
- **Métadonnées** : Le champ `metadata` (JSON) permet de stocker des liens vers planètes, systèmes, etc. pour enrichir les messages
- **Performance** : Utiliser la pagination Laravel (20 messages par page) pour éviter les problèmes de performance
- **Autorisation** : Toujours vérifier que l'utilisateur est le destinataire du message avant toute action
- **Templates** : Système simple de templates pour le MVP, peut être enrichi plus tard avec un système de templates plus avancé
- **Livewire** : Le composant Livewire utilise directement les services (pas d'appels API) pour une meilleure performance
- **Style terminal** : L'interface doit être cohérente avec le reste du jeu (style terminal immersif)
- **Badge non lus** : Afficher un badge avec le nombre de messages non lus dans la navigation pour encourager l'engagement

## Références

- [ISSUE-006-implement-inbox-system.md](../issues/ISSUE-006-implement-inbox-system.md)
- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Architecture technique, système d'événements, modèle de données
- [STACK.md](../memory_bank/STACK.md) - Stack technique (Laravel, Livewire)
- [PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md) - Contexte métier et personas

