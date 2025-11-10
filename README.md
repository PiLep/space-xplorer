# Space Xplorer 🚀

Un jeu d'exploration de l'univers où les joueurs peuvent découvrir et explorer différents systèmes stellaires, planètes et objets célestes dans un univers virtuel.

[![CI](https://github.com/PiLep/space-xplorer/actions/workflows/ci.yml/badge.svg)](https://github.com/PiLep/space-xplorer/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/PiLep/space-xplorer/branch/develop/graph/badge.svg)](https://codecov.io/gh/PiLep/space-xplorer)

## 🎮 À propos

Space Xplorer est un jeu web d'exploration spatiale développé avec Laravel et Livewire. Chaque joueur commence avec une planète d'origine générée aléatoirement et peut explorer l'univers progressivement.

### Fonctionnalités MVP

- ✅ **Inscription/Connexion** : Création de compte et authentification
- ✅ **Génération de planète d'origine** : À l'inscription, chaque joueur reçoit automatiquement une planète d'origine générée aléatoirement
- ✅ **Visualisation de la planète** : Affichage des caractéristiques de la planète d'origine du joueur
- ✅ **Profil utilisateur** : Gestion du profil du joueur

### Fonctionnalités futures

- Exploration d'autres planètes
- Découverte de systèmes stellaires
- Système de progression et d'achievements
- Interactions entre joueurs

## 🛠️ Stack Technique

- **Backend** : Laravel 12 avec architecture événementielle (Events & Listeners)
- **Frontend** : Livewire 3 + Tailwind CSS + Alpine.js
- **Base de données** : MySQL 8.0
- **Cache & Queues** : Redis
- **Build** : Vite
- **Développement** : Laravel Sail (Docker)
- **CI/CD** : GitHub Actions

## 📋 Prérequis

- Docker et Docker Compose
- Git

## 🚀 Installation

### Avec Laravel Sail (Recommandé)

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/PiLep/space-xplorer.git
   cd space-xplorer
   ```

2. **Installer les dépendances et démarrer les conteneurs**
   ```bash
   ./vendor/bin/sail up -d
   ```

3. **Installer les dépendances Composer et NPM**
   ```bash
   ./vendor/bin/sail composer install
   ./vendor/bin/sail npm install
   ```

4. **Configurer l'environnement**
   ```bash
   cp .env.example .env
   ./vendor/bin/sail artisan key:generate
   ```

5. **Exécuter les migrations**
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

6. **Builder les assets frontend**
   ```bash
   ./vendor/bin/sail npm run build
   ```

7. **Configurer MinIO (S3 local)** :
   - Accéder à la console MinIO : http://localhost:9001 (minioadmin/minioadmin)
   - Créer un bucket nommé `space-xplorer` (ou celui configuré dans `.env`)
   - Cocher "Make this bucket public" pour que les images soient accessibles

8. **Accéder à l'application**
   - Application : http://localhost
   - Mailpit (emails) : http://localhost:8025
   - MinIO Console : http://localhost:9001

### Configuration du Super Admin

Le projet inclut un système d'administration caché accessible via `/admin`. Pour configurer un super admin :

1. **Créer un utilisateur normal** via l'inscription publique
2. **Ajouter l'email dans `.env`** :
   ```env
   ADMIN_EMAIL_WHITELIST=admin@example.com,superadmin@example.com
   ```
   (Plusieurs emails séparés par des virgules)
3. **Définir le flag super admin** :
   ```bash
   ./vendor/bin/sail artisan admin:make admin@example.com
   ```
4. **Se connecter** via `/admin/login` avec les identifiants de cet utilisateur

**Sécurité** : L'accès admin nécessite à la fois le flag `is_super_admin` ET la présence de l'email dans `ADMIN_EMAIL_WHITELIST`.

### Génération automatique de ressources

Le système génère automatiquement des ressources via des tâches planifiées (scheduler) :

#### Ressources planètes (20 par jour)
- **Horaire** : Tous les jours à 2h00 du matin
- **Statut initial** : Les ressources sont créées avec le statut `generating`
- **Génération** : Les images sont générées de manière asynchrone via des jobs
- **Validation** : Les ressources doivent être approuvées par un admin via `/admin/resources` avant d'être réutilisées

Les prompts sont générés de manière variée pour couvrir tous les types de planètes selon leurs probabilités :
- **Tellurique** : 40% des ressources
- **Gazeuse** : 25% des ressources
- **Glacée** : 15% des ressources
- **Désertique** : 10% des ressources
- **Océanique** : 10% des ressources

#### Ressources avatars (20 par jour)
- **Horaire** : Tous les jours à 2h30 du matin (30 minutes après les planètes)
- **Distribution** : 50% hommes, 50% femmes
- **Professions variées** : Techniciens, capitaines, explorateurs, ingénieurs, pilotes, scientifiques, mécaniciens, médecins, officiers de communication, agents de sécurité
- **Même processus** : Génération asynchrone puis validation admin

**Note** : Pour que le scheduler fonctionne, vous devez configurer une tâche cron :
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Commandes utiles

```bash
# Démarrer les conteneurs
./vendor/bin/sail up -d

# Arrêter les conteneurs
./vendor/bin/sail down

# Accéder au shell du conteneur
./vendor/bin/sail shell

# Exécuter les tests
./vendor/bin/sail test

# Formater le code avec Pint
./vendor/bin/sail pint

# Builder les assets en mode développement
./vendor/bin/sail npm run dev

# Créer un super admin
./vendor/bin/sail artisan admin:make email@example.com

# Générer des ressources planètes quotidiennes (20 par défaut)
./vendor/bin/sail artisan resources:generate-daily-planets

# Générer des ressources planètes avec un nombre personnalisé
./vendor/bin/sail artisan resources:generate-daily-planets --count=30

# Générer des ressources avatars quotidiennes (20 par défaut)
./vendor/bin/sail artisan resources:generate-daily-avatars

# Générer des ressources avatars avec un nombre personnalisé
./vendor/bin/sail artisan resources:generate-daily-avatars --count=30
```

## 🧪 Tests

```bash
# Exécuter tous les tests
./vendor/bin/sail test

# Exécuter les tests avec coverage
./vendor/bin/sail test --coverage
```

**Documentation sur les tests** :
- **[Tester les Événements avec Listeners en Queue](./docs/setup/testing-events.md)** - Guide complet pour tester les événements avec des listeners en queue

## 📚 Documentation

Ce projet utilise un système de workflow structuré avec des agents IA spécialisés :

### 🚀 Démarrage Rapide

- **[PROMPTS_GUIDE.md](./PROMPTS_GUIDE.md)** - Guide complet avec tous les prompts pour chaque étape du workflow
- **[GET_STARTED_WORKFLOW.md](./GET_STARTED_WORKFLOW.md)** - Guide de démarrage rapide du workflow

### 📖 Documentation Complète

- **[WORKFLOW.md](./WORKFLOW.md)** - Schéma et description complète du workflow de développement
- **[AGENTS.md](./AGENTS.md)** - Liste complète des agents et leurs rôles

### 🧠 Memory Bank

Documentation du projet Space Xplorer :

- **[PROJECT_BRIEF.md](./docs/memory_bank/PROJECT_BRIEF.md)** - Vision métier, fonctionnalités, personas, flux utilisateurs
- **[ARCHITECTURE.md](./docs/memory_bank/ARCHITECTURE.md)** - Architecture technique, modèle de données, API endpoints, flux métier
- **[STACK.md](./docs/memory_bank/STACK.md)** - Stack technique détaillée

### 👥 Agents

- **[PRODUCT.md](./docs/agents/PRODUCT.md)** - **Alex** - Product Manager
- **[LEAD-DEV.md](./docs/agents/LEAD-DEV.md)** - **Sam** - Lead Developer
- **[ARCHITECT.md](./docs/agents/ARCHITECT.md)** - **Morgan** - Architecte
- **[FULLSTACK-DEV.md](./docs/agents/FULLSTACK-DEV.md)** - **Jordan** - Fullstack Developer
- **[MANAGER.md](./docs/agents/MANAGER.md)** - **Taylor** - Workflow Manager

## 🔄 Workflow de Développement

Ce projet suit un workflow structuré en 9 étapes :

1. **Création d'Issue** (Alex) → Issue produit documentée
2. **Création de Branche** (Sam) → Branche Git créée
3. **Création du Plan** (Sam) → Plan technique détaillé
4. **Review Architecturale** (Morgan) → Plan approuvé
5. **Implémentation** (Jordan) → Code implémenté
6. **Review du Code** (Sam) → Code approuvé
7. **Review Fonctionnelle** (Alex) → Fonctionnalité approuvée
8. **Création de PR** (Sam) → Pull Request créée
9. **Merge** (Sam) → Code mergé dans `develop`

Voir **[WORKFLOW.md](./WORKFLOW.md)** pour plus de détails.

## 🤝 Contribution

Les contributions sont les bienvenues ! Veuillez suivre le workflow décrit dans **[WORKFLOW.md](./WORKFLOW.md)**.

1. Créer une issue pour discuter des changements proposés
2. Créer une branche depuis `develop`
3. Suivre le workflow de développement avec les agents IA
4. Créer une Pull Request vers `develop`

## 📝 License

Ce projet est open-source et disponible sous la [MIT License](https://opensource.org/licenses/MIT).

## 🔗 Liens

- **Repository** : https://github.com/PiLep/space-xplorer
- **Documentation** : Voir le dossier `docs/`
- **Issues** : https://github.com/PiLep/space-xplorer/issues

---

**Space Xplorer** - Explore the universe 🌌
