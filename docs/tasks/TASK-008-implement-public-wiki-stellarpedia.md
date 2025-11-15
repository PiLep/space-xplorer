# TASK-008 : Implémenter le Wiki Public Stellarpedia (MVP)

## Issue Associée

[ISSUE-008-implement-public-wiki-stellarpedia.md](../issues/ISSUE-008-implement-public-wiki-stellarpedia.md)

## Vue d'Ensemble

Implémenter un wiki public basique accessible à tous (joueurs et non-joueurs) pour afficher toutes les planètes découvertes dans l'univers de Stellar. Le wiki doit avoir un design superbe, style encyclopédie spatiale, et être alimenté automatiquement par les actions de jeu.

**Objectifs MVP** :
- Créer un référentiel public consultable par tous
- Afficher toutes les planètes découvertes (y compris planètes d'origine)
- Permettre aux joueurs de nommer leurs découvertes
- Générer automatiquement les descriptions via IA
- Maintenir la qualité du contenu avec validation automatique

## Suivi et Historique

### Statut

✅ Terminé - Toutes les phases implémentées

### Historique

#### 2025-01-XX - Sam (Lead Developer) - Création du plan
**Statut** : À faire
**Détails** : Plan de développement créé. Le plan décompose l'issue en 7 phases avec 25 tâches au total.
**Fichiers modifiés** : docs/tasks/TASK-008-implement-public-wiki-stellarpedia.md
**Notes** : Estimation totale : ~20h de développement. Focus sur MVP simplifié avec création automatique d'articles, nommage des planètes, et génération IA des descriptions.

#### 2025-01-XX - Morgan (Architect) - Review architecturale
**Statut** : À faire
**Détails** : Review architecturale complète effectuée. Le plan est approuvé avec recommandations.
**Fichiers modifiés** : docs/reviews/ARCHITECT-REVIEW-008-implement-public-wiki-stellarpedia.md
**Notes** : ⚠️ Approuvé avec recommandations. Principales recommandations : clarification de l'utilisation des services par Livewire (High), utilisation explicite des ULIDs dans les migrations (High), génération IA asynchrone (High pour évolution future), rate limiting (Medium), index de performance (Medium). Le plan peut être implémenté en tenant compte des recommandations.

#### 2025-01-20 - Jordan (Fullstack Dev) - Implémentation complète
**Statut** : ✅ Terminé
**Détails** : Toutes les phases terminées. Toutes les recommandations de l'architecte ont été prises en compte :
- ✅ Migrations créées avec ULIDs explicites (`$table->ulid('id')->primary()`)
- ✅ Foreign keys utilisent des ULIDs
- ✅ Index de performance ajoutés (discovered_by_user_id, name, fallback_name, created_at)
- ✅ Configuration wiki.php créée avec validation des mots interdits
- ✅ WikiService créé avec validation complète des noms
- ✅ AIDescriptionService créé avec cache et retry logic
- ✅ Listeners créés pour PlanetCreated et PlanetExplored
- ✅ API endpoints créés avec rate limiting (60 req/min pour publics, 5 req/min pour nommage)
- ✅ FormRequests créés pour validation
- ✅ Composants Livewire créés utilisant directement les services (pas l'API)
- ✅ Routes web créées avec préfixe `/codex` (au lieu de `/wiki`)
- ✅ Tests unitaires et d'intégration créés
- ✅ ARCHITECTURE.md mis à jour avec documentation complète
**Fichiers créés** :
- database/migrations/2025_01_20_100000_create_wiki_entries_table.php
- database/migrations/2025_01_20_100001_create_wiki_contributions_table.php
- app/Models/WikiEntry.php
- app/Models/WikiContribution.php
- config/text-generation.php
- config/wiki.php
- app/Services/AIDescriptionService.php
- app/Services/WikiService.php
- app/Listeners/CreateWikiEntryOnPlanetCreated.php
- app/Listeners/CreateWikiEntryOnPlanetExplored.php
- app/Http/Requests/NamePlanetRequest.php
- app/Http/Requests/ContributeToWikiRequest.php
- app/Http/Controllers/Api/WikiController.php
- app/Livewire/WikiIndex.php
- app/Livewire/WikiPlanet.php
- app/Livewire/NamePlanet.php
- app/Livewire/ContributeToWiki.php
- resources/views/livewire/wiki-index.blade.php
- resources/views/livewire/wiki-planet.blade.php
- resources/views/livewire/name-planet.blade.php
- resources/views/livewire/contribute-to-wiki.blade.php
- database/factories/WikiEntryFactory.php
- database/factories/WikiContributionFactory.php
- tests/Unit/Services/WikiServiceTest.php
- tests/Unit/Services/AIDescriptionServiceTest.php
- tests/Feature/Listeners/CreateWikiEntryOnPlanetCreatedTest.php
- tests/Feature/Listeners/CreateWikiEntryOnPlanetExploredTest.php
- tests/Feature/Api/WikiControllerTest.php
**Fichiers modifiés** :
- app/Providers/EventServiceProvider.php (ajout des listeners)
- routes/api.php (ajout des routes codex avec rate limiting)
- routes/web.php (ajout des routes codex publiques)
- docs/memory_bank/ARCHITECTURE.md (documentation complète du système Codex)
**Notes** : Toutes les recommandations High Priority de l'architecte ont été implémentées. Le système utilise le préfixe `/codex` pour les routes publiques comme demandé. Les composants Livewire utilisent directement les services Laravel plutôt que l'API, conformément à l'architecture hybride.

## Objectifs Techniques

- Créer le modèle de données pour les articles wiki (`wiki_entries`)
- Implémenter la création automatique d'articles lors de `PlanetCreated` et `PlanetExplored`
- Développer le service de génération de descriptions IA (`AIDescriptionService`)
- Créer le service de gestion du wiki (`WikiService`)
- Implémenter la validation automatique des noms de planètes
- Créer les endpoints API publics et authentifiés
- Développer les composants Livewire pour l'interface publique
- Implémenter la recherche et les filtres

## Architecture & Design

**Modèle de données** :
- Table `wiki_entries` : Articles wiki (une entrée par planète)
- Table `wiki_contributions` : Contributions des joueurs (optionnel pour MVP simplifié)

**Services** :
- `WikiService` : Gestion des articles wiki (création, validation, nommage)
- `AIDescriptionService` : Génération de descriptions via IA (similaire à `ImageGenerationService`)

**Événements & Listeners** :
- Listener sur `PlanetCreated` : Créer un article wiki pour les planètes d'origine
- Listener sur `PlanetExplored` : Créer un article si inexistant

**Routes** :
- Routes publiques : `/wiki`, `/wiki/planets/{id}`, `/api/wiki/planets`, `/api/wiki/search`
- Routes authentifiées : `POST /api/wiki/planets/{id}/name`, `POST /api/wiki/planets/{id}/contribute`

**Composants Livewire** :
- `WikiIndex` : Page d'accueil du wiki (liste des planètes)
- `WikiPlanet` : Page détail d'une planète

## Tâches de Développement

### Phase 1 : Modèle de Données et Migrations

#### Tâche 1.1 : Créer la migration pour la table wiki_entries
- **Description** : Créer la migration avec tous les champs nécessaires (id ULID, planet_id unique, name nullable, fallback_name, description text, discovered_by_user_id nullable, is_named boolean, is_public boolean, timestamps)
- **Fichiers concernés** : `database/migrations/YYYY_MM_DD_HHMMSS_create_wiki_entries_table.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Vérifier la structure de la table et les contraintes

#### Tâche 1.2 : Créer la migration pour la table wiki_contributions (optionnel MVP)
- **Description** : Créer la migration pour les contributions (id ULID, wiki_entry_id FK, contributor_user_id FK, content_type string, content text, status enum, timestamps). Pour le MVP, cette table peut être simplifiée ou omise si les modifications sont directes sur l'article.
- **Fichiers concernés** : `database/migrations/YYYY_MM_DD_HHMMSS_create_wiki_contributions_table.php`
- **Estimation** : 20 min
- **Dépendances** : Tâche 1.1
- **Tests** : Vérifier la structure et les relations

#### Tâche 1.3 : Créer le modèle WikiEntry
- **Description** : Créer le modèle Eloquent avec relations (belongsTo Planet, belongsTo User pour discovered_by, hasMany WikiContribution), attributs castés, méthodes helper
- **Fichiers concernés** : `app/Models/WikiEntry.php`
- **Estimation** : 45 min
- **Dépendances** : Tâche 1.1
- **Tests** : Tests unitaires du modèle et des relations

#### Tâche 1.4 : Créer le modèle WikiContribution (si table créée)
- **Description** : Créer le modèle Eloquent avec relations (belongsTo WikiEntry, belongsTo User), attributs castés
- **Fichiers concernés** : `app/Models/WikiContribution.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 1.2
- **Tests** : Tests unitaires du modèle

### Phase 2 : Service de Génération IA

#### Tâche 2.1 : Créer la configuration pour la génération de texte IA
- **Description** : Créer le fichier de configuration `config/text-generation.php` avec les providers (OpenAI GPT), endpoints, modèles, paramètres (similaire à `config/image-generation.php`)
- **Fichiers concernés** : `config/text-generation.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Tests de la configuration

#### Tâche 2.2 : Créer le service AIDescriptionService
- **Description** : Service pour générer des descriptions de planètes via IA. Méthodes principales : `generatePlanetDescription(Planet $planet)` qui construit un prompt basé sur les caractéristiques et appelle l'API IA. Gestion des erreurs, retry, cache. Pattern similaire à `ImageGenerationService`.
- **Fichiers concernés** : `app/Services/AIDescriptionService.php`
- **Estimation** : 3h
- **Dépendances** : Tâche 2.1
- **Tests** : Tests unitaires du service avec mocks de l'API

#### Tâche 2.3 : Créer les exceptions pour la génération de texte
- **Description** : Créer les exceptions personnalisées (TextGenerationException, ProviderConfigurationException pour texte, etc.) si nécessaire, ou réutiliser les exceptions existantes
- **Fichiers concernés** : `app/Exceptions/TextGenerationException.php` (si nécessaire)
- **Estimation** : 20 min
- **Dépendances** : Tâche 2.2
- **Tests** : Tests des exceptions

### Phase 3 : Service Wiki

#### Tâche 3.1 : Créer le service WikiService
- **Description** : Service principal pour gérer les articles wiki. Méthodes : `createEntryForPlanet(Planet $planet, ?User $discoverer = null)` (créer un article avec génération de description IA), `generateFallbackName(Planet $planet)` (générer le nom de fallback technique), `validateName(string $name)` (valider un nom selon les règles), `namePlanet(WikiEntry $entry, User $user, string $name)` (nommer une planète avec validation), `canUserNamePlanet(WikiEntry $entry, User $user)` (vérifier si l'utilisateur peut nommer)
- **Fichiers concernés** : `app/Services/WikiService.php`
- **Estimation** : 3h
- **Dépendances** : Tâche 1.3, Tâche 2.2
- **Tests** : Tests unitaires du service

#### Tâche 3.2 : Créer la configuration pour la validation des noms
- **Description** : Créer `config/wiki.php` avec les règles de validation (longueur min/max, caractères autorisés, mots interdits, etc.)
- **Fichiers concernés** : `config/wiki.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Tests de la configuration

### Phase 4 : Événements & Listeners

#### Tâche 4.1 : Créer le listener CreateWikiEntryOnPlanetCreated
- **Description** : Listener sur `PlanetCreated` qui crée automatiquement un article wiki pour les planètes d'origine. Appelle `WikiService::createEntryForPlanet()` avec le découvreur si disponible (via `home_planet_id` des users).
- **Fichiers concernés** : `app/Listeners/CreateWikiEntryOnPlanetCreated.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 3.1
- **Tests** : Tests du listener (vérifier création d'article, génération description)

#### Tâche 4.2 : Créer le listener CreateWikiEntryOnPlanetExplored
- **Description** : Listener sur `PlanetExplored` qui vérifie l'existence d'un article et en crée un si nécessaire. Appelle `WikiService::createEntryForPlanet()` avec le découvreur.
- **Fichiers concernés** : `app/Listeners/CreateWikiEntryOnPlanetExplored.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 3.1
- **Tests** : Tests du listener (vérifier création uniquement si inexistant)

#### Tâche 4.3 : Enregistrer les listeners dans EventServiceProvider
- **Description** : Enregistrer les listeners dans `app/Providers/EventServiceProvider.php` pour écouter `PlanetCreated` et `PlanetExplored`
- **Fichiers concernés** : `app/Providers/EventServiceProvider.php`
- **Estimation** : 15 min
- **Dépendances** : Tâche 4.1, Tâche 4.2
- **Tests** : Vérifier l'enregistrement des listeners

### Phase 5 : API Endpoints

#### Tâche 5.1 : Créer le FormRequest pour nommer une planète
- **Description** : Créer `NamePlanetRequest` avec validation (name: required|string|min:3|max:50|regex pour caractères autorisés)
- **Fichiers concernés** : `app/Http/Requests/NamePlanetRequest.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Tests de validation

#### Tâche 5.2 : Créer le FormRequest pour contribuer
- **Description** : Créer `ContributeToWikiRequest` avec validation (content: required|string|min:10|max:5000)
- **Fichiers concernés** : `app/Http/Requests/ContributeToWikiRequest.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Tests de validation

#### Tâche 5.3 : Créer le contrôleur WikiController
- **Description** : Créer le contrôleur API avec méthodes : `index()` (liste publique des planètes avec pagination, filtres), `show($id)` (détail d'une planète), `search()` (recherche avec autocomplétion), `namePlanet($id, NamePlanetRequest)` (nommer une planète, authentifié), `contribute($id, ContributeToWikiRequest)` (contribuer, authentifié)
- **Fichiers concernés** : `app/Http/Controllers/Api/WikiController.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 3.1, Tâche 5.1, Tâche 5.2
- **Tests** : Tests d'intégration des endpoints

#### Tâche 5.4 : Ajouter les routes API publiques
- **Description** : Ajouter les routes dans `routes/api.php` : `GET /wiki/planets` (publique), `GET /wiki/planets/{id}` (publique), `GET /wiki/search` (publique)
- **Fichiers concernés** : `routes/api.php`
- **Estimation** : 15 min
- **Dépendances** : Tâche 5.3
- **Tests** : Vérifier les routes

#### Tâche 5.5 : Ajouter les routes API authentifiées
- **Description** : Ajouter les routes dans `routes/api.php` avec middleware `auth:sanctum` : `POST /wiki/planets/{id}/name`, `POST /wiki/planets/{id}/contribute`
- **Fichiers concernés** : `routes/api.php`
- **Estimation** : 15 min
- **Dépendances** : Tâche 5.3
- **Tests** : Vérifier les routes et l'authentification

### Phase 6 : Frontend - Composants Livewire

#### Tâche 6.1 : Créer le composant WikiIndex
- **Description** : Composant Livewire pour la page d'accueil du wiki (`/wiki`). Affiche : liste des planètes récemment découvertes, liste des planètes les plus consultées, barre de recherche avec autocomplétion, filtres (Type, Taille, Température). Utilise les endpoints API publics. Design style encyclopédie spatiale (couleurs : #0a0e27, #1e3a5f, #ffffff).
- **Fichiers concernés** : `app/Livewire/WikiIndex.php`, `resources/views/livewire/wiki-index.blade.php`
- **Estimation** : 3h
- **Dépendances** : Tâche 5.3, Tâche 5.4
- **Tests** : Tests fonctionnels du composant

#### Tâche 6.2 : Créer le composant WikiPlanet
- **Description** : Composant Livewire pour la page détail d'une planète (`/wiki/planets/{id}`). Affiche : titre (nom ou fallback), auteur découvreur, date de découverte, caractéristiques complètes, description générée IA, image de la planète, bouton "Contribuer" (visible uniquement si authentifié et a exploré). Design cohérent avec WikiIndex.
- **Fichiers concernés** : `app/Livewire/WikiPlanet.php`, `resources/views/livewire/wiki-planet.blade.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 5.3, Tâche 5.4
- **Tests** : Tests fonctionnels du composant

#### Tâche 6.3 : Créer le composant NamePlanet (modal/formulaire)
- **Description** : Composant Livewire pour nommer une planète (modal ou formulaire). Validation côté client et serveur. Appelle `POST /api/wiki/planets/{id}/name`. Affiche les erreurs de validation.
- **Fichiers concernés** : `app/Livewire/NamePlanet.php`, `resources/views/livewire/name-planet.blade.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 5.3, Tâche 5.5
- **Tests** : Tests fonctionnels du composant

#### Tâche 6.4 : Créer le composant ContributeToWiki (modal/formulaire)
- **Description** : Composant Livewire pour contribuer à une page wiki. Validation côté client et serveur. Appelle `POST /api/wiki/planets/{id}/contribute`. Affiche les erreurs de validation.
- **Fichiers concernés** : `app/Livewire/ContributeToWiki.php`, `resources/views/livewire/contribute-to-wiki.blade.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 5.3, Tâche 5.5
- **Tests** : Tests fonctionnels du composant

#### Tâche 6.5 : Ajouter les routes web pour le wiki
- **Description** : Ajouter les routes dans `routes/web.php` : `GET /wiki` (publique, WikiIndex), `GET /wiki/planets/{id}` (publique, WikiPlanet)
- **Fichiers concernés** : `routes/web.php`
- **Estimation** : 15 min
- **Dépendances** : Tâche 6.1, Tâche 6.2
- **Tests** : Vérifier les routes

### Phase 7 : Tests et Documentation

#### Tâche 7.1 : Tests unitaires pour WikiService
- **Description** : Tests complets pour toutes les méthodes de WikiService (création d'article, validation de nom, nommage, génération fallback name)
- **Fichiers concernés** : `tests/Unit/Services/WikiServiceTest.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 3.1
- **Tests** : Tests unitaires

#### Tâche 7.2 : Tests unitaires pour AIDescriptionService
- **Description** : Tests avec mocks de l'API IA, gestion des erreurs, retry, cache
- **Fichiers concernés** : `tests/Unit/Services/AIDescriptionServiceTest.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 2.2
- **Tests** : Tests unitaires

#### Tâche 7.3 : Tests d'intégration pour les listeners
- **Description** : Tests des listeners (création d'article lors de PlanetCreated, PlanetExplored)
- **Fichiers concernés** : `tests/Feature/Listeners/CreateWikiEntryOnPlanetCreatedTest.php`, `tests/Feature/Listeners/CreateWikiEntryOnPlanetExploredTest.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 4.1, Tâche 4.2
- **Tests** : Tests d'intégration

#### Tâche 7.4 : Tests d'intégration pour les endpoints API
- **Description** : Tests complets des endpoints API (liste, détail, recherche, nommage, contribution)
- **Fichiers concernés** : `tests/Feature/Api/WikiControllerTest.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 5.3
- **Tests** : Tests d'intégration

#### Tâche 7.5 : Tests fonctionnels pour les composants Livewire
- **Description** : Tests des composants Livewire (WikiIndex, WikiPlanet, NamePlanet, ContributeToWiki)
- **Fichiers concernés** : `tests/Feature/Livewire/WikiIndexTest.php`, `tests/Feature/Livewire/WikiPlanetTest.php`, etc.
- **Estimation** : 2h
- **Dépendances** : Tâche 6.1, Tâche 6.2, Tâche 6.3, Tâche 6.4
- **Tests** : Tests fonctionnels

## Ordre d'Exécution

1. Phase 1 : Modèle de Données et Migrations (Tâches 1.1, 1.2, 1.3, 1.4)
2. Phase 2 : Service de Génération IA (Tâches 2.1, 2.2, 2.3)
3. Phase 3 : Service Wiki (Tâches 3.1, 3.2)
4. Phase 4 : Événements & Listeners (Tâches 4.1, 4.2, 4.3)
5. Phase 5 : API Endpoints (Tâches 5.1, 5.2, 5.3, 5.4, 5.5)
6. Phase 6 : Frontend - Composants Livewire (Tâches 6.1, 6.2, 6.3, 6.4, 6.5)
7. Phase 7 : Tests et Documentation (Tâches 7.1, 7.2, 7.3, 7.4, 7.5)

## Migrations de Base de Données

- [ ] Migration : Créer la table wiki_entries
- [ ] Migration : Créer la table wiki_contributions (optionnel MVP)

## Endpoints API

### Nouveaux Endpoints

#### Routes Publiques

- `GET /api/wiki/planets` - Liste des planètes avec pagination et filtres
  - Query params : `page`, `per_page`, `type`, `size`, `temperature`, `search`
  - Response : 
    ```json
    {
      "data": {
        "data": [...],
        "links": {...},
        "meta": {...}
      },
      "status": "success"
    }
    ```

- `GET /api/wiki/planets/{id}` - Détails d'une planète
  - Response : 
    ```json
    {
      "data": {
        "id": "...",
        "name": "...",
        "fallback_name": "...",
        "description": "...",
        "discovered_by": {...},
        "is_named": true,
        "planet": {...},
        "characteristics": {...}
      },
      "status": "success"
    }
    ```

- `GET /api/wiki/search` - Recherche avec autocomplétion
  - Query params : `q` (query string)
  - Response : 
    ```json
    {
      "data": [
        {"id": "...", "name": "...", "fallback_name": "..."},
        ...
      ],
      "status": "success"
    }
    ```

#### Routes Authentifiées

- `POST /api/wiki/planets/{id}/name` - Nommer une planète
  - Request body : 
    ```json
    {
      "name": "string"
    }
    ```
  - Response : 
    ```json
    {
      "data": {...},
      "message": "Planet named successfully",
      "status": "success"
    }
    ```
  - Validation : name (required|string|min:3|max:50|regex), vérification unicité, mots interdits

- `POST /api/wiki/planets/{id}/contribute` - Contribuer à une page wiki
  - Request body : 
    ```json
    {
      "content": "string"
    }
    ```
  - Response : 
    ```json
    {
      "data": {...},
      "message": "Contribution added successfully",
      "status": "success"
    }
    ```
  - Validation : content (required|string|min:10|max:5000), pas de mots interdits

## Événements & Listeners

### Nouveaux Listeners

- `CreateWikiEntryOnPlanetCreated` : Crée un article wiki lors de la création d'une planète d'origine
  - Écoute : `PlanetCreated`
  - Action : Vérifie si c'est une planète d'origine (via `home_planet_id`), crée un article avec `WikiService::createEntryForPlanet()`, assigne le découvreur si disponible

- `CreateWikiEntryOnPlanetExplored` : Crée un article wiki lors de l'exploration d'une planète
  - Écoute : `PlanetExplored`
  - Action : Vérifie l'existence d'un article, crée un article si inexistant avec `WikiService::createEntryForPlanet()`, assigne le découvreur

## Services & Classes

### Nouveaux Services

- `WikiService` : Service principal pour gérer les articles wiki
  - Méthodes : 
    - `createEntryForPlanet(Planet $planet, ?User $discoverer = null)` : Créer un article avec génération de description IA
    - `generateFallbackName(Planet $planet)` : Générer le nom de fallback technique (ex: "Planète Tellurique #1234")
    - `validateName(string $name)` : Valider un nom selon les règles (unicité, longueur, caractères, mots interdits)
    - `namePlanet(WikiEntry $entry, User $user, string $name)` : Nommer une planète avec validation complète
    - `canUserNamePlanet(WikiEntry $entry, User $user)` : Vérifier si l'utilisateur peut nommer (découvreur uniquement)
    - `canUserContribute(WikiEntry $entry, User $user)` : Vérifier si l'utilisateur peut contribuer (a exploré la planète)

- `AIDescriptionService` : Service pour générer des descriptions via IA
  - Méthodes : 
    - `generatePlanetDescription(Planet $planet)` : Générer une description basée sur les caractéristiques de la planète
    - `buildPrompt(Planet $planet)` : Construire le prompt pour l'IA à partir des caractéristiques
    - Gestion des erreurs, retry, cache des descriptions générées

### Classes Modifiées

- `EventServiceProvider` : Ajout des listeners pour `PlanetCreated` et `PlanetExplored`

## Tests

### Tests Unitaires

- [ ] Test : WikiService crée un article avec description IA
- [ ] Test : WikiService génère un nom de fallback correct
- [ ] Test : WikiService valide les noms correctement (unicité, format, mots interdits)
- [ ] Test : WikiService nomme une planète avec validation
- [ ] Test : WikiService vérifie les permissions (canUserNamePlanet, canUserContribute)
- [ ] Test : AIDescriptionService génère une description valide
- [ ] Test : AIDescriptionService gère les erreurs et retry
- [ ] Test : AIDescriptionService utilise le cache

### Tests d'Intégration

- [ ] Test : Listener CreateWikiEntryOnPlanetCreated crée un article lors de PlanetCreated
- [ ] Test : Listener CreateWikiEntryOnPlanetExplored crée un article si inexistant
- [ ] Test : GET /api/wiki/planets retourne la liste avec pagination
- [ ] Test : GET /api/wiki/planets/{id} retourne les détails d'une planète
- [ ] Test : GET /api/wiki/search retourne les résultats de recherche
- [ ] Test : POST /api/wiki/planets/{id}/name nomme une planète avec validation
- [ ] Test : POST /api/wiki/planets/{id}/name rejette les noms invalides
- [ ] Test : POST /api/wiki/planets/{id}/contribute ajoute une contribution

### Tests Fonctionnels

- [ ] Test : WikiIndex affiche la liste des planètes
- [ ] Test : WikiIndex filtre par type/taille/température
- [ ] Test : WikiIndex recherche avec autocomplétion
- [ ] Test : WikiPlanet affiche les détails d'une planète
- [ ] Test : WikiPlanet affiche le bouton "Contribuer" si autorisé
- [ ] Test : NamePlanet nomme une planète avec validation
- [ ] Test : ContributeToWiki ajoute une contribution

## Documentation

- [ ] Mettre à jour ARCHITECTURE.md avec les nouveaux endpoints et modèles
- [ ] Documenter WikiService et AIDescriptionService
- [ ] Ajouter des commentaires dans le code
- [ ] Documenter les règles de validation des noms dans config/wiki.php

## Notes Techniques

### Génération IA

- **Provider** : Utiliser OpenAI GPT (similaire à ImageGenerationService)
- **Prompt** : Construire un prompt détaillé basé sur les caractéristiques de la planète (type, taille, température, atmosphère, terrain, ressources)
- **Format** : Texte narratif + données scientifiques
- **Cache** : Mettre en cache les descriptions générées pour éviter les régénérations
- **Retry** : Implémenter un système de retry en cas d'échec
- **Fallback** : En cas d'échec de génération IA, utiliser un template pré-écrit avec variables

### Validation des Noms

- **Règles** :
  - Longueur : 3-50 caractères
  - Caractères autorisés : Lettres (a-z, A-Z, accents), chiffres (0-9), espaces, tirets (-), apostrophes (')
  - Unicité : Vérifier que le nom n'est pas déjà utilisé
  - Mots interdits : Liste de mots interdits dans `config/wiki.php`
- **Processus** :
  - Validation automatique côté serveur
  - Publication immédiate si validation réussie
  - Soumission à modération admin si validation échoue

### Performance

- **Recherche** : Index sur `name` et `fallback_name` dans `wiki_entries`
- **Cache** : Mettre en cache les descriptions générées
- **Pagination** : Utiliser la pagination Laravel pour les listes
- **Optimisation** : Eager loading des relations (planet, discovered_by) dans les requêtes

### Sécurité

- **Données publiques** : Ne pas exposer d'informations sensibles (emails, IDs utilisateurs complets)
- **Validation** : Toujours valider côté serveur, même si validation côté client existe
- **Autorisation** : Vérifier les permissions avant de permettre le nommage/contribution

### Design

- **Style** : Design épuré et scientifique, style encyclopédie spatiale
- **Couleurs** : Bleu foncé (#0a0e27), Bleu clair (#1e3a5f), Blanc (#ffffff)
- **Responsive** : Interface adaptée mobile/tablet/desktop
- **Composants** : Réutiliser les composants du design system existant

## Références

- [ISSUE-008-implement-public-wiki-stellarpedia.md](../issues/ISSUE-008-implement-public-wiki-stellarpedia.md)
- [DRAFT-04-stellarpedia-system-MVP.md](../game-design/drafts/DRAFT-04-stellarpedia-system-MVP.md) - Spécifications détaillées du MVP
- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Architecture technique, modèle de données, API endpoints
- [STACK.md](../memory_bank/STACK.md) - Stack technique
- [EVENTS.md](../EVENTS.md) - Documentation des événements (PlanetCreated, PlanetExplored)

