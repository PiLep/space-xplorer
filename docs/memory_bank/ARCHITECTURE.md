# Architecture - Space Xplorer

## Architecture technique

### Structure générale

- **Backend** : Laravel (MVC pattern)
- **Frontend** : Livewire components
- **Database** : MySQL
- **Architecture** : Monolithique (application unique Laravel)

### Organisation des dossiers

Structure MVC Laravel standard avec gestion par événements :

```
app/
├── Console/          # Commandes Artisan
├── Events/           # Événements du domaine métier
├── Exceptions/       # Gestion des exceptions
├── Http/
│   ├── Controllers/  # Contrôleurs MVC
│   ├── Middleware/   # Middleware HTTP
│   └── Requests/     # Form Requests (validation)
├── Listeners/        # Écouteurs d'événements
├── Livewire/         # Composants Livewire
├── Models/           # Modèles Eloquent
├── Policies/         # Policies d'autorisation
├── Providers/        # Service Providers
└── Services/         # Services métier (optionnel)

routes/
├── web.php          # Routes web (Livewire)
├── api.php          # Routes API
└── channels.php     # Routes de broadcasting

database/
├── migrations/      # Migrations de base de données
├── seeders/         # Seeders
└── factories/       # Factories pour les tests

resources/
├── views/           # Vues Blade (layouts, composants)
│   └── livewire/    # Vues Livewire
├── css/             # Styles CSS (Tailwind)
└── js/              # JavaScript (Alpine.js)

tests/               # Tests unitaires et fonctionnels
```

## Modèle de données

### Entités principales

- **Users** : Gestion des utilisateurs/joueurs
- **Planets** : Planètes explorables

*Note : Le modèle de données sera étendu progressivement (systèmes stellaires, objets célestes, etc.)*

### Relations

- **Users → Planets** : Relation "planète d'origine"
  - Un joueur a une planète d'origine (générée aléatoirement à l'inscription)
  - Une planète peut être la planète d'origine de plusieurs joueurs
  - Relation : `user.home_planet_id` → `planets.id`
  - *Note : Les fonctionnalités d'exploration seront ajoutées progressivement*

### Structure simplifiée

```
Users
├── id
├── name
├── email
├── home_planet_id (foreign key → planets.id)
└── [autres champs standards Laravel]

Planets
├── id
├── name (généré aléatoirement)
├── type (type de planète : tellurique, gazeuse, etc.)
├── size (petite, moyenne, grande)
├── temperature (froide, tempérée, chaude)
├── atmosphere (respirable, toxique, inexistante)
├── terrain (rocheux, océanique, désertique, forestier, etc.)
├── resources (abondantes, modérées, rares)
├── description (générée à partir des caractéristiques)
└── created_at / updated_at
```

## API endpoints

**Approche API-first** : Toute la logique métier est exposée via des endpoints API REST. Livewire consomme ces APIs en interne.

### Authentification

- `POST /api/auth/register` - Inscription d'un nouveau joueur
- `POST /api/auth/login` - Connexion (retourne un token Sanctum)
- `POST /api/auth/logout` - Déconnexion
- `GET /api/auth/user` - Informations du joueur connecté

### Endpoints utilisateurs

- `GET /api/users` - Liste des utilisateurs (si nécessaire)
- `GET /api/users/{id}` - Détails d'un utilisateur
- `GET /api/users/{id}/home-planet` - Planète d'origine du joueur
- `PUT /api/users/{id}` - Mise à jour du profil utilisateur
- [À compléter selon les besoins]

### Endpoints planètes

- `GET /api/planets` - Liste des planètes (avec pagination)
- `GET /api/planets/{id}` - Détails d'une planète
- `POST /api/planets/{id}/explore` - Explorer une planète (action du joueur)
- [À compléter selon les besoins]

### Format de réponse

Toutes les réponses API suivent un format JSON standardisé :
```json
{
  "data": { ... },
  "message": "Success message",
  "status": "success"
}
```

## Flux métier

### Flux d'inscription et génération de planète

1. **Inscription** : `POST /api/auth/register`
   - Création du compte utilisateur
   - Génération automatique d'une planète d'origine (aléatoire)
   - Attribution de la planète au joueur (`home_planet_id`)
   - Retour du token d'authentification

2. **Connexion** : `POST /api/auth/login`
   - Authentification du joueur
   - Retour du token d'authentification

3. **Affichage de la planète d'origine** : `GET /api/users/{id}/home-planet`
   - Récupération de la planète d'origine du joueur
   - Affichage des détails de la planète

### Flux d'exploration (à venir)

- [À documenter : exploration d'autres planètes, découvertes, etc.]

## Architecture événementielle

### Événements (MVP)

**Approche simplifiée pour le MVP** : Un seul événement essentiel pour démarrer.

#### `UserRegistered`

**Déclencheur** : Lors de l'inscription d'un nouveau joueur (`POST /api/auth/register`)

**Listeners** :
- `GenerateHomePlanet` : Génère automatiquement une planète d'origine aléatoire et l'assigne au joueur

**Flux** :
1. Contrôleur API crée l'utilisateur
2. Événement `UserRegistered` est dispatché avec l'utilisateur créé
3. Listener `GenerateHomePlanet` génère une planète aléatoire
4. La planète est assignée au joueur (`home_planet_id`)

**Événements futurs** (à ajouter progressivement) :
- `PlanetExplored` : Lorsqu'un joueur explore une planète
- `DiscoveryMade` : Lorsqu'un joueur fait une découverte
- [À compléter selon les besoins]

## Authentification & Autorisation

### Authentification

**Laravel Sanctum** : Authentification par tokens pour l'API

**Fonctionnement** :
- Inscription/Connexion génère un token Sanctum
- Le token est envoyé dans le header `Authorization: Bearer {token}`
- Middleware `auth:sanctum` protège les routes API

**MVP** :
- Authentification simple : joueur connecté / non connecté
- Pas de système de rôles ou permissions pour le moment
- Tous les joueurs ont les mêmes droits d'accès

**Routes protégées** :
- Toutes les routes `/api/users/*` nécessitent l'authentification
- Toutes les routes `/api/planets/*` nécessitent l'authentification (sauf si liste publique)
- Route `/api/auth/user` pour récupérer le joueur connecté

**Évolutions futures** :
- Système de rôles (admin, modérateur, joueur)
- Permissions granulaires
- [À compléter selon les besoins]

## Génération de planètes

### Architecture de génération

**Principe** : Système de génération procédurale avec pool de types pondérés

**Composants** :
- **Service de génération** : `PlanetGeneratorService` dans `app/Services/`
- **Configuration des types** : Pool de types de planètes avec poids de probabilité
- **Randomisation** : Sélection aléatoire pondérée du type, puis génération des caractéristiques selon les poids du type

**Flux technique** :
1. Listener `GenerateHomePlanet` appelle `PlanetGeneratorService`
2. Service sélectionne un type selon les poids définis
3. Service génère les caractéristiques selon les distributions du type
4. Service crée l'entité `Planet` en base de données
5. Planète assignée au joueur (`home_planet_id`)

**Stockage** : Configuration des types et poids dans un fichier de config ou une classe dédiée

*Note : Les détails métier (types de planètes, caractéristiques) sont documentés dans PROJECT_BRIEF.md*

## Aspects techniques standards

### Validation des données

- **Form Requests** : Utilisation de `FormRequest` pour valider les données API
- **Validation côté serveur** : Toutes les données sont validées avant traitement
- **Messages d'erreur** : Format JSON standardisé pour les erreurs de validation

### Gestion des erreurs

- **Exceptions** : Gestion centralisée via `app/Exceptions/Handler.php`
- **Format de réponse** : Erreurs API au format JSON standardisé
- **Codes HTTP** : Utilisation appropriée des codes de statut (200, 201, 400, 401, 404, 500, etc.)

### Pagination

- **Collections Laravel** : Utilisation de `paginate()` pour les listes
- **Format** : Pagination Laravel standard avec métadonnées (links, meta)
- **Taille par défaut** : [À définir]

### Cache

- **Redis** : Utilisation de Redis pour le cache des données fréquemment accédées
- **Stratégie** : Cache des planètes, des listes, etc. (à définir selon les besoins)

