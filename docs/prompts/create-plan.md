# Action: Create Plan

## Description

Cette action permet à l'agent Lead Developer de créer un plan de développement détaillé à partir d'une issue produit. Le plan décompose l'issue en tâches techniques exécutables pour l'équipe de développement.

## Quand Utiliser Cette Action

L'agent Lead Developer doit créer un plan quand :
- Une issue produit nécessite un plan de développement détaillé
- Une fonctionnalité doit être décomposée en tâches techniques
- Une implémentation complexe nécessite une structuration
- L'équipe a besoin d'un guide technique clair

## Format du Plan

Chaque plan doit être créé dans `docs/tasks/` avec le format suivant :

**Nom du fichier** : `TASK-{numero}-{titre-kebab-case}.md`

Le numéro correspond généralement au numéro de l'issue associée.

Exemple : Si l'issue est `ISSUE-001-implement-user-registration.md`, le plan sera `TASK-001-implement-user-registration.md`

## Structure du Plan

```markdown
# TASK-{numero} : {Titre du plan}

## Issue Associée

[Lien vers l'issue produit correspondante]

## Vue d'Ensemble

{Description technique de haut niveau de ce qui doit être développé}

## Suivi et Historique

### Statut

[À faire | En cours | En review | Approuvé | Terminé]

### Historique

#### [Date] - [Agent] - [Action]
**Statut** : [Nouveau statut]
**Détails** : [Description de ce qui a été fait]
**Fichiers modifiés** : [Si applicable]
**Notes** : [Notes additionnelles]
```

## Objectifs Techniques

- Objectif 1
- Objectif 2
- Objectif 3

## Architecture & Design

{Description de l'architecture et du design technique}

## Tâches de Développement

### Phase 1 : [Nom de la phase]

#### Tâche 1.1 : [Titre de la tâche]
- **Description** : Description détaillée de la tâche
- **Fichiers concernés** : Liste des fichiers à créer/modifier
- **Estimation** : [Temps estimé]
- **Dépendances** : [Tâches prérequises]
- **Tests** : [Tests à écrire]

#### Tâche 1.2 : [Titre de la tâche]
...

### Phase 2 : [Nom de la phase]
...

## Ordre d'Exécution

1. Tâche X (prérequis)
2. Tâche Y (dépend de X)
3. Tâche Z (dépend de Y)

## Migrations de Base de Données

- [ ] Migration 1 : Description
- [ ] Migration 2 : Description

## Endpoints API

### Nouveaux Endpoints

- `METHOD /api/endpoint` - Description
  - Request body : `{ ... }`
  - Response : `{ ... }`
  - Validation : [Règles de validation]

### Endpoints Modifiés

- `METHOD /api/endpoint` - Modifications apportées

## Événements & Listeners

### Nouveaux Événements

- `EventName` : Description
  - Déclenché quand : [Condition]
  - Listeners : [Liste des listeners]

### Nouveaux Listeners

- `ListenerName` : Description
  - Écoute : `EventName`
  - Action : [Ce que fait le listener]

## Services & Classes

### Nouveaux Services

- `ServiceName` : Description
  - Méthodes : [Liste des méthodes principales]

### Classes Modifiées

- `ClassName` : Modifications apportées

## Tests

### Tests Unitaires

- [ ] Test 1 : Description
- [ ] Test 2 : Description

### Tests d'Intégration

- [ ] Test 1 : Description
- [ ] Test 2 : Description

### Tests Fonctionnels

- [ ] Test 1 : Description

## Documentation

- [ ] Mettre à jour ARCHITECTURE.md si nécessaire
- [ ] Mettre à jour la documentation API
- [ ] Ajouter des commentaires dans le code

## Notes Techniques

{Notes additionnelles, considérations techniques, pièges à éviter, etc.}

## Références

- [Lien vers l'issue produit]
- [Lien vers documentation architecture]
- [Lien vers documentation stack]
```

## Exemple de Plan

```markdown
# TASK-001 : Implémenter l'inscription utilisateur avec génération de planète

## Issue Associée

[ISSUE-001-implement-user-registration.md](../issues/ISSUE-001-implement-user-registration.md)

## Vue d'Ensemble

Implémenter le système d'inscription utilisateur avec génération automatique d'une planète d'origine. L'inscription doit créer un compte utilisateur, générer une planète via un événement, et retourner un token Sanctum pour l'authentification.

## Objectifs Techniques

- Créer l'endpoint API d'inscription
- Implémenter l'événement `UserRegistered`
- Créer le listener `GenerateHomePlanet`
- Développer le service `PlanetGeneratorService`
- Gérer l'authentification Sanctum

## Architecture & Design

- **Endpoint** : `POST /api/auth/register` dans `AuthController`
- **Événement** : `UserRegistered` dans `app/Events/`
- **Listener** : `GenerateHomePlanet` dans `app/Listeners/`
- **Service** : `PlanetGeneratorService` dans `app/Services/`
- **Modèle** : Utiliser le modèle `User` existant et créer le modèle `Planet`

## Tâches de Développement

### Phase 1 : Modèles et Migrations

#### Tâche 1.1 : Créer la migration pour la table planets
- **Description** : Créer la migration avec tous les champs nécessaires (name, type, size, temperature, atmosphere, terrain, resources, description)
- **Fichiers concernés** : `database/migrations/YYYY_MM_DD_create_planets_table.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Vérifier la structure de la table

#### Tâche 1.2 : Créer le modèle Planet
- **Description** : Créer le modèle Eloquent Planet avec les relations et attributs
- **Fichiers concernés** : `app/Models/Planet.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 1.1
- **Tests** : Tests unitaires du modèle

#### Tâche 1.3 : Ajouter la colonne home_planet_id à la table users
- **Description** : Migration pour ajouter la foreign key home_planet_id
- **Fichiers concernés** : `database/migrations/YYYY_MM_DD_add_home_planet_id_to_users_table.php`
- **Estimation** : 20 min
- **Dépendances** : Tâche 1.1
- **Tests** : Vérifier la relation

### Phase 2 : Service de Génération

#### Tâche 2.1 : Créer PlanetGeneratorService
- **Description** : Service pour générer des planètes avec le système de poids
- **Fichiers concernés** : `app/Services/PlanetGeneratorService.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 1.2
- **Tests** : Tests unitaires du service

#### Tâche 2.2 : Créer la configuration des types de planètes
- **Description** : Fichier de config ou classe pour les types et poids
- **Fichiers concernés** : `config/planets.php` ou `app/Data/PlanetTypes.php`
- **Estimation** : 1h
- **Dépendances** : Aucune
- **Tests** : Tests de la configuration

### Phase 3 : Événements & Listeners

#### Tâche 3.1 : Créer l'événement UserRegistered
- **Description** : Événement dispatché lors de l'inscription
- **Fichiers concernés** : `app/Events/UserRegistered.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Tests de l'événement

#### Tâche 3.2 : Créer le listener GenerateHomePlanet
- **Description** : Listener qui génère la planète et l'assigne au joueur
- **Fichiers concernés** : `app/Listeners/GenerateHomePlanet.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 2.1, Tâche 3.1
- **Tests** : Tests du listener

### Phase 4 : API Endpoint

#### Tâche 4.1 : Créer le FormRequest pour l'inscription
- **Description** : Validation des données d'inscription
- **Fichiers concernés** : `app/Http/Requests/RegisterRequest.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Tests de validation

#### Tâche 4.2 : Créer l'endpoint POST /api/auth/register
- **Description** : Endpoint d'inscription dans AuthController
- **Fichiers concernés** : `app/Http/Controllers/Api/AuthController.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 3.1, Tâche 4.1
- **Tests** : Tests d'intégration de l'endpoint

#### Tâche 4.3 : Ajouter la route API
- **Description** : Ajouter la route dans routes/api.php
- **Fichiers concernés** : `routes/api.php`
- **Estimation** : 10 min
- **Dépendances** : Tâche 4.2
- **Tests** : Vérifier la route

## Ordre d'Exécution

1. Phase 1 : Modèles et Migrations (Tâches 1.1, 1.2, 1.3)
2. Phase 2 : Service de Génération (Tâches 2.1, 2.2)
3. Phase 3 : Événements & Listeners (Tâches 3.1, 3.2)
4. Phase 4 : API Endpoint (Tâches 4.1, 4.2, 4.3)

## Migrations de Base de Données

- [x] Migration : Créer la table planets
- [x] Migration : Ajouter home_planet_id à users

## Endpoints API

### Nouveaux Endpoints

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
        "user": {...},
        "token": "string"
      },
      "message": "User registered successfully",
      "status": "success"
    }
    ```
  - Validation : name (required|string|max:255), email (required|email|unique:users), password (required|min:8|confirmed)

## Événements & Listeners

### Nouveaux Événements

- `UserRegistered` : Déclenché lors de la création d'un utilisateur
  - Déclenché quand : Un nouvel utilisateur est créé
  - Listeners : `GenerateHomePlanet`

### Nouveaux Listeners

- `GenerateHomePlanet` : Génère une planète d'origine et l'assigne au joueur
  - Écoute : `UserRegistered`
  - Action : Appelle PlanetGeneratorService, crée la planète, assigne home_planet_id

## Services & Classes

### Nouveaux Services

- `PlanetGeneratorService` : Service de génération procédurale de planètes
  - Méthodes : 
    - `generate()` : Génère une planète aléatoire
    - `selectPlanetType()` : Sélectionne un type selon les poids
    - `generateCharacteristics()` : Génère les caractéristiques selon le type

## Tests

### Tests Unitaires

- [ ] Test : PlanetGeneratorService génère une planète valide
- [ ] Test : PlanetGeneratorService respecte les poids de probabilité
- [ ] Test : GenerateHomePlanet assigne correctement la planète

### Tests d'Intégration

- [ ] Test : POST /api/auth/register crée un utilisateur et une planète
- [ ] Test : L'événement UserRegistered est bien dispatché
- [ ] Test : Le token Sanctum est retourné correctement

### Tests Fonctionnels

- [ ] Test : Inscription complète avec génération de planète
- [ ] Test : Validation des données d'inscription

## Documentation

- [ ] Mettre à jour ARCHITECTURE.md avec les nouveaux endpoints
- [ ] Documenter PlanetGeneratorService
- [ ] Ajouter des commentaires dans le code

## Notes Techniques

- Utiliser Laravel Sanctum pour l'authentification
- La génération de planète doit être synchrone pour l'instant (peut être async plus tard)
- Gérer les erreurs de génération de planète élégamment
- Le nom de la planète doit être unique ou gérer les collisions

## Références

- [ISSUE-001-implement-user-registration.md](../issues/ISSUE-001-implement-user-registration.md)
- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Flux d'inscription et architecture événementielle
- [STACK.md](../memory_bank/STACK.md) - Stack technique
```

## Instructions pour l'Agent Lead Developer

Quand tu crées un plan :

1. **Lire l'issue** : Analyse complète de l'issue produit associée
2. **Comprendre le contexte** : Identifier les besoins métier et techniques
3. **Décomposer** : Diviser le travail en phases et tâches logiques
4. **Détailler** : Chaque tâche doit être claire et actionnable
5. **Ordre** : Organiser les tâches dans un ordre logique avec dépendances
6. **Estimer** : Fournir des estimations réalistes pour chaque tâche
7. **Tests** : Prévoir les tests à chaque étape
8. **Documentation** : Prévoir la mise à jour de la documentation
9. **Références** : Lier vers l'architecture et la documentation pertinente
10. **Ajouter le suivi** : Créer la section "Suivi et Historique" dans le plan
11. **Mettre à jour l'issue** : Ajouter une entrée dans l'historique de l'issue

### Mise à Jour des Documents

Après avoir créé le plan :
- **Dans le plan (TASK-XXX)** : Ajouter une section "Suivi et Historique" avec statut "À faire" et une première entrée
- **Dans l'issue (ISSUE-XXX)** : Mettre à jour le statut à "En cours" et ajouter une entrée dans l'historique

Voir [update-tracking.md](./update-tracking.md) pour le format exact.

## Organisation

Les plans sont organisés dans `docs/tasks/` et peuvent être :
- Utilisés par l'équipe de développement pour implémenter les fonctionnalités
- Référencés dans les PRs et commits
- Utilisés pour suivre la progression du développement
- Mis à jour au fur et à mesure de l'avancement

