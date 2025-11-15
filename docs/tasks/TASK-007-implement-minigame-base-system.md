# TASK-007 : Implémenter le système de base des mini-jeux

## Issue Associée

[ISSUE-007-implement-minigame-base-system.md](../issues/ISSUE-007-implement-minigame-base-system.md)

## Vue d'Ensemble

Implémenter un système de base extensible pour les mini-jeux qui permettra d'ajouter facilement de nouveaux types de mini-jeux depuis l'interface admin. Le système doit être sécurisé (validation serveur, protection anti-hack) et permettre la configuration complète des mini-jeux (difficulté, récompenses, paramètres) depuis l'admin.

**MVP** : Système de base avec un seul mini-jeu fonctionnel (Scanning/Scan Circulaire) pour valider l'architecture. Les autres types de mini-jeux pourront être ajoutés ultérieurement via l'admin.

**Architecture** :
- Composant Livewire parent générique qui gère la sélection, la validation et les récompenses
- Composants enfants par type de mini-jeu (un par type)
- Configuration stockée en base de données (sécurité)
- Interface admin pour gérer les types et configurations
- Page de test accessible en admin pour tester les mini-jeux

## Suivi et Historique

### Statut

À faire

### Historique

#### 2025-01-XX - Sam (Lead Dev) - Création du plan
**Statut** : À faire
**Détails** : Plan de développement créé pour implémenter le système de base des mini-jeux avec un mini-jeu fonctionnel (Scanning) pour valider l'architecture.

## Objectifs Techniques

- Créer le modèle de données pour les types de mini-jeux et les tentatives
- Implémenter le service métier `MiniGameService` pour gérer la logique des mini-jeux
- Créer le validateur `ScanningMinigameValidator` pour valider les scores côté serveur
- Développer le composant Livewire parent `Minigame` pour gérer la sélection et la validation
- Implémenter le composant Livewire enfant `ScanningMinigame` avec la mécanique du scan circulaire
- Créer l'interface admin pour gérer les types de mini-jeux
- Ajouter la colonne `scientific_data` à la table `users` pour stocker les récompenses
- Sécuriser le système contre les tentatives de hack (validation serveur stricte)

## Architecture & Design

**Modèle de données** :
- `mini_game_types` : Types de mini-jeux avec configuration JSON
- `mini_game_attempts` : Tentatives de jeu avec scores validés et récompenses
- `users.scientific_data` : Colonne pour stocker les données scientifiques gagnées

**Services** :
- `MiniGameService` : Logique métier principale (limitation quotidienne, validation, récompenses)
- `ScanningMinigameValidator` : Validation spécifique du mini-jeu Scanning

**Composants Livewire** :
- `Minigame` (parent) : Gère la sélection, la validation et l'affichage des résultats
- `ScanningMinigame` (enfant) : Implémente la mécanique du scan circulaire

**Admin** :
- `MiniGameTypeController` : CRUD pour les types de mini-jeux
- Vues admin pour la gestion et le test des mini-jeux

## Tâches de Développement

### Phase 1 : Modèle de données et Migrations

#### Tâche 1.1 : Créer la migration pour la table mini_game_types
- **Description** : Créer la migration avec tous les champs nécessaires (id ULID, code unique, name, description, component_class, enabled, config JSON)
- **Fichiers concernés** : `database/migrations/YYYY_MM_DD_HHMMSS_create_mini_game_types_table.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Vérifier la structure de la table et les contraintes

#### Tâche 1.2 : Créer la migration pour la table mini_game_attempts
- **Description** : Créer la migration avec tous les champs nécessaires (id ULID, user_id, mini_game_type_id, score, success, data JSON, rewards JSON, duration_ms, played_at, timestamps, index)
- **Fichiers concernés** : `database/migrations/YYYY_MM_DD_HHMMSS_create_mini_game_attempts_table.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 1.1
- **Tests** : Vérifier la structure de la table, les foreign keys et les index

#### Tâche 1.3 : Créer la migration pour ajouter scientific_data à users
- **Description** : Migration pour ajouter la colonne `scientific_data` (integer, default: 0) à la table users si elle n'existe pas déjà
- **Fichiers concernés** : `database/migrations/YYYY_MM_DD_HHMMSS_add_scientific_data_to_users_table.php`
- **Estimation** : 15 min
- **Dépendances** : Aucune
- **Tests** : Vérifier que la colonne est ajoutée correctement

#### Tâche 1.4 : Créer le modèle MiniGameType
- **Description** : Créer le modèle Eloquent avec relations, casts (config JSON, enabled boolean) et méthodes utilitaires
- **Fichiers concernés** : `app/Models/MiniGameType.php`
- **Estimation** : 45 min
- **Dépendances** : Tâche 1.1
- **Tests** : Tests unitaires du modèle (relations, casts, méthodes)

#### Tâche 1.5 : Créer le modèle MiniGameAttempt
- **Description** : Créer le modèle Eloquent avec relations vers User et MiniGameType, casts (data JSON, rewards JSON, success boolean) et méthodes utilitaires
- **Fichiers concernés** : `app/Models/MiniGameAttempt.php`
- **Estimation** : 45 min
- **Dépendances** : Tâche 1.2, Tâche 1.4
- **Tests** : Tests unitaires du modèle (relations, casts, méthodes)

### Phase 2 : Services métier

#### Tâche 2.1 : Créer MiniGameService
- **Description** : Service principal pour gérer la logique des mini-jeux
  - `getAvailableMinigame(User $user): ?MiniGameType` - Récupère le mini-jeu disponible pour aujourd'hui
  - `canPlayToday(User $user, MiniGameType $type): bool` - Vérifie la limitation quotidienne
  - `validateScore(array $gameData, MiniGameType $type): int` - Valide et calcule le score côté serveur
  - `calculateRewards(int $score, MiniGameType $type): array` - Calcule les récompenses selon le score
  - `recordAttempt(User $user, MiniGameType $type, int $score, array $gameData, array $rewards): MiniGameAttempt` - Enregistre la tentative
  - `grantRewards(User $user, array $rewards): void` - Attribue les récompenses au joueur
- **Fichiers concernés** : `app/Services/MiniGameService.php`
- **Estimation** : 3h
- **Dépendances** : Tâche 1.4, Tâche 1.5
- **Tests** : Tests unitaires pour chaque méthode

#### Tâche 2.2 : Créer ScanningMinigameValidator
- **Description** : Validateur spécifique pour le mini-jeu Scanning
  - `validate(array $gameData): int` - Valide les actions et calcule le score 0-100
  - Vérifie les timestamps, la précision des clics, le nombre de signaux verrouillés
  - Détecte les manipulations (timestamps invalides, actions impossibles)
- **Fichiers concernés** : `app/Services/Minigames/ScanningMinigameValidator.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 2.1
- **Tests** : Tests unitaires pour la validation et la détection de manipulations

### Phase 3 : Composants Livewire Frontend

#### Tâche 3.1 : Créer le composant parent Minigame
- **Description** : Composant Livewire parent qui gère la sélection, la validation et les récompenses
  - `mount()` - Charge le mini-jeu disponible pour aujourd'hui
  - `play()` - Démarre le mini-jeu
  - `submitScore(array $gameData)` - Soumet le score et valide côté serveur
  - `getAvailableMinigameProperty()` - Propriété calculée pour le mini-jeu disponible
- **Fichiers concernés** : `app/Livewire/Minigame.php`, `resources/views/livewire/minigame.blade.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 2.1
- **Tests** : Tests fonctionnels du composant

#### Tâche 3.2 : Créer le composant enfant ScanningMinigame
- **Description** : Composant Livewire enfant qui implémente la mécanique du scan circulaire
  - Reçoit la config depuis le parent (difficulté, durée, nombre de signaux)
  - Gère l'affichage du radar avec signaux éphémères
  - Enregistre les actions (clics avec timestamps) pour validation serveur
  - Calcule un score préliminaire côté client (affichage)
  - Appelle le parent pour soumettre le score final
- **Fichiers concernés** : `app/Livewire/Minigames/ScanningMinigame.php`, `resources/views/livewire/minigames/scanning-minigame.blade.php`
- **Estimation** : 4h
- **Dépendances** : Tâche 3.1
- **Tests** : Tests fonctionnels de la mécanique de jeu

#### Tâche 3.3 : Ajouter la route et le lien vers le mini-jeu
- **Description** : Ajouter la route `/minigame` (middleware `auth`) et un lien/bouton "Mini-jeu du jour" sur le dashboard
- **Fichiers concernés** : `routes/web.php`, `resources/views/livewire/dashboard.blade.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 3.1
- **Tests** : Vérifier la route et l'affichage du lien

### Phase 4 : Interface Admin

#### Tâche 4.1 : Créer MiniGameTypeController
- **Description** : Contrôleur admin pour gérer les types de mini-jeux
  - `index()` - Liste des types
  - `create()` - Formulaire de création
  - `store()` - Création d'un nouveau type
  - `show(MiniGameType $miniGameType)` - Détails d'un type
  - `edit(MiniGameType $miniGameType)` - Formulaire d'édition
  - `update(MiniGameType $miniGameType)` - Mise à jour
  - `destroy(MiniGameType $miniGameType)` - Suppression
  - `test(MiniGameType $miniGameType)` - Page de test
- **Fichiers concernés** : `app/Http/Controllers/Admin/MiniGameTypeController.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 1.4
- **Tests** : Tests fonctionnels du contrôleur

#### Tâche 4.2 : Créer les vues admin
- **Description** : Créer les vues Blade pour l'interface admin
  - `index.blade.php` - Liste des types
  - `create.blade.php` - Formulaire de création
  - `show.blade.php` - Détails et configuration
  - `edit.blade.php` - Formulaire d'édition
  - `test.blade.php` - Page de test du mini-jeu
- **Fichiers concernés** : `resources/views/admin/minigames/index.blade.php`, `create.blade.php`, `show.blade.php`, `edit.blade.php`, `test.blade.php`
- **Estimation** : 3h
- **Dépendances** : Tâche 4.1
- **Tests** : Vérifier l'affichage et la fonctionnalité des formulaires

#### Tâche 4.3 : Ajouter les routes admin
- **Description** : Ajouter les routes admin dans `routes/admin.php`
  - `Route::resource('minigames', MiniGameTypeController::class)`
  - `Route::get('/minigames/{miniGameType}/test', [MiniGameTypeController::class, 'test'])->name('minigames.test')`
- **Fichiers concernés** : `routes/admin.php`
- **Estimation** : 15 min
- **Dépendances** : Tâche 4.1
- **Tests** : Vérifier les routes

#### Tâche 4.4 : Créer le seeder pour le mini-jeu Scanning
- **Description** : Créer un seeder pour insérer le mini-jeu Scanning par défaut avec sa configuration
- **Fichiers concernés** : `database/seeders/MiniGameTypeSeeder.php` ou ajout dans `DatabaseSeeder.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 1.4
- **Tests** : Vérifier que le seeder fonctionne correctement

### Phase 5 : Sécurité et Rate Limiting

#### Tâche 5.1 : Ajouter le rate limiting sur les endpoints
- **Description** : Ajouter le rate limiting sur les routes de soumission de score pour éviter le spam
- **Fichiers concernés** : `routes/web.php` (middleware `throttle`)
- **Estimation** : 15 min
- **Dépendances** : Tâche 3.1
- **Tests** : Vérifier que le rate limiting fonctionne

### Phase 6 : Mise à jour du modèle User

#### Tâche 6.1 : Ajouter scientific_data au modèle User
- **Description** : Ajouter `scientific_data` au fillable et créer une méthode helper pour incrémenter les données scientifiques
- **Fichiers concernés** : `app/Models/User.php`
- **Estimation** : 15 min
- **Dépendances** : Tâche 1.3
- **Tests** : Tests unitaires de la méthode helper

## Ordre d'Exécution

1. Phase 1 : Modèle de données et Migrations (Tâches 1.1, 1.2, 1.3, 1.4, 1.5)
2. Phase 2 : Services métier (Tâches 2.1, 2.2)
3. Phase 3 : Composants Livewire Frontend (Tâches 3.1, 3.2, 3.3)
4. Phase 4 : Interface Admin (Tâches 4.1, 4.2, 4.3, 4.4)
5. Phase 5 : Sécurité et Rate Limiting (Tâche 5.1)
6. Phase 6 : Mise à jour du modèle User (Tâche 6.1)

## Migrations de Base de Données

- [ ] Migration : Créer la table mini_game_types
- [ ] Migration : Créer la table mini_game_attempts
- [ ] Migration : Ajouter scientific_data à users

## Endpoints API

Aucun endpoint API n'est nécessaire pour cette fonctionnalité. Toute la logique est gérée via Livewire et les services Laravel directement.

## Routes Web

### Nouvelles Routes

- `GET /minigame` - Page du mini-jeu (composant Livewire `Minigame`, middleware `auth`)

### Routes Admin

- `GET /admin/minigames` - Liste des types
- `GET /admin/minigames/create` - Création
- `POST /admin/minigames` - Stockage
- `GET /admin/minigames/{id}` - Détails
- `GET /admin/minigames/{id}/edit` - Édition
- `PUT /admin/minigames/{id}` - Mise à jour
- `DELETE /admin/minigames/{id}` - Suppression
- `GET /admin/minigames/{id}/test` - Page de test

## Événements & Listeners

Aucun événement spécifique n'est prévu pour le MVP. Les événements pourront être ajoutés ultérieurement si nécessaire (ex: `MinigamePlayed`, `MinigameRewardGranted`).

## Services & Classes

### Nouveaux Services

- `MiniGameService` : Service principal pour gérer la logique des mini-jeux
  - Méthodes : `getAvailableMinigame()`, `canPlayToday()`, `validateScore()`, `calculateRewards()`, `recordAttempt()`, `grantRewards()`

- `ScanningMinigameValidator` : Validateur spécifique pour le mini-jeu Scanning
  - Méthodes : `validate()`

### Nouveaux Modèles

- `MiniGameType` : Modèle pour les types de mini-jeux
- `MiniGameAttempt` : Modèle pour les tentatives de jeu

### Classes Modifiées

- `User` : Ajout de `scientific_data` et méthode helper pour incrémenter

## Tests

### Tests Unitaires

- [ ] Test : MiniGameType modèle (relations, casts, méthodes)
- [ ] Test : MiniGameAttempt modèle (relations, casts, méthodes)
- [ ] Test : MiniGameService::getAvailableMinigame() retourne le bon mini-jeu
- [ ] Test : MiniGameService::canPlayToday() vérifie la limitation quotidienne
- [ ] Test : MiniGameService::validateScore() valide correctement les scores
- [ ] Test : MiniGameService::calculateRewards() calcule les récompenses selon les paliers
- [ ] Test : MiniGameService::recordAttempt() enregistre correctement les tentatives
- [ ] Test : MiniGameService::grantRewards() attribue les récompenses au joueur
- [ ] Test : ScanningMinigameValidator::validate() valide les actions correctement
- [ ] Test : ScanningMinigameValidator détecte les manipulations (timestamps invalides)
- [ ] Test : User::incrementScientificData() incrémente correctement les données

### Tests d'Intégration

- [ ] Test : Jouer un mini-jeu → Score validé → Récompenses attribuées → Limitation quotidienne activée
- [ ] Test : Tentative de jouer deux fois le même jour → Bloqué
- [ ] Test : Reset de la limitation quotidienne à minuit (vérification à la volée)

### Tests Fonctionnels

- [ ] Test : Composant Minigame affiche le mini-jeu disponible
- [ ] Test : Composant Minigame soumet le score et affiche les résultats
- [ ] Test : Composant ScanningMinigame affiche le radar avec signaux
- [ ] Test : Composant ScanningMinigame enregistre les actions correctement
- [ ] Test : Interface admin permet de créer/modifier/supprimer des types
- [ ] Test : Page de test admin permet de tester le mini-jeu

## Documentation

- [ ] Mettre à jour ARCHITECTURE.md avec les nouveaux modèles et services
- [ ] Documenter MiniGameService et ScanningMinigameValidator
- [ ] Ajouter des commentaires dans le code
- [ ] Documenter la structure JSON de la configuration des mini-jeux

## Notes Techniques

### Structure JSON de Configuration

**`mini_game_types.config`** :
```json
{
  "scanning": {
    "signal_count": 8,
    "signal_duration_min": 1000,
    "signal_duration_max": 3000,
    "total_duration": 60000,
    "score_thresholds": {
      "failure": {"min": 0, "max": 25, "reward": {"type": "scientific_data", "amount": 0}},
      "minimal": {"min": 25, "max": 60, "reward": {"type": "scientific_data", "amount": 50}},
      "good": {"min": 60, "max": 85, "reward": {"type": "scientific_data", "amount": 100}},
      "excellent": {"min": 85, "max": 100, "reward": {"type": "scientific_data", "amount": 150}}
    }
  }
}
```

**`mini_game_attempts.data`** :
```json
{
  "actions": [
    {"signal_id": 1, "clicked_at": 1234567890, "position": {"x": 100, "y": 150}, "success": true},
    {"signal_id": 2, "clicked_at": 1234567891, "position": {"x": 200, "y": 250}, "success": false}
  ],
  "signals_generated": 8,
  "signals_locked": 6
}
```

**`mini_game_attempts.rewards`** :
```json
{
  "scientific_data": 100
}
```

### Sélection du Mini-jeu du Jour

Pour le MVP : Sélectionner le premier mini-jeu actif disponible. Plus tard, on pourra implémenter un algorithme de sélection aléatoire pondéré selon la progression.

### Limitation Quotidienne

Vérification à la volée : `MiniGameAttempt::where('user_id', $user->id)->whereDate('played_at', today())->exists()`
Reset automatique à minuit (pas besoin de job, vérification à la volée).

### Sécurité

- Toute la validation et le calcul des récompenses sont côté serveur
- Les actions (clics, timestamps) sont vérifiées pour détecter les manipulations
- La config de difficulté/récompenses reste côté serveur (pas exposée au client)
- Rate limiting sur les endpoints de soumission de score

### Extensibilité

Le système est conçu pour faciliter l'ajout de nouveaux types de mini-jeux sans modifier le code existant :
- Nouveau type ajouté via l'admin avec sa configuration JSON
- Nouveau composant Livewire enfant créé dans `app/Livewire/Minigames/`
- Le composant parent charge dynamiquement le composant enfant selon `component_class`

## Références

- [ISSUE-007-implement-minigame-base-system.md](../issues/ISSUE-007-implement-minigame-base-system.md)
- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Architecture technique générale
- [STACK.md](../memory_bank/STACK.md) - Stack technique
- [PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md) - Vision métier

