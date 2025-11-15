# ISSUE-007 : Implémenter le système de base des mini-jeux

## Type
Feature

## Priorité
Medium

## Description

Implémenter un système de base extensible pour les mini-jeux qui permettra d'ajouter facilement de nouveaux types de mini-jeux depuis l'interface admin. Le système doit être sécurisé (validation serveur, protection anti-hack) et permettre la configuration complète des mini-jeux (difficulté, récompenses, paramètres) depuis l'admin.

**MVP** : Système de base avec un seul mini-jeu fonctionnel (Scanning/Scan Circulaire) pour valider l'architecture. Les autres types de mini-jeux pourront être ajoutés ultérieurement via l'admin.

**Architecture** : 
- Composant Livewire parent générique qui gère la sélection, la validation et les récompenses
- Composants enfants par type de mini-jeu (un par type)
- Configuration stockée en base de données (sécurité)
- Interface admin pour gérer les types et configurations
- Page de test accessible en admin pour tester les mini-jeux

## Contexte Métier

Les mini-jeux sont des interactions courtes (10-90 secondes) qui ajoutent de l'interactivité à la boucle quotidienne de Stellar. Ils permettent :
- D'ajouter un moment d'interactivité dans la session quotidienne
- De donner un sentiment de progression rapide
- D'offrir des récompenses variées (données scientifiques pour le MVP)
- D'introduire de la tension et du risque léger
- De rester suffisamment simples pour ne jamais devenir chronophages

**Approche itérative** : On commence par créer l'infrastructure (système de base) avec un seul mini-jeu pour valider l'architecture. Ensuite, on pourra facilement ajouter d'autres types de mini-jeux depuis l'admin sans toucher au code.

**Sécurité** : Le système doit être sécurisé contre les tentatives de hack via le réseau. Toute la logique de validation et de calcul des récompenses doit être côté serveur.

## Critères d'Acceptation

### Backend - Modèle de données

- [ ] Créer la migration pour la table `mini_game_types`
  - `id` (ULID)
  - `code` (string, unique) - ex: "scanning", "signal_decrypt", etc.
  - `name` (string) - Nom affiché
  - `description` (text, nullable) - Description du mini-jeu
  - `component_class` (string) - Classe du composant Livewire enfant (ex: "ScanningMinigame")
  - `enabled` (boolean, default: true) - Actif/inactif
  - `config` (JSON) - Configuration par défaut (difficulté, durée, paramètres spécifiques)
  - `created_at`, `updated_at`

- [ ] Créer la migration pour la table `mini_game_attempts`
  - `id` (ULID)
  - `user_id` (ULID, foreign key → users.id)
  - `mini_game_type_id` (ULID, foreign key → mini_game_types.id)
  - `score` (integer, 0-100) - Score final validé côté serveur
  - `success` (boolean) - Réussite/échec selon le score
  - `data` (JSON) - Données de la partie (actions, timestamps, etc.) pour audit
  - `rewards` (JSON) - Récompenses attribuées (type, quantité)
  - `duration_ms` (integer) - Durée de la partie en millisecondes
  - `played_at` (timestamp) - Date/heure de la partie (pour limitation quotidienne)
  - `created_at`, `updated_at`
  - Index sur `user_id`, `mini_game_type_id`, `played_at`

- [ ] Créer la migration pour ajouter `scientific_data` (integer, default: 0) à la table `users` si elle n'existe pas déjà
  - Stockage des données scientifiques gagnées via les mini-jeux

- [ ] Créer les modèles Eloquent :
  - `MiniGameType` avec relations et méthodes utilitaires
  - `MiniGameAttempt` avec relations vers User et MiniGameType

### Backend - Services et logique métier

- [ ] Créer `MiniGameService` dans `app/Services/`
  - `getAvailableMinigame(User $user): ?MiniGameType` - Récupère le mini-jeu disponible pour aujourd'hui (vérifie limitation quotidienne)
  - `canPlayToday(User $user, MiniGameType $type): bool` - Vérifie si le joueur peut jouer aujourd'hui
  - `validateScore(array $gameData, MiniGameType $type): int` - Valide et calcule le score côté serveur
  - `calculateRewards(int $score, MiniGameType $type): array` - Calcule les récompenses selon le score et la config
  - `recordAttempt(User $user, MiniGameType $type, int $score, array $gameData, array $rewards): MiniGameAttempt` - Enregistre la tentative
  - `grantRewards(User $user, array $rewards): void` - Attribue les récompenses au joueur

- [ ] Créer `ScanningMinigameValidator` dans `app/Services/Minigames/`
  - `validate(array $gameData): int` - Valide les actions du scanning et calcule le score
  - Vérifie les timestamps, la précision des clics, le nombre de signaux verrouillés
  - Retourne un score 0-100 validé

### Frontend - Composants Livewire

- [ ] Créer le composant parent `Minigame` dans `app/Livewire/`
  - Route : `/minigame` (middleware `auth`)
  - Méthodes :
    - `mount()` - Charge le mini-jeu disponible pour aujourd'hui
    - `play()` - Démarre le mini-jeu
    - `submitScore(array $gameData)` - Soumet le score et valide côté serveur
    - `getAvailableMinigameProperty()` - Propriété calculée pour le mini-jeu disponible
  - Vue : `resources/views/livewire/minigame.blade.php`
    - Affiche le composant enfant dynamiquement selon le type
    - Gère l'affichage des résultats et récompenses

- [ ] Créer le composant enfant `ScanningMinigame` dans `app/Livewire/Minigames/`
  - Implémente la mécanique du scan circulaire
  - Reçoit la config depuis le parent (difficulté, durée, nombre de signaux)
  - Gère l'affichage du radar avec signaux éphémères
  - Enregistre les actions (clics avec timestamps) pour validation serveur
  - Calcule un score préliminaire côté client (affichage)
  - Appelle le parent pour soumettre le score final
  - Vue : `resources/views/livewire/minigames/scanning-minigame.blade.php`

- [ ] Ajouter un lien/bouton "Mini-jeu du jour" sur le dashboard pour accéder au mini-jeu

### Admin - Interface de gestion

- [ ] Créer `MiniGameTypeController` dans `app/Http/Controllers/Admin/`
  - `index()` - Liste des types de mini-jeux
  - `create()` - Formulaire de création
  - `store()` - Création d'un nouveau type
  - `show(MiniGameType $miniGameType)` - Détails d'un type
  - `edit(MiniGameType $miniGameType)` - Formulaire d'édition
  - `update(MiniGameType $miniGameType)` - Mise à jour
  - `destroy(MiniGameType $miniGameType)` - Suppression (soft delete si possible)

- [ ] Créer les vues admin dans `resources/views/admin/minigames/`
  - `index.blade.php` - Liste des types
  - `create.blade.php` - Formulaire de création
  - `show.blade.php` - Détails et configuration
  - `edit.blade.php` - Formulaire d'édition

- [ ] Ajouter les routes admin dans `routes/admin.php`
  - `Route::resource('minigames', MiniGameTypeController::class)`
  - `Route::get('/minigames/{miniGameType}/test', [MiniGameTypeController::class, 'test'])->name('minigames.test')`

- [ ] Créer la page de test dans `resources/views/admin/minigames/test.blade.php`
  - Permet de tester le mini-jeu avec la configuration actuelle
  - Utilise le même composant Livewire que le frontend mais en mode admin/test

- [ ] Interface de configuration dans les formulaires admin :
  - Configuration des récompenses par palier de score (ex: 0-25 = échec, 25-60 = succès minimal, etc.)
  - Paramètres de difficulté (vitesse, nombre de signaux, durée)
  - Activation/désactivation du type

### Mini-jeu Scanning - Implémentation

- [ ] Implémenter la mécanique du scan circulaire :
  - Radar circulaire affiché à l'écran
  - Signaux apparaissent et disparaissent rapidement (durée configurable)
  - Le joueur doit cliquer quand le signal est dans la zone optimale
  - Nombre de signaux configurable (5-10 par défaut)
  - Score basé sur la précision et le nombre de signaux verrouillés
  - Feedback visuel pour chaque clic (succès/échec)

- [ ] Configuration par défaut pour le scanning :
  - Nombre de signaux : 8
  - Durée d'affichage signal : 1-3 secondes (aléatoire)
  - Durée totale : 30-60 secondes
  - Zones de score :
    - 0-25 : Échec (0 données)
    - 25-60 : Succès minimal (50 données)
    - 60-85 : Bonne performance (100 données)
    - 85-100 : Réussite exceptionnelle (150 données)

### Sécurité

- [ ] Validation serveur stricte :
  - Tous les scores sont validés côté serveur via `ScanningMinigameValidator`
  - Les actions (clics, timestamps) sont vérifiées pour détecter les manipulations
  - Les récompenses sont calculées côté serveur uniquement
  - La config de difficulté/récompenses reste côté serveur (pas exposée au client)

- [ ] Protection contre les tentatives multiples :
  - Vérification de la limitation quotidienne (1 mini-jeu par jour)
  - Reset à minuit (basé sur `played_at`)
  - Validation que le mini-jeu n'a pas déjà été joué aujourd'hui

- [ ] Rate limiting sur les endpoints de soumission de score :
  - Limiter les tentatives de soumission pour éviter le spam

### Tests

- [ ] Tests unitaires pour `MiniGameService`
  - Test de limitation quotidienne
  - Test de calcul de récompenses
  - Test de validation de score

- [ ] Tests unitaires pour `ScanningMinigameValidator`
  - Test de validation des actions
  - Test de calcul de score
  - Test de détection de manipulations

- [ ] Tests fonctionnels pour le composant `Minigame`
  - Test d'affichage du mini-jeu disponible
  - Test de soumission de score
  - Test d'attribution de récompenses

- [ ] Tests fonctionnels pour le composant `ScanningMinigame`
  - Test de la mécanique de jeu
  - Test d'enregistrement des actions

- [ ] Tests d'intégration pour le cycle complet
  - Jouer un mini-jeu → Score validé → Récompenses attribuées → Limitation quotidienne activée

## Détails Techniques

### Architecture des composants Livewire

```
Minigame (parent)
├── Charge le mini-jeu disponible pour aujourd'hui
├── Vérifie la limitation quotidienne
├── Charge dynamiquement le composant enfant selon le type
│   └── ScanningMinigame (enfant)
│       ├── Reçoit la config depuis le parent
│       ├── Implémente la mécanique du scan circulaire
│       ├── Enregistre les actions pour validation
│       └── Soumet le score au parent
├── Valide le score côté serveur
├── Calcule les récompenses
└── Affiche les résultats
```

### Structure des données JSON

**`mini_game_types.config`** (JSON) :
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

**`mini_game_attempts.data`** (JSON) :
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

**`mini_game_attempts.rewards`** (JSON) :
```json
{
  "scientific_data": 100
}
```

### Sélectionner le mini-jeu du jour

- Algorithme simple pour le MVP : Sélectionner le premier mini-jeu actif disponible
- Plus tard : Algorithme de sélection aléatoire pondéré selon la progression

### Limitation quotidienne

- Vérifier si le joueur a déjà joué aujourd'hui : `MiniGameAttempt::where('user_id', $user->id)->whereDate('played_at', today())->exists()`
- Reset automatique à minuit (pas besoin de job, vérification à la volée)

### Routes

**Frontend** :
- `GET /minigame` - Page du mini-jeu (composant Livewire `Minigame`)

**Admin** :
- `GET /admin/minigames` - Liste des types
- `GET /admin/minigames/create` - Création
- `POST /admin/minigames` - Stockage
- `GET /admin/minigames/{id}` - Détails
- `GET /admin/minigames/{id}/edit` - Édition
- `PUT /admin/minigames/{id}` - Mise à jour
- `DELETE /admin/minigames/{id}` - Suppression
- `GET /admin/minigames/{id}/test` - Page de test

## Notes

- **Approche itérative** : On commence avec un seul mini-jeu (Scanning) pour valider l'architecture. Les autres types pourront être ajoutés facilement depuis l'admin ensuite.

- **Sécurité prioritaire** : Toute la logique de validation et de calcul des récompenses doit être côté serveur pour éviter les hacks.

- **Extensibilité** : Le système doit être conçu pour faciliter l'ajout de nouveaux types de mini-jeux sans modifier le code existant.

- **Système de ressources** : Pour le MVP, on utilise simplement `scientific_data` dans la table `users`. Plus tard, on pourra créer un système de ressources plus complexe.

- **Référence** : Le draft de conception des mini-jeux existe dans `docs/game-design/drafts/DRAFT-03-mini-games-system.md` mais n'est pas encore validé. Cette issue implémente le système de base qui permettra d'implémenter les mini-jeux décrits dans le draft.

## Références

- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Architecture technique générale
- [PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md) - Vision métier
- [DRAFT-03-mini-games-system.md](../game-design/drafts/DRAFT-03-mini-games-system.md) - Draft de conception des mini-jeux (non validé)

## Suivi et Historique

### Statut

À faire

### Historique

#### 2024-12-XX - Alex (Product) - Création de l'issue
**Statut** : À faire
**Détails** : Issue créée pour implémenter le système de base des mini-jeux avec un seul mini-jeu (Scanning) pour valider l'architecture. Le système doit être extensible et permettre l'ajout de nouveaux types depuis l'admin.
**GitHub** : [#15](https://github.com/PiLep/space-xplorer/issues/15)

