# Get Started - Stellar

Guide complet pour démarrer le projet Stellar en local.

## Vue d'Ensemble

Stellar utilise **Laravel Sail** (Docker) pour simplifier l'environnement de développement. Tous les services nécessaires (PHP, MySQL, Redis) sont conteneurisés.

## Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **Docker Desktop** (ou Docker Engine + Docker Compose)
  - [Installation Docker Desktop](https://www.docker.com/products/docker-desktop)
  - Version minimale : Docker 20.10+, Docker Compose 2.0+
- **Git**
  - [Installation Git](https://git-scm.com/downloads)
- **Un éditeur de code** (recommandé : VS Code, PhpStorm, etc.)

### Vérification des Prérequis

Vérifiez que Docker est bien installé :

```bash
docker --version
docker compose version
```

## Installation

### 1. Cloner le Repository

```bash
git clone https://github.com/your-username/stellar-game.git
cd stellar-game
```

### 2. Installer les Dépendances PHP

Si vous avez Composer installé localement :

```bash
composer install
```

Sinon, utilisez Docker pour installer Composer :

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. Configurer l'Environnement

Copiez le fichier d'environnement exemple :

```bash
cp .env.example .env
```

### 4. Démarrer les Conteneurs Docker

Lancez Laravel Sail pour démarrer tous les services :

```bash
./vendor/bin/sail up -d
```

Cette commande démarre :
- **PHP 8.2** (ou version configurée)
- **MySQL 8.0**
- **Redis**
- **Nginx** (serveur web)

### 5. Générer la Clé d'Application

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Installer les Dépendances Node.js

```bash
./vendor/bin/sail npm install
```

### 7. Configurer la Base de Données

Vérifiez que les variables d'environnement de base de données sont correctes dans `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=space_xplorer
DB_USERNAME=sail
DB_PASSWORD=password
```

Puis exécutez les migrations :

```bash
./vendor/bin/sail artisan migrate
```

Si vous voulez aussi charger des données de test (seeders) :

```bash
./vendor/bin/sail artisan migrate --seed
```

### 8. Compiler les Assets Frontend

Pour le développement (avec hot reload) :

```bash
./vendor/bin/sail npm run dev
```

Pour la production :

```bash
./vendor/bin/sail npm run build
```

### 9. Accéder à l'Application

Une fois tout démarré, l'application est accessible à :

- **Application Web** : http://localhost
- **Laravel Telescope** (si activé) : http://localhost/telescope
- **Laravel Horizon** (si activé) : http://localhost/horizon

## Commandes Utiles

### Laravel Sail

Toutes les commandes Laravel doivent être préfixées par `./vendor/bin/sail` :

```bash
# Artisan commands
./vendor/bin/sail artisan [command]

# Composer
./vendor/bin/sail composer [command]

# NPM
./vendor/bin/sail npm [command]

# Accéder au shell du conteneur PHP
./vendor/bin/sail shell

# Voir les logs
./vendor/bin/sail logs

# Arrêter les conteneurs
./vendor/bin/sail down

# Redémarrer les conteneurs
./vendor/bin/sail restart
```

### Commandes de Développement

```bash
# Exécuter les tests
./vendor/bin/sail artisan test

# Formater le code avec Pint
./vendor/bin/sail pint

# Analyser le code avec Pint (sans modifier)
./vendor/bin/sail pint --test

# Créer un contrôleur
./vendor/bin/sail artisan make:controller Api/ExampleController

# Créer un modèle avec migration
./vendor/bin/sail artisan make:model Example -m

# Créer une migration
./vendor/bin/sail artisan make:migration create_examples_table

# Créer un seeder
./vendor/bin/sail artisan make:seeder ExampleSeeder

# Créer un événement et listener
./vendor/bin/sail artisan make:event ExampleEvent
./vendor/bin/sail artisan make:listener ExampleListener --event=ExampleEvent
```

### Base de Données

```bash
# Accéder à MySQL
./vendor/bin/sail mysql

# Créer une migration
./vendor/bin/sail artisan make:migration migration_name

# Exécuter les migrations
./vendor/bin/sail artisan migrate

# Rollback de la dernière migration
./vendor/bin/sail artisan migrate:rollback

# Rollback de toutes les migrations
./vendor/bin/sail artisan migrate:reset

# Réinitialiser et réexécuter les migrations
./vendor/bin/sail artisan migrate:fresh

# Réinitialiser avec seeders
./vendor/bin/sail artisan migrate:fresh --seed
```

### Frontend

```bash
# Développement avec hot reload
./vendor/bin/sail npm run dev

# Build pour production
./vendor/bin/sail npm run build

# Vérifier le code
./vendor/bin/sail npm run lint
```

## Configuration de l'Environnement

### Variables d'Environnement Importantes

Éditez le fichier `.env` pour configurer :

```env
# Application
APP_NAME="Stellar"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Base de données
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=space_xplorer
DB_USERNAME=sail
DB_PASSWORD=password

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# Laravel Telescope (développement)
TELESCOPE_ENABLED=true

# Laravel Horizon (queues)
HORIZON_ENABLED=true
```

## Structure du Projet

```
stellar-game/
├── app/                    # Code source Laravel
│   ├── Console/            # Commandes Artisan
│   ├── Events/             # Événements métier
│   ├── Http/
│   │   ├── Controllers/    # Contrôleurs API
│   │   ├── Middleware/      # Middleware HTTP
│   │   └── Requests/       # Form Requests (validation)
│   ├── Listeners/          # Écouteurs d'événements
│   ├── Livewire/           # Composants Livewire
│   ├── Models/             # Modèles Eloquent
│   ├── Policies/           # Policies d'autorisation
│   └── Services/           # Services métier
├── database/
│   ├── migrations/         # Migrations de base de données
│   └── seeders/            # Seeders pour données de test
├── docs/                   # Documentation du projet
│   ├── agents/             # Documentation des agents IA
│   ├── memory_bank/        # Memory Bank (architecture, stack)
│   ├── prompts/            # Guides d'actions pour agents
│   └── rules/              # Règles et validations
├── public/                 # Point d'entrée web
├── resources/
│   ├── js/                 # JavaScript/Alpine.js
│   ├── css/                # CSS/Tailwind
│   └── views/              # Vues Blade/Livewire
├── routes/
│   ├── api.php             # Routes API
│   └── web.php             # Routes Web
├── tests/                  # Tests automatisés
└── docker-compose.yml      # Configuration Docker Sail
```

## Dépannage

### Les conteneurs ne démarrent pas

```bash
# Vérifier les logs
./vendor/bin/sail logs

# Redémarrer les conteneurs
./vendor/bin/sail down
./vendor/bin/sail up -d
```

### Erreur de permissions

Sur Linux/Mac, vous pourriez avoir besoin de donner les permissions :

```bash
chmod +x vendor/bin/sail
```

### Port déjà utilisé

Si le port 80 est déjà utilisé, modifiez `docker-compose.yml` :

```yaml
ports:
    - '8080:80'  # Utilisez le port 8080 au lieu de 80
```

Puis accédez à http://localhost:8080

### Base de données ne se connecte pas

Vérifiez que MySQL est bien démarré :

```bash
./vendor/bin/sail ps
```

Si MySQL n'est pas démarré :

```bash
./vendor/bin/sail up -d mysql
```

### Erreur "Class not found" après composer install

```bash
./vendor/bin/sail composer dump-autoload
```

### Cache Laravel

Si vous rencontrez des problèmes de cache :

```bash
# Vider tous les caches
./vendor/bin/sail artisan optimize:clear

# Ou individuellement
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear
```

## Prochaines Étapes

Une fois l'application démarrée :

1. **Explorer la documentation** :
   - [AGENTS.md](./AGENTS.md) : Documentation complète des agents
   - [WORKFLOW.md](./WORKFLOW.md) : Workflow de développement
   - [docs/memory_bank/](./docs/memory_bank/) : Architecture et stack

2. **Créer un compte utilisateur** :
   - Accédez à http://localhost
   - Créez un compte pour générer votre planète d'origine

3. **Explorer le code** :
   - Commencez par `routes/api.php` pour voir les endpoints
   - Explorez `app/Models/` pour comprendre les modèles
   - Regardez `app/Events/` et `app/Listeners/` pour l'architecture événementielle

4. **Lire les issues** :
   - Consultez `docs/issues/` pour voir les fonctionnalités planifiées
   - Regardez `docs/tasks/` pour les plans de développement

## Support

Si vous rencontrez des problèmes :

1. Vérifiez les logs : `./vendor/bin/sail logs`
2. Consultez la documentation dans `docs/`
3. Vérifiez les issues GitHub existantes
4. Créez une nouvelle issue si nécessaire

## Ressources

- [Documentation Laravel](https://laravel.com/docs)
- [Documentation Livewire](https://livewire.laravel.com/docs)
- [Documentation Laravel Sail](https://laravel.com/docs/sail)
- [Documentation Tailwind CSS](https://tailwindcss.com/docs)

---

**Bon développement ! 🚀**

