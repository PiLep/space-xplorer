# Technical Rules - Space Xplorer

Ce document contient les règles techniques validées pour le projet Space Xplorer. Ces règles améliorent la qualité du code et standardisent les bonnes pratiques de l'équipe.

## Processus d'Ajout

Les règles techniques sont proposées par Morgan (Architect) ou Sam (Lead Developer) via le processus décrit dans [propose-technical-rule.md](../prompts/propose-technical-rule.md).

⚠️ **Toute nouvelle règle nécessite une validation humaine avant application.**

## Règles Actuelles

### Règle 1 : Utilisation de Laravel Sail pour l'environnement de développement

**Date d'ajout** : 2025-11-09  
**Proposée par** : Jordan (Fullstack Developer)  
**Validée par** : À valider

**Description** : Toutes les commandes de développement (artisan, composer, npm, migrations, tests, etc.) doivent être exécutées via Laravel Sail (`./vendor/bin/sail`) pour garantir la cohérence avec l'environnement Docker de développement.

**Exemples** :

**Bon exemple** :
```bash
# Exécuter les migrations
./vendor/bin/sail artisan migrate

# Installer une dépendance Composer
./vendor/bin/sail composer require laravel/sanctum

# Exécuter les tests
./vendor/bin/sail artisan test

# Formater le code
./vendor/bin/sail pint

# Installer les dépendances NPM
./vendor/bin/sail npm install
```

**Mauvais exemple** :
```bash
# ❌ Ne pas utiliser directement artisan/composer/npm
php artisan migrate
composer require laravel/sanctum
npm install
```

**Justification** : 
- Garantit la cohérence de l'environnement de développement (PHP version, extensions, MySQL, Redis)
- Évite les problèmes de compatibilité entre les environnements locaux
- Simplifie le setup pour les nouveaux développeurs
- Assure que l'environnement de développement correspond à l'environnement de production
- Laravel Sail fournit un environnement isolé et reproductible

### Règle 2 : Vérification de l'application avec Chrome DevTools MCP

**Date d'ajout** : 2025-11-09  
**Proposée par** : Jordan (Fullstack Developer)  
**Validée par** : À valider

**Description** : Après avoir lancé l'application ou implémenté une nouvelle fonctionnalité, utiliser Chrome DevTools MCP pour vérifier visuellement que l'application fonctionne correctement. Cela permet de détecter les problèmes d'affichage, d'interaction, ou d'erreurs JavaScript avant de continuer.

**Quand utiliser** :
- Après avoir lancé l'application pour la première fois
- Après avoir implémenté une nouvelle fonctionnalité frontend
- Après avoir modifié des routes ou des contrôleurs qui affectent l'affichage
- Avant de marquer une phase comme terminée
- Lorsqu'on suspecte un problème d'affichage ou d'interaction

**Exemples** :

**Bon exemple** :
```bash
# 1. Lancer l'application
./vendor/bin/sail up -d

# 2. Vérifier avec Chrome DevTools MCP
# - Prendre un snapshot de la page
# - Vérifier les erreurs dans la console
# - Vérifier les requêtes réseau
# - Tester les interactions utilisateur si nécessaire
```

**Mauvais exemple** :
```bash
# ❌ Ne pas vérifier visuellement l'application
# Se contenter de vérifier que les migrations passent
# Ignorer les problèmes d'affichage ou d'interaction
```

**Justification** : 
- Détecte les problèmes visuels et d'interaction avant qu'ils ne soient découverts par les utilisateurs
- Permet de vérifier que les modifications fonctionnent comme prévu dans un vrai navigateur
- Identifie les erreurs JavaScript ou CSS qui ne seraient pas détectées par les tests backend
- Assure une meilleure qualité globale de l'application
- Chrome DevTools MCP permet une vérification automatisée et reproductible

**Outils disponibles** :
- `take_snapshot` : Prendre un snapshot de la page pour voir la structure HTML
- `list_console_messages` : Vérifier les erreurs JavaScript
- `list_network_requests` : Vérifier les requêtes réseau et leurs statuts
- `take_screenshot` : Capturer une image de la page pour documentation
- `evaluate_script` : Exécuter du JavaScript pour tester des interactions

---

## Format d'une Règle

Chaque règle doit suivre ce format :

```markdown
### Règle X : [Titre de la règle]

**Date d'ajout** : YYYY-MM-DD  
**Proposée par** : [Morgan | Sam]  
**Validée par** : [Nom du validateur]

**Description** : [Description de la règle]

**Exemples** :

**Bon exemple** :
```php
// Code conforme
```

**Mauvais exemple** :
```php
// Code non conforme
```

**Justification** : [Pourquoi cette règle est importante]
```

## Références

- [propose-technical-rule.md](../prompts/propose-technical-rule.md) : Guide pour proposer une nouvelle règle
- [HUMAN_VALIDATION.md](./HUMAN_VALIDATION.md) : Points de validation humaine
- [proposals/](./proposals/) : Propositions en attente de validation

