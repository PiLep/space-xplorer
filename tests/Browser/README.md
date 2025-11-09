# Tests E2E avec Playwright

Ce répertoire contient les tests end-to-end utilisant Playwright pour tester l'application dans un vrai navigateur.

## Installation

Playwright est déjà installé via npm. Les navigateurs sont installés automatiquement.

## Utilisation

### Via Pest (recommandé)

Les tests dans ce répertoire utilisent Pest et peuvent être exécutés avec :

```bash
./vendor/bin/sail pest tests/Browser
```

### Via Playwright directement

#### Dans Docker (Laravel Sail)

```bash
# Tous les tests (mode headless - fonctionne dans Docker)
./vendor/bin/sail npm run test:e2e

# Mode debug (pause sur erreur, utile pour débugger)
./vendor/bin/sail npm run test:e2e:debug

# Voir le rapport HTML après les tests
./vendor/bin/sail npm run test:e2e:report
```

**Note importante** : Le mode UI (`--ui`) et le mode headed (`--headed`) **ne fonctionnent pas dans Docker** car il n'y a pas de serveur X (interface graphique). 

**Alternatives pour débugger** :
- ✅ **Mode debug** : `npm run test:e2e:debug` - pause automatique sur erreur
- ✅ **Screenshots** : Capturés automatiquement en cas d'échec (dans `test-results/`)
- ✅ **Vidéos** : Enregistrés automatiquement en cas d'échec (dans `test-results/`)
- ✅ **Rapport HTML** : `npm run test:e2e:report` - visualise les tests avec screenshots/vidéos
- ✅ **Traces** : Activés automatiquement en cas d'échec (dans `test-results/`)

#### En local (hors Docker) - Si vous voulez vraiment le mode UI

Si vous exécutez les tests **en dehors de Docker** (sur votre machine locale), vous pouvez utiliser :

```bash
# Mode UI interactif (nécessite Node.js local, pas dans Docker)
playwright test --ui

# Mode headed (voir le navigateur)
playwright test --headed
```

## Configuration

La configuration Playwright se trouve dans `playwright.config.cjs` à la racine du projet (extension `.cjs` car `package.json` utilise `"type": "module"`).

### Configuration Docker

- **Mode headless** : Activé par défaut (nécessaire dans Docker)
- **Base URL** : `http://localhost:8000` (serveur Laravel)
- **Web Server** : Playwright démarre automatiquement `php artisan serve` si l'app n'est pas déjà en cours d'exécution

## Exemple de test

### Test Pest (recommandé)

```php
it('can complete registration flow', function () {
    // Test avec Pest + assertions Laravel
    $response = $this->get('/register');
    $response->assertStatus(200);
    
    // Vous pouvez aussi utiliser Playwright directement
    // via le PlaywrightHelper pour des tests plus complexes
});
```

### Test Playwright standalone

```javascript
import { test, expect } from '@playwright/test';

test('should complete registration', async ({ page }) => {
    await page.goto('/register');
    await page.fill('input[name="name"]', 'Test User');
    // ...
});
```

## Notes importantes

### Docker vs Local

- **Dans Docker** : Seul le mode headless fonctionne (pas d'interface graphique)
- **En local** : Tous les modes sont disponibles (UI, headed, headless)

### Prérequis pour les tests E2E

1. **Base de données** : Les migrations doivent être à jour
2. **Application** : L'app doit être accessible sur `http://localhost:8000`
3. **Environnement** : Les variables d'environnement de test doivent être configurées

### Dépannage

- **Erreur "XServer"** : Vous êtes dans Docker, utilisez le mode headless
- **Tests qui échouent** : Vérifiez que l'application est accessible et que la base de données est configurée
- **Timeout** : Augmentez le timeout dans `playwright.config.cjs` si nécessaire
