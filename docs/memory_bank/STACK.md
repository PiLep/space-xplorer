# Stack Technique - Stellar

## Vue d'ensemble

**Backend** : Laravel 12 avec architecture événementielle (Events & Listeners)  
**Frontend** : Livewire 3 + Tailwind CSS + Alpine.js  
**Base de données** : MySQL 8.0  
**Cache & Queues** : Redis  
**Build** : Vite  
**Développement** : Laravel Sail (Docker)  
**Déploiement** : Laravel Forge

## Laravel

**Version** : Laravel 12 (dernière version, sortie le 24 février 2025)
**Rôle** : Framework PHP backend pour la logique métier et l'API
**Utilisation** : 
- Gestion des routes et contrôleurs
- Modèles Eloquent pour la base de données
- Authentification et autorisation
- **Events & Listeners** : Architecture basée sur les événements pour découpler la logique métier
- [À compléter]

### Packages Laravel officiels

- **Laravel Sanctum** : Authentification par tokens pour les API (SPA et applications mobiles)
- **Laravel Telescope** : Outil de debugging et monitoring en développement (requêtes, exceptions, logs)
- **Laravel Horizon** : Interface de monitoring et gestion des files d'attente Redis (pour génération de systèmes stellaires en arrière-plan)
- **Laravel Pint** : Outil de formatage et analyse de code PHP (cohérence du code)
- **Laravel Socialite** : Authentification OAuth via Google, Facebook, GitHub (optionnel)

### Fonctionnalités Laravel utilisées

- **Events & Listeners** : Architecture événementielle pour gérer les interactions du jeu
  - Découverte de systèmes stellaires
  - Exploration de planètes
  - Actions des joueurs
  - [À compléter avec les événements spécifiques]

## Livewire

**Version** : Livewire 3 (version actuelle, compatible avec Laravel 12)
**Rôle** : Framework pour créer des interfaces interactives sans JavaScript
**Utilisation** :
- **Interface complète** : Toute l'interface utilisateur sera construite avec Livewire (dans un premier temps)
- Composants interactifs côté serveur
- Mise à jour dynamique de l'interface
- [À compléter]

## MySQL

**Version** : MySQL 8.0
**Rôle** : Base de données relationnelle pour stocker les données du jeu
**Utilisation** :
- Stockage des données utilisateurs
- Stockage des systèmes stellaires et planètes
- Utilisation classique (pas de fonctionnalités avancées spécifiques)
- [À compléter]

## Frontend & Styling

### Tailwind CSS

**Version** : [À spécifier]
**Rôle** : Framework CSS utility-first pour le styling
**Utilisation** :
- Framework CSS principal pour l'interface
- **Design system custom** : Création d'un design system personnalisé basé sur Tailwind
- [À compléter]

### Vite

**Version** : [À spécifier]
**Rôle** : Build tool pour compiler et optimiser les assets (JavaScript/CSS)
**Utilisation** :
- Compilation des assets frontend (recommandé avec Laravel 12)
- Hot Module Replacement (HMR) pour le développement
- Optimisation des assets pour la production
- [À compléter]

### JavaScript

**Bibliothèques prévues** :
- **Alpine.js** : Framework JavaScript léger, souvent intégré avec Livewire pour les interactions côté client
- [À compléter selon les besoins : visualisations spatiales, animations, etc.]

## Outils de développement

### Laravel Sail

**Rôle** : Environnement de développement Docker pour Laravel
**Utilisation** :
- Environnement de développement local avec Docker
- Conteneurs pour PHP, MySQL, Redis, etc.
- Simplifie la configuration et le démarrage du projet
- [À compléter]

### Laravel Forge

**Rôle** : Plateforme de déploiement et gestion de serveurs pour Laravel
**Utilisation** :
- Déploiement automatisé de l'application
- Gestion des serveurs et configurations
- [À compléter]

## Services & Infrastructure

### Redis

**Rôle** : Base de données en mémoire pour le cache et les queues
**Utilisation** :
- Cache des données fréquemment accédées
- Backend pour les queues Laravel (requis par Laravel Horizon)
- Stockage de sessions (optionnel)
- [À compléter]

## Autres technologies

- [À ajouter selon les besoins : outils de build, etc.]

