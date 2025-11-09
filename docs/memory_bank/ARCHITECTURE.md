# Architecture - Space Xplorer

## Architecture technique

### Structure générale

- **Backend** : Laravel (MVC pattern)
- **Frontend** : Livewire 3 components
- **Database** : MySQL 8.0
- **Architecture** : Monolithique (application unique Laravel)
- **Approche** : API-first - Toute la logique métier via endpoints REST API, Livewire consomme ces APIs en interne

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

### Endpoints utilisateurs (MVP)

- `GET /api/users/{id}` - Détails d'un utilisateur (authentification requise)
- `PUT /api/users/{id}` - Mise à jour du profil utilisateur (authentification requise, uniquement son propre profil)
- `GET /api/users/{id}/home-planet` - Planète d'origine du joueur (authentification requise)

**Endpoints futurs** :
- `GET /api/users` - Liste des utilisateurs (avec pagination) - À implémenter selon les besoins

### Endpoints planètes (MVP)

- `GET /api/planets/{id}` - Détails d'une planète (authentification requise)

**Endpoints futurs** :
- `GET /api/planets` - Liste des planètes (avec pagination) - À implémenter selon les besoins
- `POST /api/planets/{id}/explore` - Explorer une planète (action du joueur) - À implémenter selon les besoins

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
- Toutes les routes `/api/users/*` nécessitent l'authentification (`auth:sanctum`)
- Toutes les routes `/api/planets/*` nécessitent l'authentification (`auth:sanctum`)
- Routes `/api/auth/logout` et `/api/auth/user` nécessitent l'authentification (`auth:sanctum`)
- Routes `/api/auth/register` et `/api/auth/login` sont publiques (pas d'authentification requise)

**Autorisation** :
- Un utilisateur ne peut modifier que son propre profil (`PUT /api/users/{id}` vérifie que `auth()->id() === $user->id`)

**Évolutions futures** :
- Système de rôles (admin, modérateur, joueur)
- Permissions granulaires
- [À compléter selon les besoins]

## Génération de planètes

### Architecture de génération

**Principe** : Système de génération procédurale avec pool de types pondérés

**Composants** :
- **Service de génération** : `PlanetGeneratorService` dans `app/Services/`
- **Configuration des types** : Pool de types de planètes avec poids de probabilité dans `config/planets.php`
- **Randomisation** : Sélection aléatoire pondérée du type, puis génération des caractéristiques selon les poids du type
- **Gestion d'unicité** : Mécanisme de vérification d'unicité du nom avec gestion des collisions (max 10 tentatives, puis ajout d'un identifiant unique)

**Flux technique** :
1. Listener `GenerateHomePlanet` appelle `PlanetGeneratorService::generate()`
2. Service sélectionne un type selon les poids définis dans `config/planets.php`
3. Service génère les caractéristiques selon les distributions du type
4. Service génère un nom unique (avec gestion des collisions si nécessaire)
5. Service génère une description textuelle à partir des caractéristiques combinées
6. Service crée l'entité `Planet` en base de données
7. Planète assignée au joueur (`home_planet_id`)

**Gestion d'erreurs** :
- Le listener `GenerateHomePlanet` utilise un try-catch pour gérer les erreurs
- En cas d'erreur de génération, l'erreur est loggée mais l'inscription n'est pas bloquée
- `home_planet_id` reste null en cas d'erreur (peut être géré plus tard)

**Stockage** : Configuration des types et poids dans `config/planets.php` (fichier de configuration Laravel standard)

*Note : Les détails métier (types de planètes, caractéristiques) sont documentés dans PROJECT_BRIEF.md*

## Frontend - Livewire Components

### Architecture Frontend

**Approche API-first** : Les composants Livewire consomment les endpoints API REST en interne. Toute la logique métier est dans l'API, Livewire sert uniquement d'interface utilisateur.

### Authentification Hybride

**Double authentification** :
- **API** : Authentification par tokens Sanctum (`Authorization: Bearer {token}`)
- **Routes Web** : Authentification par session Laravel (pour les routes Livewire)

**Fonctionnement** :
1. Lors de l'inscription/connexion via API (`POST /api/auth/register` ou `POST /api/auth/login`), le token Sanctum est créé
2. Le token est stocké en session (`Session::put('sanctum_token', $token)`)
3. L'utilisateur est également authentifié en session (`Auth::login($user)`) pour les routes web
4. Les composants Livewire utilisent le token de session pour faire des requêtes API authentifiées
5. Les routes web Livewire utilisent l'authentification de session (`middleware('auth')`)

### Trait MakesApiRequests

**Localisation** : `app/Livewire/Concerns/MakesApiRequests.php`

**Fonctionnalités** :
- Méthodes helper pour faire des requêtes API authentifiées depuis Livewire
- Récupération automatique du token depuis la session
- Gestion des erreurs (validation, erreurs API)
- Méthodes disponibles :
  - `apiGet(string $endpoint)` : Requête GET authentifiée
  - `apiPost(string $endpoint, array $data)` : Requête POST authentifiée
  - `apiPut(string $endpoint, array $data)` : Requête PUT authentifiée
  - `apiPostPublic(string $endpoint, array $data)` : Requête POST publique (pour register/login)

**Utilisation** :
```php
use App\Livewire\Concerns\MakesApiRequests;

class Dashboard extends Component
{
    use MakesApiRequests;
    
    public function mount()
    {
        $response = $this->apiGet('/auth/user');
        // ...
    }
}
```

### Composants Livewire (MVP)

- **Register** : Formulaire d'inscription (`/register`)
- **Login** : Formulaire de connexion (`/login`)
- **Dashboard** : Affichage de la planète d'origine (`/dashboard`)
- **Profile** : Gestion du profil utilisateur (`/profile`)

**Routes Web** :
- `/` : Page d'accueil (publique)
- `/register` : Inscription (guest)
- `/login` : Connexion (guest)
- `/dashboard` : Tableau de bord (auth)
- `/profile` : Profil utilisateur (auth)
- `POST /logout` : Déconnexion (auth)

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

