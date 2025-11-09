# TASK-001 : Implémenter le MVP complet de Space Xplorer

## Issue Associée

[ISSUE-001-implement-mvp.md](../issues/ISSUE-001-implement-mvp.md)

## Vue d'Ensemble

Implémenter le MVP complet de Space Xplorer incluant le système d'authentification complet (inscription, connexion, déconnexion), la génération automatique de planète d'origine via architecture événementielle, la visualisation de la planète sur le tableau de bord, et la gestion du profil utilisateur. L'approche est API-first : toute la logique métier est exposée via des endpoints REST API, et Livewire consomme ces APIs en interne pour l'interface utilisateur.

## Objectifs Techniques

- Créer les migrations de base de données pour les tables `users` et `planets`
- Implémenter les modèles Eloquent `User` et `Planet` avec leurs relations
- Développer le service `PlanetGeneratorService` pour la génération procédurale de planètes
- Implémenter l'architecture événementielle avec `UserRegistered` et `GenerateHomePlanet`
- Créer tous les endpoints API d'authentification et de gestion utilisateur/planète
- Développer les composants Livewire pour le tableau de bord et le profil
- Écrire une suite complète de tests (unitaires, intégration, fonctionnels)

## Architecture & Design

### Architecture Générale

- **Pattern** : MVC avec architecture événementielle
- **Approche** : API-first - Toute la logique métier via endpoints REST API
- **Frontend** : Livewire 3 pour l'interface utilisateur
- **Authentification** : Laravel Sanctum avec tokens
- **Base de données** : MySQL 8.0 avec migrations Laravel

### Flux d'Inscription et Génération de Planète

1. **POST /api/auth/register** → Création utilisateur → Événement `UserRegistered` dispatché
2. **Listener `GenerateHomePlanet`** → Appelle `PlanetGeneratorService` → Génère planète → Assigne `home_planet_id`
3. **Retour** : Token Sanctum + données utilisateur

### Structure des Fichiers

```
app/
├── Events/
│   └── UserRegistered.php
├── Listeners/
│   └── GenerateHomePlanet.php
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── UserController.php
│   │       └── PlanetController.php
│   └── Requests/
│       ├── RegisterRequest.php
│       ├── LoginRequest.php
│       └── UpdateProfileRequest.php
├── Models/
│   ├── User.php
│   └── Planet.php
├── Services/
│   └── PlanetGeneratorService.php
└── Livewire/
    ├── Dashboard.php
    └── Profile.php

database/
└── migrations/
    ├── YYYY_MM_DD_create_users_table.php
    ├── YYYY_MM_DD_create_planets_table.php
    └── YYYY_MM_DD_add_home_planet_id_to_users_table.php
```

## Tâches de Développement

### Phase 1 : Base de Données et Modèles

#### Tâche 1.1 : Créer la migration pour la table users
- [x] ✅ **Terminée** - Migration users existante (Laravel par défaut)
- **Fichiers concernés** : `database/migrations/0001_01_01_000000_create_users_table.php`
- **Notes** : Migration Laravel par défaut contient tous les champs nécessaires

#### Tâche 1.2 : Créer la migration pour la table planets
- [x] ✅ **Terminée**
- **Fichiers créés** : `database/migrations/2025_11_09_092648_create_planets_table.php`
- **Détails** : Migration créée avec tous les champs (name, type, size, temperature, atmosphere, terrain, resources, description)

#### Tâche 1.3 : Ajouter la colonne home_planet_id à la table users
- [x] ✅ **Terminée**
- **Fichiers créés** : `database/migrations/2025_11_09_092654_add_home_planet_id_to_users_table.php`
- **Détails** : Colonne nullable avec foreign key vers planets.id, onDelete('set null')

#### Tâche 1.4 : Créer le modèle User
- [x] ✅ **Terminée**
- **Fichiers modifiés** : `app/Models/User.php`
- **Détails** : Ajout de HasApiTokens (Sanctum), relation homePlanet(), home_planet_id dans fillable

#### Tâche 1.5 : Créer le modèle Planet
- [x] ✅ **Terminée**
- **Fichiers créés** : `app/Models/Planet.php`
- **Détails** : Modèle créé avec fillable, relation users() vers User

### Phase 2 : Service de Génération de Planètes

#### Tâche 2.1 : Créer la configuration des types de planètes
- **Description** : Créer un fichier de configuration ou une classe pour définir les types de planètes avec leurs poids de probabilité et leurs distributions de caractéristiques. Types : Tellurique (40%), Gazeuse (25%), Glacée (15%), Désertique (10%), Océanique (10%)
- **Fichiers concernés** : `config/planets.php` ou `app/Data/PlanetTypes.php`
- **Estimation** : 1h30
- **Dépendances** : Aucune
- **Tests** : Tests de la configuration et des poids de probabilité

#### Tâche 2.2 : Créer PlanetGeneratorService
- **Description** : Service pour générer des planètes avec le système de poids. Méthodes principales : `generate()` (génère une planète complète), `selectPlanetType()` (sélection pondérée), `generateCharacteristics()` (génère les caractéristiques selon le type), `generateName()` (génère un nom aléatoire), `generateDescription()` (génère une description à partir des caractéristiques)
- **Fichiers concernés** : `app/Services/PlanetGeneratorService.php`
- **Estimation** : 3h
- **Dépendances** : Tâche 2.1, Tâche 1.5
- **Tests** : Tests unitaires complets du service (génération, poids, unicité)

### Phase 3 : Architecture Événementielle

#### Tâche 3.1 : Créer l'événement UserRegistered
- **Description** : Événement dispatché lors de l'inscription d'un nouveau joueur. Contient l'instance User créée
- **Fichiers concernés** : `app/Events/UserRegistered.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 1.4
- **Tests** : Tests de l'événement et de ses propriétés

#### Tâche 3.2 : Créer le listener GenerateHomePlanet
- **Description** : Listener qui écoute UserRegistered, appelle PlanetGeneratorService pour générer une planète, crée la planète en base, et assigne home_planet_id au joueur
- **Fichiers concernés** : `app/Listeners/GenerateHomePlanet.php`
- **Estimation** : 1h30
- **Dépendances** : Tâche 2.2, Tâche 3.1
- **Tests** : Tests du listener (génération, assignation, gestion d'erreurs)

#### Tâche 3.3 : Enregistrer l'événement et le listener
- **Description** : Enregistrer l'événement et le listener dans `app/Providers/EventServiceProvider.php`
- **Fichiers concernés** : `app/Providers/EventServiceProvider.php`
- **Estimation** : 15 min
- **Dépendances** : Tâche 3.1, Tâche 3.2
- **Tests** : Vérifier que l'événement est bien dispatché et écouté

### Phase 4 : API Endpoints - Authentification

#### Tâche 4.1 : Créer RegisterRequest
- **Description** : FormRequest pour valider les données d'inscription : name (required|string|max:255), email (required|email|unique:users|max:255), password (required|string|min:8|confirmed)
- **Fichiers concernés** : `app/Http/Requests/RegisterRequest.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Tests de validation (succès, erreurs)

#### Tâche 4.2 : Créer LoginRequest
- **Description** : FormRequest pour valider les données de connexion : email (required|email), password (required|string)
- **Fichiers concernés** : `app/Http/Requests/LoginRequest.php`
- **Estimation** : 20 min
- **Dépendances** : Aucune
- **Tests** : Tests de validation

#### Tâche 4.3 : Créer AuthController avec endpoint register
- **Description** : Créer le contrôleur API AuthController avec la méthode register() qui crée l'utilisateur, dispatch l'événement UserRegistered, crée un token Sanctum, et retourne la réponse JSON standardisée
- **Fichiers concernés** : `app/Http/Controllers/Api/AuthController.php`
- **Estimation** : 1h30
- **Dépendances** : Tâche 4.1, Tâche 3.1
- **Tests** : Tests d'intégration de l'endpoint (succès, validation, génération planète)

#### Tâche 4.4 : Ajouter endpoint login dans AuthController
- **Description** : Méthode login() qui authentifie l'utilisateur, crée un token Sanctum, et retourne la réponse JSON standardisée
- **Fichiers concernés** : `app/Http/Controllers/Api/AuthController.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 4.2, Tâche 4.3
- **Tests** : Tests d'intégration (succès, identifiants incorrects)

#### Tâche 4.5 : Ajouter endpoint logout dans AuthController
- **Description** : Méthode logout() qui révoque le token Sanctum de l'utilisateur connecté
- **Fichiers concernés** : `app/Http/Controllers/Api/AuthController.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 4.4
- **Tests** : Tests d'intégration (succès, non authentifié)

#### Tâche 4.6 : Ajouter endpoint user dans AuthController
- **Description** : Méthode user() qui retourne les informations de l'utilisateur connecté avec sa planète d'origine
- **Fichiers concernés** : `app/Http/Controllers/Api/AuthController.php`
- **Estimation** : 45 min
- **Dépendances** : Tâche 4.5
- **Tests** : Tests d'intégration (succès, non authentifié)

#### Tâche 4.7 : Ajouter les routes API d'authentification
- **Description** : Ajouter les routes dans routes/api.php : POST /api/auth/register, POST /api/auth/login, POST /api/auth/logout (auth:sanctum), GET /api/auth/user (auth:sanctum)
- **Fichiers concernés** : `routes/api.php`
- **Estimation** : 20 min
- **Dépendances** : Tâche 4.6
- **Tests** : Vérifier que les routes sont accessibles

### Phase 5 : API Endpoints - Utilisateurs et Planètes

#### Tâche 5.1 : Créer UpdateProfileRequest
- **Description** : FormRequest pour valider la mise à jour du profil : name (sometimes|string|max:255), email (sometimes|email|unique:users,email,{id}|max:255)
- **Fichiers concernés** : `app/Http/Requests/UpdateProfileRequest.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Tests de validation

#### Tâche 5.2 : Créer UserController
- **Description** : Créer le contrôleur API UserController avec les méthodes : show() (GET /api/users/{id}), update() (PUT /api/users/{id}), getHomePlanet() (GET /api/users/{id}/home-planet). Toutes protégées par auth:sanctum
- **Fichiers concernés** : `app/Http/Controllers/Api/UserController.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 5.1
- **Tests** : Tests d'intégration de tous les endpoints

#### Tâche 5.3 : Créer PlanetController
- **Description** : Créer le contrôleur API PlanetController avec la méthode show() (GET /api/planets/{id}) protégée par auth:sanctum
- **Fichiers concernés** : `app/Http/Controllers/Api/PlanetController.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 1.5
- **Tests** : Tests d'intégration de l'endpoint

#### Tâche 5.4 : Ajouter les routes API utilisateurs et planètes
- **Description** : Ajouter les routes dans routes/api.php : GET /api/users/{id}, PUT /api/users/{id}, GET /api/users/{id}/home-planet, GET /api/planets/{id}. Toutes protégées par auth:sanctum
- **Fichiers concernés** : `routes/api.php`
- **Estimation** : 20 min
- **Dépendances** : Tâche 5.2, Tâche 5.3
- **Tests** : Vérifier que les routes sont accessibles

### Phase 6 : Frontend - Composants Livewire

#### Tâche 6.1 : Configurer Sanctum pour Livewire
- **Description** : Configurer Sanctum pour que Livewire puisse consommer les APIs en interne. S'assurer que les requêtes internes Livewire utilisent les tokens Sanctum correctement
- **Fichiers concernés** : `config/sanctum.php`, `app/Http/Middleware/` (si nécessaire)
- **Estimation** : 1h
- **Dépendances** : Phase 4
- **Tests** : Vérifier que Livewire peut appeler les APIs

#### Tâche 6.2 : Créer le layout principal
- **Description** : Créer le layout Blade principal avec navigation, structure HTML de base, intégration Tailwind CSS et Alpine.js
- **Fichiers concernés** : `resources/views/layouts/app.blade.php`
- **Estimation** : 1h30
- **Dépendances** : Aucune
- **Tests** : Vérifier le rendu du layout

#### Tâche 6.3 : Créer la page d'accueil
- **Description** : Créer la page d'accueil avec présentation du jeu et liens vers inscription/connexion
- **Fichiers concernés** : `resources/views/welcome.blade.php` ou composant Livewire
- **Estimation** : 1h
- **Dépendances** : Tâche 6.2
- **Tests** : Vérifier le rendu

#### Tâche 6.4 : Créer le composant Livewire Register
- **Description** : Composant Livewire pour le formulaire d'inscription avec validation côté client et serveur, appel à POST /api/auth/register, gestion des erreurs, redirection vers dashboard après succès
- **Fichiers concernés** : `app/Livewire/Register.php`, `resources/views/livewire/register.blade.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 6.2, Tâche 4.3
- **Tests** : Tests fonctionnels du formulaire

#### Tâche 6.5 : Créer le composant Livewire Login
- **Description** : Composant Livewire pour le formulaire de connexion avec validation, appel à POST /api/auth/login, gestion des erreurs, redirection vers dashboard après succès
- **Fichiers concernés** : `app/Livewire/Login.php`, `resources/views/livewire/login.blade.php`
- **Estimation** : 1h30
- **Dépendances** : Tâche 6.2, Tâche 4.4
- **Tests** : Tests fonctionnels du formulaire

#### Tâche 6.6 : Créer le composant Livewire Dashboard
- **Description** : Composant Livewire pour le tableau de bord qui affiche la planète d'origine du joueur. Appelle GET /api/users/{id}/home-planet, affiche toutes les caractéristiques de la planète avec un design attrayant, affiche le nom et la description
- **Fichiers concernés** : `app/Livewire/Dashboard.php`, `resources/views/livewire/dashboard.blade.php`
- **Estimation** : 3h
- **Dépendances** : Tâche 6.2, Tâche 5.2
- **Tests** : Tests fonctionnels de l'affichage

#### Tâche 6.7 : Créer le composant Livewire Profile
- **Description** : Composant Livewire pour la gestion du profil utilisateur. Affiche les informations (nom, email), permet la mise à jour via PUT /api/users/{id}, gestion des erreurs et messages de succès
- **Fichiers concernés** : `app/Livewire/Profile.php`, `resources/views/livewire/profile.blade.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 6.2, Tâche 5.2
- **Tests** : Tests fonctionnels du profil

#### Tâche 6.8 : Ajouter la navigation et la déconnexion
- **Description** : Ajouter la navigation dans le layout avec liens vers dashboard et profile, bouton de déconnexion qui appelle POST /api/auth/logout et redirige vers la page d'accueil
- **Fichiers concernés** : `resources/views/layouts/app.blade.php` ou composant Navigation
- **Estimation** : 1h
- **Dépendances** : Tâche 6.2, Tâche 4.5
- **Tests** : Tests fonctionnels de la navigation

#### Tâche 6.9 : Ajouter les routes web
- **Description** : Ajouter les routes web dans routes/web.php pour les pages publiques (accueil, register, login) et les pages protégées (dashboard, profile). Utiliser middleware auth:sanctum pour les pages protégées
- **Fichiers concernés** : `routes/web.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 6.3 à Tâche 6.8
- **Tests** : Vérifier que les routes sont accessibles

### Phase 7 : Tests

#### Tâche 7.1 : Tests unitaires PlanetGeneratorService
- **Description** : Tests complets du service : génération de planète valide, respect des poids de probabilité, génération de nom unique, génération de description cohérente
- **Fichiers concernés** : `tests/Unit/Services/PlanetGeneratorServiceTest.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 2.2
- **Tests** : Exécuter les tests unitaires

#### Tâche 7.2 : Tests unitaires GenerateHomePlanet
- **Description** : Tests du listener : génération et assignation de planète, gestion des erreurs
- **Fichiers concernés** : `tests/Unit/Listeners/GenerateHomePlanetTest.php`
- **Estimation** : 1h30
- **Dépendances** : Tâche 3.2
- **Tests** : Exécuter les tests unitaires

#### Tâche 7.3 : Tests d'intégration API AuthController
- **Description** : Tests complets des endpoints d'authentification : register (succès, validation, génération planète), login (succès, identifiants incorrects), logout (succès, non authentifié), user (succès, non authentifié)
- **Fichiers concernés** : `tests/Feature/Api/AuthControllerTest.php`
- **Estimation** : 3h
- **Dépendances** : Phase 4
- **Tests** : Exécuter les tests d'intégration

#### Tâche 7.4 : Tests d'intégration API UserController et PlanetController
- **Description** : Tests complets des endpoints : GET /api/users/{id}, PUT /api/users/{id}, GET /api/users/{id}/home-planet, GET /api/planets/{id}. Tester l'authentification, les autorisations, les validations
- **Fichiers concernés** : `tests/Feature/Api/UserControllerTest.php`, `tests/Feature/Api/PlanetControllerTest.php`
- **Estimation** : 2h30
- **Dépendances** : Phase 5
- **Tests** : Exécuter les tests d'intégration

#### Tâche 7.5 : Tests fonctionnels Livewire
- **Description** : Tests fonctionnels des composants Livewire : Register, Login, Dashboard, Profile. Tester les interactions utilisateur, les validations, les redirections
- **Fichiers concernés** : `tests/Feature/Livewire/RegisterTest.php`, `tests/Feature/Livewire/LoginTest.php`, `tests/Feature/Livewire/DashboardTest.php`, `tests/Feature/Livewire/ProfileTest.php`
- **Estimation** : 3h
- **Dépendances** : Phase 6
- **Tests** : Exécuter les tests fonctionnels

### Phase 8 : Finalisation

#### Tâche 8.1 : Formatage du code avec Laravel Pint
- **Description** : Exécuter Laravel Pint sur tout le code pour assurer la cohérence du formatage
- **Fichiers concernés** : Tous les fichiers PHP créés/modifiés
- **Estimation** : 30 min
- **Dépendances** : Toutes les phases précédentes
- **Tests** : Vérifier que Pint ne modifie plus rien

#### Tâche 8.2 : Vérification de la documentation
- **Description** : Vérifier que la documentation ARCHITECTURE.md est à jour avec les nouveaux endpoints et flux
- **Fichiers concernés** : `docs/memory_bank/ARCHITECTURE.md`
- **Estimation** : 1h
- **Dépendances** : Toutes les phases précédentes
- **Tests** : Vérifier la cohérence de la documentation

#### Tâche 8.3 : Tests end-to-end complets
- **Description** : Effectuer des tests manuels complets du parcours utilisateur : inscription → visualisation planète → gestion profil → déconnexion → connexion
- **Fichiers concernés** : Aucun (tests manuels)
- **Estimation** : 1h
- **Dépendances** : Toutes les phases précédentes
- **Tests** : Valider le parcours complet

## Ordre d'Exécution

1. **Phase 1** : Base de Données et Modèles (Tâches 1.1 → 1.5)
2. **Phase 2** : Service de Génération de Planètes (Tâches 2.1 → 2.2)
3. **Phase 3** : Architecture Événementielle (Tâches 3.1 → 3.3)
4. **Phase 4** : API Endpoints - Authentification (Tâches 4.1 → 4.7)
5. **Phase 5** : API Endpoints - Utilisateurs et Planètes (Tâches 5.1 → 5.4)
6. **Phase 6** : Frontend - Composants Livewire (Tâches 6.1 → 6.9)
7. **Phase 7** : Tests (Tâches 7.1 → 7.5) - Peut être fait en parallèle avec les phases précédentes
8. **Phase 8** : Finalisation (Tâches 8.1 → 8.3)

## Migrations de Base de Données

- [ ] Migration : Créer la table users (id, name, email, password, email_verified_at, remember_token, timestamps)
- [ ] Migration : Créer la table planets (id, name, type, size, temperature, atmosphere, terrain, resources, description, timestamps)
- [ ] Migration : Ajouter home_planet_id (nullable, unsigned big integer, foreign key vers planets.id) à la table users

## Endpoints API

### Nouveaux Endpoints

#### Authentification

- `POST /api/auth/register` - Inscription d'un nouveau joueur
  - Request body : 
    ```json
    {
      "name": "string",
      "email": "string",
      "password": "string",
      "password_confirmation": "string"
    }
    ```
  - Response : 
    ```json
    {
      "data": {
        "user": {
          "id": 1,
          "name": "John Doe",
          "email": "john@example.com"
        },
        "token": "sanctum_token_string"
      },
      "message": "User registered successfully",
      "status": "success"
    }
    ```
  - Validation : name (required|string|max:255), email (required|email|unique:users|max:255), password (required|string|min:8|confirmed)
  - Notes : Déclenche automatiquement la génération de planète d'origine

- `POST /api/auth/login` - Connexion
  - Request body : 
    ```json
    {
      "email": "string",
      "password": "string"
    }
    ```
  - Response : 
    ```json
    {
      "data": {
        "user": {...},
        "token": "sanctum_token_string"
      },
      "message": "Login successful",
      "status": "success"
    }
    ```
  - Validation : email (required|email), password (required|string)

- `POST /api/auth/logout` - Déconnexion
  - Headers : Authorization: Bearer {token}
  - Response : 
    ```json
    {
      "message": "Logged out successfully",
      "status": "success"
    }
    ```
  - Protection : auth:sanctum

- `GET /api/auth/user` - Informations du joueur connecté
  - Headers : Authorization: Bearer {token}
  - Response : 
    ```json
    {
      "data": {
        "user": {
          "id": 1,
          "name": "John Doe",
          "email": "john@example.com",
          "home_planet_id": 1
        }
      },
      "status": "success"
    }
    ```
  - Protection : auth:sanctum

#### Utilisateurs

- `GET /api/users/{id}` - Détails d'un utilisateur
  - Headers : Authorization: Bearer {token}
  - Response : 
    ```json
    {
      "data": {
        "user": {
          "id": 1,
          "name": "John Doe",
          "email": "john@example.com",
          "home_planet_id": 1
        }
      },
      "status": "success"
    }
    ```
  - Protection : auth:sanctum

- `PUT /api/users/{id}` - Mise à jour du profil utilisateur
  - Headers : Authorization: Bearer {token}
  - Request body : 
    ```json
    {
      "name": "string (optional)",
      "email": "string (optional)"
    }
    ```
  - Response : 
    ```json
    {
      "data": {
        "user": {...}
      },
      "message": "Profile updated successfully",
      "status": "success"
    }
    ```
  - Validation : name (sometimes|string|max:255), email (sometimes|email|unique:users,email,{id}|max:255)
  - Protection : auth:sanctum

- `GET /api/users/{id}/home-planet` - Planète d'origine du joueur
  - Headers : Authorization: Bearer {token}
  - Response : 
    ```json
    {
      "data": {
        "planet": {
          "id": 1,
          "name": "Kepler-452b",
          "type": "tellurique",
          "size": "moyenne",
          "temperature": "tempérée",
          "atmosphere": "respirable",
          "terrain": "forestier",
          "resources": "abondantes",
          "description": "..."
        }
      },
      "status": "success"
    }
    ```
  - Protection : auth:sanctum

#### Planètes

- `GET /api/planets/{id}` - Détails d'une planète
  - Headers : Authorization: Bearer {token}
  - Response : 
    ```json
    {
      "data": {
        "planet": {
          "id": 1,
          "name": "Kepler-452b",
          "type": "tellurique",
          "size": "moyenne",
          "temperature": "tempérée",
          "atmosphere": "respirable",
          "terrain": "forestier",
          "resources": "abondantes",
          "description": "..."
        }
      },
      "status": "success"
    }
    ```
  - Protection : auth:sanctum

## Événements & Listeners

### Nouveaux Événements

- `UserRegistered` : Déclenché lors de la création d'un utilisateur
  - Déclenché quand : Un nouvel utilisateur est créé via POST /api/auth/register
  - Propriétés : `public User $user`
  - Listeners : `GenerateHomePlanet`

### Nouveaux Listeners

- `GenerateHomePlanet` : Génère une planète d'origine et l'assigne au joueur
  - Écoute : `UserRegistered`
  - Action : 
    1. Appelle `PlanetGeneratorService::generate()` pour créer une planète
    2. Crée la planète en base de données
    3. Assigne `home_planet_id` au joueur
    4. Sauvegarde le joueur
  - Gestion d'erreurs : En cas d'erreur de génération, logger l'erreur et ne pas bloquer l'inscription (home_planet_id reste null)

## Services & Classes

### Nouveaux Services

- `PlanetGeneratorService` : Service de génération procédurale de planètes
  - Méthodes : 
    - `generate(): Planet` : Génère une planète complète avec toutes les caractéristiques
    - `selectPlanetType(): string` : Sélectionne un type de planète selon les poids de probabilité
    - `generateCharacteristics(string $type): array` : Génère les caractéristiques (size, temperature, etc.) selon le type sélectionné
    - `generateName(): string` : Génère un nom aléatoire pour la planète (ex: "Kepler-452b", "Proxima Centauri c")
    - `generateDescription(Planet $planet): string` : Génère une description textuelle à partir des caractéristiques combinées
  - Configuration : Utilise la configuration des types de planètes avec leurs poids

### Classes Modifiées

- `User` : Ajout de la relation `homePlanet()` et utilisation de `HasApiTokens` de Sanctum
- `Planet` : Ajout de la relation inverse `users()` vers User

## Tests

### Tests Unitaires

- [ ] Test : PlanetGeneratorService génère une planète valide avec toutes les caractéristiques
- [ ] Test : PlanetGeneratorService respecte les poids de probabilité des types (Tellurique 40%, etc.)
- [ ] Test : PlanetGeneratorService génère des noms uniques
- [ ] Test : PlanetGeneratorService génère des descriptions cohérentes
- [ ] Test : GenerateHomePlanet assigne correctement la planète au joueur
- [ ] Test : GenerateHomePlanet gère les erreurs de génération
- [ ] Test : Modèle User a la relation homePlanet()
- [ ] Test : Modèle Planet a la relation users()

### Tests d'Intégration

- [ ] Test : POST /api/auth/register crée un utilisateur et une planète
- [ ] Test : POST /api/auth/register retourne un token Sanctum valide
- [ ] Test : POST /api/auth/register valide les données d'entrée
- [ ] Test : POST /api/auth/login authentifie correctement
- [ ] Test : POST /api/auth/login retourne un token Sanctum
- [ ] Test : POST /api/auth/logout révoque le token
- [ ] Test : GET /api/auth/user retourne l'utilisateur connecté
- [ ] Test : GET /api/users/{id} retourne les détails de l'utilisateur
- [ ] Test : PUT /api/users/{id} met à jour le profil
- [ ] Test : GET /api/users/{id}/home-planet retourne la planète d'origine
- [ ] Test : GET /api/planets/{id} retourne les détails de la planète
- [ ] Test : Toutes les routes API nécessitent l'authentification (sauf register/login)
- [ ] Test : L'événement UserRegistered est bien dispatché lors de l'inscription

### Tests Fonctionnels

- [ ] Test : Inscription complète avec génération de planète (end-to-end)
- [ ] Test : Connexion et redirection vers dashboard
- [ ] Test : Affichage de la planète d'origine sur le dashboard
- [ ] Test : Mise à jour du profil utilisateur
- [ ] Test : Déconnexion et redirection vers accueil
- [ ] Test : Validation des formulaires côté client et serveur
- [ ] Test : Gestion des erreurs d'authentification (email déjà utilisé, identifiants incorrects)

## Documentation

- [ ] Mettre à jour ARCHITECTURE.md avec les nouveaux endpoints et flux
- [ ] Documenter PlanetGeneratorService avec des commentaires PHPDoc
- [ ] Ajouter des commentaires dans le code pour les parties complexes
- [ ] Documenter les routes API (peut être fait via Laravel API documentation ou commentaires)

## Notes Techniques

### Authentification

- Utiliser Laravel Sanctum pour l'authentification par tokens
- Les tokens Sanctum sont créés lors de l'inscription et de la connexion
- Les tokens sont révoqués lors de la déconnexion
- Middleware `auth:sanctum` protège toutes les routes API (sauf register/login)
- Livewire doit être configuré pour utiliser Sanctum en interne

### Génération de Planètes

- La génération de planète doit être synchrone pour l'instant (peut être async plus tard avec queues)
- La génération doit être rapide (< 1 seconde)
- Chaque planète générée doit être unique (pas de doublons exacts)
- Le nom de la planète doit être unique ou gérer les collisions
- Gérer les erreurs de génération élégamment (ne pas bloquer l'inscription)

### Format de Réponse API

- Toutes les réponses API suivent le format JSON standardisé :
  ```json
  {
    "data": { ... },
    "message": "Success message (optional)",
    "status": "success"
  }
  ```
- Les erreurs suivent le format Laravel standard avec codes HTTP appropriés

### Performance

- La génération de planète doit être instantanée (< 1 seconde)
- Optimiser les requêtes de base de données (eager loading pour les relations)
- Utiliser le cache Redis si nécessaire pour les données fréquemment accédées

### Sécurité

- Validation stricte des données d'entrée via FormRequest
- Protection CSRF pour les routes web
- Hachage sécurisé des mots de passe (bcrypt)
- Protection des routes API avec middleware d'authentification
- Validation de l'unicité de l'email lors de l'inscription

### Frontend

- Utiliser Tailwind CSS pour le styling avec un design system personnalisé
- Utiliser Alpine.js pour les interactions côté client si nécessaire
- Assurer la responsivité de l'interface
- Créer une expérience utilisateur fluide et intuitive
- La découverte de la planète doit être un moment mémorable visuellement

## Références

- [ISSUE-001-implement-mvp.md](../issues/ISSUE-001-implement-mvp.md) - Issue produit associée
- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Architecture technique complète, modèle de données, endpoints API, flux métier
- [STACK.md](../memory_bank/STACK.md) - Stack technique détaillée
- [PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md) - Vision métier, fonctionnalités MVP, personas, flux utilisateurs, système de planètes

## Review Architecturale

### Statut

⚠️ Approuvé avec recommandations

### Vue d'Ensemble

Le plan technique est globalement bien structuré et respecte l'architecture définie dans ARCHITECTURE.md. L'approche API-first est correctement suivie, l'utilisation de l'architecture événementielle pour découpler la génération de planète est excellente, et la structure des fichiers est cohérente avec l'organisation du projet. Le plan est prêt pour l'implémentation avec quelques recommandations pour améliorer la robustesse et la maintenabilité.

### Cohérence Architecturale

#### ✅ Points Positifs

- **Approche API-first respectée** : Toute la logique métier est correctement exposée via des endpoints REST API, et Livewire consomme ces APIs en interne, conformément à l'architecture définie.
- **Architecture événementielle bien utilisée** : L'utilisation de l'événement `UserRegistered` et du listener `GenerateHomePlanet` découple parfaitement la génération de planète de la création d'utilisateur, suivant le pattern Events & Listeners défini.
- **Structure des fichiers cohérente** : L'organisation des fichiers respecte la structure MVC Laravel avec séparation claire des responsabilités (Events, Listeners, Services, Controllers, Models).
- **Services bien utilisés** : Le `PlanetGeneratorService` encapsule correctement la logique métier complexe de génération procédurale.
- **FormRequests pour validation** : Toutes les entrées API utilisent des FormRequest, respectant les bonnes pratiques Laravel.

#### ⚠️ Points d'Attention

- **Configuration des types de planètes** : Le plan mentionne deux options (`config/planets.php` ou `app/Data/PlanetTypes.php`) sans justifier le choix. Pour la cohérence avec Laravel, `config/planets.php` est recommandé.
- **Gestion d'erreurs lors de la génération** : Le plan mentionne "gérer les erreurs élégamment" mais ne détaille pas suffisamment la stratégie de gestion d'erreurs (rollback, retry, logging).
- **Unicité des noms de planètes** : Le plan mentionne "nom unique ou gérer les collisions" sans préciser le mécanisme exact (vérification d'unicité, UUID dans le nom, etc.).

### Qualité Technique

#### Choix Techniques

- **Événements & Listeners** : ✅ Validé
  - Excellente utilisation de l'architecture événementielle pour découpler la logique métier. Le flux `UserRegistered` → `GenerateHomePlanet` est clair et maintenable.

- **Service PlanetGeneratorService** : ✅ Validé
  - Bon choix pour encapsuler la logique de génération procédurale. La séparation des responsabilités (sélection du type, génération des caractéristiques, génération du nom, génération de la description) est bien pensée.

- **FormRequest pour validation** : ✅ Validé
  - Respect des bonnes pratiques Laravel. Les règles de validation sont appropriées et complètes.

- **Laravel Sanctum** : ✅ Validé
  - Choix approprié pour l'authentification par tokens. L'utilisation de `HasApiTokens` dans le modèle User est correcte.

- **Migrations Laravel** : ✅ Validé
  - Utilisation correcte des migrations Laravel pour la gestion du schéma de base de données. Les relations sont bien définies avec les foreign keys.

#### Structure & Organisation

- **Structure** : ✅ Cohérente
  - Les 8 phases sont logiques et bien ordonnées. L'ordre d'exécution respecte les dépendances (Base de données → Services → Événements → API → Frontend → Tests → Finalisation).

- **Dépendances** : ✅ Bien gérées
  - Les dépendances entre les tâches sont clairement identifiées. L'ordre d'exécution est cohérent.

#### Points d'Amélioration

- **Configuration Sanctum pour Livewire** : La tâche 6.1 mentionne la configuration de Sanctum pour Livewire mais ne détaille pas suffisamment. Dans un contexte API-first, Livewire doit pouvoir consommer les APIs internes avec les tokens Sanctum. Cette configuration peut nécessiter des ajustements dans le middleware ou la configuration Sanctum.

### Performance & Scalabilité

#### Points Positifs

- **Génération synchrone acceptable pour MVP** : La génération synchrone de planète est appropriée pour le MVP. Le plan mentionne que cela peut être async plus tard avec queues, ce qui montre une vision évolutive.

- **Structure évolutive** : L'architecture événementielle permet d'ajouter facilement de nouveaux listeners ou événements sans modifier le code existant.

- **Eager loading prévu** : Le plan mentionne l'optimisation des requêtes avec eager loading pour les relations, ce qui est une bonne pratique.

#### Recommandations

- **Recommandation Performance** : Considérer l'utilisation de queues pour la génération de planète si le processus devient plus complexe à l'avenir.
  - **Justification** : Pour le MVP, synchrone est acceptable (< 1 seconde), mais prévoir l'évolution vers l'asynchrone si nécessaire.
  - **Priorité** : Low (pour le MVP)

- **Recommandation Cache** : Le plan mentionne Redis pour le cache mais ne détaille pas quelles données seront mises en cache. Considérer le cache des planètes fréquemment accédées.
  - **Justification** : Les planètes sont des données relativement statiques qui peuvent bénéficier du cache.
  - **Priorité** : Low (optimisation future)

### Sécurité

#### Validations

- ✅ **Validations prévues et complètes**
  - FormRequest avec règles appropriées pour chaque endpoint
  - Validation d'email unique lors de l'inscription
  - Validation de mot de passe avec confirmation et longueur minimale (8 caractères)
  - Validation "sometimes" pour la mise à jour du profil (permet les mises à jour partielles)

#### Authentification & Autorisation

- ✅ **Gestion correcte**
  - Utilisation de Sanctum pour les tokens d'authentification
  - Middleware `auth:sanctum` protège toutes les routes API appropriées
  - Pas de système de rôles nécessaire pour le MVP (conforme à l'architecture)
  - Protection CSRF mentionnée pour les routes web

#### Points d'Attention

- **Autorisation sur les endpoints utilisateurs** : Le plan ne mentionne pas explicitement la vérification que l'utilisateur ne peut modifier que son propre profil. Cette vérification doit être implémentée dans `UserController::update()`.
  - **Recommandation** : Ajouter une vérification dans le contrôleur ou utiliser une Policy pour s'assurer qu'un utilisateur ne peut modifier que son propre profil.
  - **Priorité** : High

### Tests

#### Couverture

- ✅ **Tests complets prévus**
  - Tests unitaires pour le `PlanetGeneratorService` (génération, poids, unicité)
  - Tests unitaires pour le listener `GenerateHomePlanet`
  - Tests d'intégration pour tous les endpoints API (AuthController, UserController, PlanetController)
  - Tests fonctionnels pour les composants Livewire (Register, Login, Dashboard, Profile)
  - Tests des modèles et relations

#### Recommandations

- **Test additionnel recommandé** : Tester le cas où la génération de planète échoue lors de l'inscription.
  - **Priorité** : High
  - **Raison** : Assurer la robustesse du système et vérifier que l'inscription n'est pas bloquée si la génération échoue.

- **Test additionnel recommandé** : Tester l'autorisation (un utilisateur ne peut pas modifier le profil d'un autre utilisateur).
  - **Priorité** : High
  - **Raison** : Sécurité critique pour éviter les accès non autorisés.

- **Test additionnel recommandé** : Tester les collisions de noms de planètes et le mécanisme de gestion.
  - **Priorité** : Medium
  - **Raison** : Vérifier que le système gère correctement les collisions potentielles.

### Documentation

#### Mise à Jour

- ✅ **Documentation prévue**
  - Mise à jour de ARCHITECTURE.md prévue dans la tâche 8.2
  - Documentation PHPDoc prévue pour `PlanetGeneratorService`
  - Commentaires prévus pour les parties complexes

#### Recommandations

- **Documentation API** : Le plan mentionne "Documenter les routes API (peut être fait via Laravel API documentation ou commentaires)" mais ne précise pas la méthode choisie. Considérer l'utilisation d'un outil comme Laravel API Documentation ou des commentaires PHPDoc structurés.
  - **Priorité** : Low

### Recommandations Spécifiques

#### Recommandation 1 : Gestion d'erreurs robuste pour la génération de planète

**Problème** : Le plan mentionne "gérer les erreurs élégamment" mais ne détaille pas suffisamment la stratégie de gestion d'erreurs si la génération de planète échoue.

**Impact** : Risque de laisser un utilisateur sans planète d'origine, ou de bloquer l'inscription si la génération échoue.

**Suggestion** : 
- Ajouter une gestion d'erreurs explicite dans le listener `GenerateHomePlanet`
- Logger l'erreur pour le debugging
- Ne pas bloquer l'inscription si la génération échoue (home_planet_id reste null)
- Prévoir un mécanisme de retry ou de génération manuelle pour les utilisateurs sans planète

**Priorité** : High

**Section concernée** : Tâche 3.2 (GenerateHomePlanet), Tâche 7.2 (Tests)

#### Recommandation 2 : Unicité du nom de planète

**Problème** : Le plan mentionne "nom unique ou gérer les collisions" mais ne détaille pas le mécanisme exact.

**Impact** : Risque de collision de noms de planètes, ce qui pourrait créer de la confusion.

**Suggestion** : 
- Prévoir un mécanisme de vérification d'unicité du nom avant la création
- En cas de collision, ajouter un suffixe unique (ex: "Kepler-452b-1") ou utiliser un UUID dans le nom
- Documenter le mécanisme choisi dans le code

**Priorité** : Medium

**Section concernée** : Tâche 2.2 (PlanetGeneratorService), Tâche 7.1 (Tests)

#### Recommandation 3 : Configuration des types de planètes

**Problème** : Le choix entre `config/planets.php` et `app/Data/PlanetTypes.php` n'est pas justifié.

**Impact** : Cohérence du projet et facilité de maintenance.

**Suggestion** : 
- Utiliser `config/planets.php` pour la configuration, plus standard dans Laravel
- Les fichiers de configuration Laravel sont plus adaptés pour ce type de données
- Facilite l'accès via `config('planets.types')`

**Priorité** : Low

**Section concernée** : Tâche 2.1 (Configuration des types)

#### Recommandation 4 : Autorisation sur les endpoints utilisateurs

**Problème** : Le plan ne mentionne pas explicitement la vérification qu'un utilisateur ne peut modifier que son propre profil.

**Impact** : Risque de sécurité si un utilisateur peut modifier le profil d'un autre utilisateur.

**Suggestion** : 
- Ajouter une vérification dans `UserController::update()` pour s'assurer que `auth()->id() === $user->id`
- Ou créer une Policy `UserPolicy` avec la méthode `update()` qui vérifie l'autorisation
- Documenter cette vérification dans la tâche 5.2

**Priorité** : High

**Section concernée** : Tâche 5.2 (UserController), Tâche 7.4 (Tests)

#### Recommandation 5 : Configuration Sanctum pour Livewire

**Problème** : La tâche 6.1 mentionne la configuration de Sanctum pour Livewire mais ne détaille pas suffisamment.

**Impact** : Risque de problèmes lors de l'implémentation si la configuration n'est pas claire.

**Suggestion** : 
- Détailer dans la tâche 6.1 comment Livewire consommera les APIs internes avec Sanctum
- Considérer l'utilisation de `Sanctum::actingAs()` dans les tests Livewire
- Vérifier que les requêtes internes Livewire peuvent utiliser les tokens Sanctum correctement

**Priorité** : Medium

**Section concernée** : Tâche 6.1 (Configuration Sanctum)

### Modifications Demandées

Aucune modification majeure demandée. Le plan peut être approuvé avec les recommandations ci-dessus. Les recommandations sont principalement des améliorations et des clarifications, pas des blocages.

### Questions & Clarifications

- **Question 1** : Le service `PlanetGeneratorService` sera-t-il réutilisé pour d'autres générations de planètes à l'avenir (planètes explorées, planètes générées dynamiquement) ?
  - **Impact** : Si oui, prévoir une interface ou une abstraction pour faciliter l'évolution.

- **Question 2** : Y a-t-il une limite au nombre de tentatives de génération de planète en cas d'erreur ?
  - **Impact** : Éviter les boucles infinies et définir un comportement clair.

- **Question 3** : Les planètes générées seront-elles stockées indéfiniment ou y a-t-il un mécanisme de nettoyage prévu pour les planètes non utilisées ?
  - **Impact** : Gestion de la base de données à long terme.

### Conclusion

Le plan technique est **approuvé avec recommandations**. Il respecte l'architecture définie, suit les bonnes pratiques Laravel, et est bien structuré pour l'implémentation. Les recommandations portent principalement sur :

1. **Sécurité** : Ajouter la vérification d'autorisation pour les endpoints utilisateurs (High)
2. **Robustesse** : Détailer la gestion d'erreurs lors de la génération de planète (High)
3. **Clarifications** : Préciser certains mécanismes (unicité des noms, configuration Sanctum) (Medium)

Le plan peut être implémenté tel quel, en tenant compte des recommandations prioritaires pour assurer la qualité et la sécurité du code.

**Prochaines étapes** :
1. Implémenter le plan en suivant les recommandations prioritaires
2. Ajouter la gestion d'erreurs robuste et les vérifications d'autorisation
3. Clarifier les mécanismes d'unicité et de configuration
4. Effectuer les tests additionnels recommandés

## Suivi et Historique

### Statut

🔄 En cours

### Historique

#### 2025-01-27 - Sam (Lead Developer) - Création du plan technique
**Statut** : À faire
**Détails** : Plan technique créé pour décomposer l'issue ISSUE-001 en tâches techniques exécutables. Le plan couvre 8 phases : Base de données et modèles, Service de génération, Architecture événementielle, API Authentification, API Utilisateurs/Planètes, Frontend Livewire, Tests, et Finalisation. Chaque phase est décomposée en tâches détaillées avec estimations, dépendances et tests.
**Notes** : Ce plan est prêt pour être implémenté par Jordan (Fullstack Developer). L'ordre d'exécution est défini et les dépendances sont clairement identifiées. Les tests sont prévus à chaque étape pour assurer la qualité du code.

#### 2025-01-27 - Morgan (Architect) - Review architecturale
**Statut** : ⚠️ Approuvé avec recommandations
**Détails** : Review architecturale complète du plan TASK-001. Le plan respecte l'architecture définie, suit les bonnes pratiques Laravel, et est bien structuré pour l'implémentation. L'approche API-first est correctement suivie, l'utilisation de l'architecture événementielle est excellente, et la structure des fichiers est cohérente. Les tests sont complets et bien planifiés.
**Notes** : Recommandations prioritaires : (1) Ajouter la vérification d'autorisation pour les endpoints utilisateurs (High), (2) Détailer la gestion d'erreurs lors de la génération de planète (High), (3) Clarifier les mécanismes d'unicité des noms et de configuration Sanctum (Medium). Le plan peut être implémenté en tenant compte de ces recommandations pour assurer la qualité et la sécurité du code.

#### 2025-01-27 - Sam (Lead Developer) - Vérification finale du plan
**Statut** : ✅ Approuvé - Prêt pour implémentation
**Détails** : Vérification finale complète du plan TASK-001 avant transmission à Jordan (Fullstack Developer) pour implémentation. Le plan a été analysé en profondeur sur tous les aspects critiques : complétude, cohérence technique, tests, documentation, et prise en compte des recommandations de Morgan.

**Points vérifiés et validés** :

1. **Complétude du plan** : ✅ Validé
   - Toutes les sections sont complètes et détaillées (8 phases, 33 tâches au total)
   - Chaque tâche contient : description, fichiers concernés, estimation, dépendances, tests
   - L'ordre d'exécution est clair et respecte les dépendances (Phase 1 → 8)
   - Les migrations, endpoints API, événements/listeners sont tous documentés

2. **Cohérence technique** : ✅ Validé
   - Migrations cohérentes avec les modèles : `users` (Tâche 1.1), `planets` (Tâche 1.2), `home_planet_id` (Tâche 1.3)
   - Modèles User et Planet avec relations correctement définies (Tâches 1.4, 1.5)
   - Endpoints API bien définis avec format de réponse standardisé (Phases 4 et 5)
   - Architecture événementielle correctement décrite : `UserRegistered` → `GenerateHomePlanet` (Phase 3)
   - Service `PlanetGeneratorService` bien structuré avec méthodes clairement définies (Phase 2)

3. **Tests** : ✅ Validé
   - Tests unitaires prévus pour `PlanetGeneratorService` et `GenerateHomePlanet` (Tâches 7.1, 7.2)
   - Tests d'intégration complets pour tous les endpoints API (Tâches 7.3, 7.4)
   - Tests fonctionnels pour tous les composants Livewire (Tâche 7.5)
   - Tests des modèles et relations inclus (Tâches 1.4, 1.5)
   - Les recommandations de Morgan concernant les tests additionnels sont documentées et doivent être prises en compte lors de l'implémentation

4. **Documentation** : ✅ Validé
   - Plan suffisamment détaillé pour Jordan avec toutes les informations nécessaires
   - Décisions techniques justifiées (API-first, architecture événementielle, Sanctum)
   - Références vers ARCHITECTURE.md, STACK.md, PROJECT_BRIEF.md
   - Mise à jour de la documentation prévue dans la Phase 8 (Tâche 8.2)

5. **Recommandations de Morgan** : ✅ Prises en compte
   - **Recommandation 1 (High)** : Gestion d'erreurs robuste - Documentée dans la section "Événements & Listeners" et dans les notes techniques. Jordan devra implémenter le try-catch dans `GenerateHomePlanet` avec logging et non-blocage de l'inscription.
   - **Recommandation 2 (Medium)** : Unicité du nom de planète - Mentionnée dans les notes techniques. Jordan devra implémenter un mécanisme de vérification d'unicité avec gestion des collisions (suffixe ou UUID).
   - **Recommandation 3 (Low)** : Configuration des types - Clarifiée : utiliser `config/planets.php` (plus standard Laravel).
   - **Recommandation 4 (High)** : Autorisation sur endpoints utilisateurs - Documentée dans la section "Sécurité" et dans les notes techniques. Jordan devra ajouter la vérification `auth()->id() === $user->id` dans `UserController::update()`.
   - **Recommandation 5 (Medium)** : Configuration Sanctum pour Livewire - Mentionnée dans la Tâche 6.1. Jordan devra détailler l'implémentation lors du développement.

**Points d'attention pour Jordan** :
- Implémenter les recommandations High priority de Morgan (gestion d'erreurs, autorisation)
- Ajouter les tests additionnels recommandés par Morgan (génération échouée, autorisation, collisions)
- Utiliser `config/planets.php` pour la configuration des types de planètes
- Détailer la configuration Sanctum pour Livewire lors de l'implémentation de la Tâche 6.1

**Conclusion** : Le plan est complet, cohérent, et prêt pour l'implémentation. Toutes les recommandations de Morgan sont documentées et doivent être prises en compte. Le plan peut être transmis à Jordan pour l'implémentation avec confiance.

#### 2025-01-27 - Jordan (Fullstack Developer) - Début de l'implémentation
**Statut** : 🔄 En cours
**Détails** : Début de l'implémentation du plan TASK-001. Mise à jour du statut à "En cours" et démarrage de la Phase 1 (Base de Données et Modèles).
**Notes** : Implémentation en cours selon l'ordre défini dans le plan. Prise en compte des recommandations prioritaires de Morgan (gestion d'erreurs, autorisation, unicité des noms).

#### 2025-11-09 - Jordan (Fullstack Developer) - Phase 1 terminée
**Statut** : 🔄 En cours
**Détails** : Phase 1 (Base de Données et Modèles) terminée. Toutes les migrations et modèles créés :
- Migration users (existante Laravel)
- Migration planets créée avec tous les champs
- Migration add_home_planet_id_to_users_table créée
- Modèle Planet créé avec relation users()
- Modèle User modifié avec relation homePlanet() et HasApiTokens
- Laravel Sanctum installé et configuré
**Fichiers créés/modifiés** :
- `database/migrations/2025_11_09_092648_create_planets_table.php`
- `database/migrations/2025_11_09_092654_add_home_planet_id_to_users_table.php`
- `app/Models/Planet.php`
- `app/Models/User.php` (modifié)
**Notes** : Les migrations n'ont pas encore été exécutées (Docker non démarré). Prêt pour Phase 2.

