# ISSUE-006 : Implémenter le système d'inbox (messages système)

## Type
Feature

## Priorité
Medium

## Description

Implémenter un système d'inbox permettant aux joueurs de recevoir et consulter des messages système provenant de Stellar (la compagnie mystérieuse). L'inbox doit s'intégrer dans l'interface terminal et créer une expérience immersive dans l'univers du jeu.

**MVP** : Système de messages système uniquement (pas de messagerie entre joueurs pour le MVP). Les messages peuvent inclure des notifications de découvertes, des missions, des alertes système, et des communications de Stellar.

## Contexte Métier

L'inbox est un **point central de communication** entre Stellar et le joueur. C'est un élément essentiel pour :
- **Immersion** : Créer un sentiment de connexion avec l'univers Stellar (compagnie mystérieuse)
- **Guidage** : Informer le joueur des événements importants (découvertes, missions, alertes)
- **Engagement** : Encourager le joueur à revenir pour consulter ses messages
- **Narration** : Permettre à Stellar de communiquer avec le joueur de manière narrative

**Ambiance** : Interface terminal cohérente avec le reste du jeu, messages présentés comme des communications officielles de Stellar.

**Moment d'affichage** : Accessible depuis le dashboard/terminal principal via une icône ou un menu.

## Critères d'Acceptation

### Fonctionnalités principales

- [ ] **Affichage de la liste des messages**
  - Liste des messages reçus par le joueur (plus récents en premier)
  - Indicateur visuel pour les messages non lus (badge avec nombre)
  - Filtrage par statut (tous, non lus, lus)
  - Pagination si nécessaire (limite de 20 messages par page)

- [ ] **Consultation d'un message**
  - Affichage du contenu complet du message
  - Marquer automatiquement comme lu lors de l'ouverture
  - Informations contextuelles (expéditeur, date, type de message)
  - Actions possibles (marquer comme lu/non lu, supprimer)

- [ ] **Types de messages système**
  - Messages de bienvenue de Stellar
  - Notifications de découvertes (planètes explorées, systèmes découverts)
  - Missions/objectifs assignés par Stellar
  - Alertes système (maintenance, événements spéciaux)
  - Messages automatiques (générés par les événements du jeu)

- [ ] **Interface utilisateur**
  - Interface terminal cohérente avec le reste du jeu
  - Design immersif dans l'univers Stellar
  - Responsive et accessible
  - Indicateurs visuels clairs (non lus, importants, etc.)

### Fonctionnalités techniques

- [ ] **Modèle de données**
  - Table `messages` avec les champs nécessaires (expéditeur, destinataire, sujet, contenu, type, statut lu/non lu, dates)
  - Relations avec les utilisateurs
  - Support pour les messages système (expéditeur = "Stellar" ou système)

- [ ] **Génération automatique de messages**
  - Messages générés automatiquement lors d'événements importants (découvertes, missions complétées, etc.)
  - Intégration avec le système d'événements Laravel existant
  - Messages de bienvenue générés lors de l'inscription ou de la complétion de l'onboarding

- [ ] **API et routes**
  - Endpoints API pour récupérer la liste des messages
  - Endpoint pour récupérer un message spécifique
  - Endpoint pour marquer un message comme lu/non lu
  - Endpoint pour supprimer un message
  - Routes web pour l'interface Livewire

- [ ] **Composant Livewire**
  - Composant `Inbox` pour gérer l'affichage et les interactions
  - Gestion de l'état (messages, filtres, pagination)
  - Actions utilisateur (marquer comme lu, supprimer, filtrer)

### Intégration avec l'existant

- [ ] **Événements Laravel**
  - Créer des messages automatiquement lors d'événements importants
  - Exemples : `PlanetExplored` → Message de découverte, `DiscoveryMade` → Message de découverte spéciale
  - Intégration avec les événements existants (`UserRegistered`, `PlanetCreated`, etc.)

- [ ] **Authentification et autorisation**
  - Seuls les utilisateurs authentifiés peuvent accéder à leur inbox
  - Un utilisateur ne peut voir que ses propres messages
  - Messages système visibles par tous les utilisateurs concernés

## Détails Techniques

### Modèle de données

**Table `messages`** :
- `id` (ULID) - Identifiant unique
- `sender_id` (ULID, nullable) - ID de l'expéditeur (null pour messages système)
- `recipient_id` (ULID) - ID du destinataire (FK → users.id)
- `type` (string) - Type de message (system, discovery, mission, alert, welcome)
- `subject` (string) - Sujet du message
- `content` (text) - Contenu du message (peut être du HTML ou Markdown)
- `is_read` (boolean, default: false) - Statut de lecture
- `read_at` (timestamp, nullable) - Date de lecture
- `is_important` (boolean, default: false) - Message important (épinglé)
- `metadata` (json, nullable) - Métadonnées additionnelles (liens vers planètes, systèmes, etc.)
- `created_at` / `updated_at` - Timestamps

**Relations** :
- `recipient()` : BelongsTo → User (destinataire)
- `sender()` : BelongsTo → User (expéditeur, nullable pour messages système)

### Types de messages

**Types définis** :
- `system` : Messages système génériques
- `discovery` : Notifications de découvertes (planètes, systèmes)
- `mission` : Missions/objectifs assignés par Stellar
- `alert` : Alertes système (maintenance, événements)
- `welcome` : Messages de bienvenue

### Génération automatique de messages

**Événements déclencheurs** :
- `UserRegistered` → Message de bienvenue de Stellar
- `PlanetExplored` → Message de découverte de planète
- `DiscoveryMade` → Message de découverte spéciale
- `PlanetCreated` → Message de présentation de la planète d'origine (si dans l'onboarding)

**Service suggéré** : `MessageService` pour :
- Créer des messages système
- Générer le contenu des messages selon le type
- Gérer les templates de messages

### Architecture suggérée

- **Modèle** : `Message` dans `app/Models/Message.php`
- **Migration** : Créer la table `messages`
- **Service** : `MessageService` dans `app/Services/MessageService.php`
- **Listeners** : Créer des listeners pour générer automatiquement des messages lors d'événements
- **Composant Livewire** : `Inbox` dans `app/Livewire/Inbox.php`
- **Vue** : `resources/views/livewire/inbox.blade.php`
- **Controller API** (optionnel) : `MessageController` dans `app/Http/Controllers/Api/MessageController.php`

### Routes

**Routes Web** :
- `GET /inbox` - Page principale de l'inbox (composant Livewire)

**Routes API** :
- `GET /api/messages` - Liste des messages de l'utilisateur connecté
- `GET /api/messages/{id}` - Détails d'un message spécifique
- `PATCH /api/messages/{id}/read` - Marquer un message comme lu
- `DELETE /api/messages/{id}` - Supprimer un message

### Intégration avec les événements

**Exemple d'intégration** :
```php
// Dans un listener pour PlanetExplored
public function handle(PlanetExplored $event)
{
    MessageService::createDiscoveryMessage(
        $event->user,
        $event->planet,
        'Planète découverte',
        'Vous avez découvert une nouvelle planète : ' . $event->planet->name
    );
}
```

## Notes

### Scope MVP

- **Messagerie entre joueurs** : Non inclus dans le MVP (peut être ajouté dans une version future)
- **Réponses aux messages** : Non prévu pour le MVP (messages système en lecture seule)
- **Notifications push** : Non prévu pour le MVP (peut être ajouté dans une version future)
- **Templates de messages** : Système simple pour le MVP, peut être enrichi plus tard
- **Recherche dans les messages** : Non prévu pour le MVP (peut être ajouté si nécessaire)

### Extensibilité

L'architecture doit permettre d'ajouter facilement :
- De nouveaux types de messages
- Des templates de messages personnalisés
- Des actions sur les messages (répondre, transférer, etc.)
- Des notifications push
- Une messagerie entre joueurs

### Expérience utilisateur

- **Accessibilité** : L'inbox doit être facilement accessible depuis le dashboard
- **Visibilité** : Indicateur visuel clair pour les messages non lus
- **Performance** : Chargement rapide de la liste des messages (pagination, lazy loading)
- **Immersion** : Interface terminal cohérente avec le reste du jeu, messages présentés comme des communications officielles de Stellar

### Messages de bienvenue

Lors de l'inscription ou de la complétion de l'onboarding, générer automatiquement :
- Un message de bienvenue de Stellar présentant la compagnie
- Un message présentant la planète d'origine du joueur
- Un message expliquant les prochaines étapes

## Références

- [PROJECT_BRIEF.md](../../memory_bank/PROJECT_BRIEF.md) - Vision métier et personas
- [ARCHITECTURE.md](../../memory_bank/ARCHITECTURE.md) - Architecture technique, modèle de données, système d'événements
- [STACK.md](../../memory_bank/STACK.md) - Stack technique (Laravel, Livewire)
- [ISSUE-005-implement-onboarding-mvp.md](../ISSUE-005-implement-onboarding-mvp.md) - Issue liée mentionnant l'inbox

## Suivi et Historique

### Statut

Terminé

### GitHub

- **Issue GitHub** : [#12](https://github.com/PiLep/space-xplorer/issues/12)
- **Branche** : `feature/ISSUE-006-implement-inbox-system`
- **Pull Request** : [#13](https://github.com/PiLep/space-xplorer/pull/13)

### Historique

#### 2025-11-13 - Alex (Product) - Merge de la Pull Request
**Statut** : Terminé
**Détails** : Pull Request #13 mergée dans `develop` le 2025-11-13. L'implémentation complète du système d'inbox est maintenant en production. Tous les tests passent (90 tests, 246 assertions). Système de messages système fonctionnel avec génération automatique via événements Laravel, interface Livewire immersive, et API complète.
**Pull Request** : [#13](https://github.com/PiLep/space-xplorer/pull/13)
**Merge commit** : `58ef1486eff68b73e5b89f3a6636574177ad84ca`

#### 2025-01-27 - Sam (Lead Dev) - Création de la Pull Request
**Statut** : En review
**Détails** : Pull Request créée vers `develop`. PR #13 créée avec toutes les améliorations (correction des types de messages, intégration des FormRequests). Tous les tests passent (90 tests, 246 assertions). Code prêt pour review et merge.
**Pull Request** : [#13](https://github.com/PiLep/space-xplorer/pull/13)

#### 2025-01-27 - Sam (Lead Dev) - Review de code
**Statut** : Approuvé
**Détails** : Review de code complète effectuée sur l'implémentation TASK-006. Code approuvé. L'implémentation est excellente et respecte parfaitement le plan ainsi que toutes les recommandations architecturales. Tous les tests passent avec succès (48+ tests au total). Toutes les vérifications Medium Priority ont été complétées (tests fonctionnels vérifiés, lien navigation vérifié). Code prêt pour la production.
**Fichiers modifiés** : `docs/reviews/CODE-REVIEW-006-implement-inbox-system.md` (nouveau), `docs/tasks/TASK-006-implement-inbox-system.md` (mis à jour)
**Review** : [CODE-REVIEW-006-implement-inbox-system.md](../reviews/CODE-REVIEW-006-implement-inbox-system.md)

#### 2025-01-27 - Sam (Lead Dev) - Mise à jour du plan avec recommandations architecturales
**Statut** : En cours
**Détails** : Plan de développement TASK-006 mis à jour pour intégrer explicitement toutes les recommandations de la review architecturale. Modifications apportées : index explicites en base de données (Tâche 1.1), scope `forUser()` pour sécurité (Tâche 1.2), gestion d'erreurs dans les listeners (Tâches 3.1-3.4), injection de dépendances et attributs PHP 8 dans Livewire (Tâche 5.1), optimisation du compteur de messages non lus (Tâche 5.4), tests de sécurité et pagination (Tâche 6.4), templates intégrés dans MessageService (Tâche 2.2). Le plan est maintenant prêt pour l'implémentation avec toutes les recommandations intégrées.
**Fichiers modifiés** : `docs/tasks/TASK-006-implement-inbox-system.md`
**Plan** : [TASK-006-implement-inbox-system.md](../tasks/TASK-006-implement-inbox-system.md)

#### 2025-01-27 - Morgan (Architect) - Review architecturale
**Statut** : En cours
**Détails** : Review architecturale complète effectuée sur le plan de développement TASK-006. Plan approuvé avec recommandations. Points principaux : ajout d'index explicites en base de données, scope Eloquent pour la sécurité, injection de dépendances dans Livewire, utilisation des attributs PHP 8 de Livewire 3.6, optimisation du compteur de messages non lus. Aucune modification majeure demandée, le plan peut être implémenté en tenant compte des recommandations.
**Fichiers modifiés** : `docs/reviews/ARCHITECT-REVIEW-006-implement-inbox-system.md` (nouveau), `docs/tasks/TASK-006-implement-inbox-system.md` (mis à jour)
**Review** : [ARCHITECT-REVIEW-006-implement-inbox-system.md](../reviews/ARCHITECT-REVIEW-006-implement-inbox-system.md)

#### 2025-01-27 - Sam (Lead Dev) - Création du plan de développement
**Statut** : En cours
**Détails** : Plan de développement créé (TASK-006). Décomposition en 6 phases : modèles et migrations, service de messages, intégration événementielle, API endpoints, composant Livewire, et tests. Branche Git créée : `feature/ISSUE-006-implement-inbox-system`.
**Fichiers modifiés** : `docs/tasks/TASK-006-implement-inbox-system.md`
**Plan** : [TASK-006-implement-inbox-system.md](../tasks/TASK-006-implement-inbox-system.md)

#### 2025-01-27 - Alex (Product) - Création de l'issue GitHub et branche dédiée
**Statut** : À faire
**Détails** : Issue GitHub créée (#12) et branche dédiée `issue/006-implement-inbox-system` créée. Issue synchronisée avec GitHub.
**GitHub** : [#12](https://github.com/PiLep/space-xplorer/issues/12)
**Branche** : `issue/006-implement-inbox-system`

#### 2025-01-27 - Alex (Product) - Création de l'issue
**Statut** : À faire
**Détails** : Issue créée pour implémenter le système d'inbox (messages système). MVP défini avec focus sur les messages système uniquement (pas de messagerie entre joueurs). Intégration avec le système d'événements Laravel pour génération automatique de messages. Architecture extensible prévue pour les évolutions futures (messagerie entre joueurs, notifications push, etc.).
**Notes** : Issue mentionnée dans ISSUE-005 comme feature future. Priorité Medium car non bloquante pour le MVP mais importante pour l'engagement et l'immersion.

