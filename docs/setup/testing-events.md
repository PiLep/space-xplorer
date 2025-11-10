# Tester les Événements avec Listeners en Queue

Ce document explique comment tester correctement les événements Laravel qui déclenchent des listeners implémentant `ShouldQueue`, en particulier dans le contexte du CI/CD.

## Problème Rencontré

### Symptômes

Les tests échouaient dans le CI GitHub avec des erreurs indiquant que :
- La génération de planète ne se lançait pas
- Les événements ne se dépilaient pas
- Les tests vérifiant `home_planet_id` échouaient car la valeur était `null`

### Cause Racine

Le problème venait de l'utilisation de `Queue::fake()` dans les tests Feature :

```php
// ❌ MAUVAISE APPROCHE
beforeEach(function () {
    Queue::fake(); // Bloque l'exécution des listeners ShouldQueue même avec QUEUE_CONNECTION=sync
    // ...
});
```

**Pourquoi c'est problématique ?**

1. `Queue::fake()` intercepte **tous** les jobs/listeners qui implémentent `ShouldQueue`
2. Même avec `QUEUE_CONNECTION=sync`, les listeners ne s'exécutent pas réellement
3. Les événements sont "fake" mais jamais traités, donc les effets de bord ne se produisent pas
4. Les tests échouent car ils attendent des résultats qui ne sont jamais générés

### Architecture des Événements

Dans Space Xplorer, nous avons cette chaîne d'événements :

```
UserRegistered (Event)
  ├─> GenerateHomePlanet (Listener synchrone)
  │     └─> PlanetGeneratorService::generate()
  │           └─> PlanetCreated (Event)
  │                 ├─> GeneratePlanetImage (Listener ShouldQueue)
  │                 └─> GeneratePlanetVideo (Listener ShouldQueue)
  └─> GenerateAvatar (Listener ShouldQueue)
```

**Listeners synchrones** : S'exécutent immédiatement
- `GenerateHomePlanet` : Génère la planète d'origine

**Listeners en queue** : S'exécutent via la queue (ou synchrone si `QUEUE_CONNECTION=sync`)
- `GenerateAvatar` : Génère l'avatar utilisateur
- `GeneratePlanetImage` : Génère l'image de la planète
- `GeneratePlanetVideo` : Génère la vidéo de la planète

## Solution Implémentée

### Principe

Au lieu de fake la queue, on **mock directement les services** qui font des appels externes, tout en permettant l'exécution réelle des listeners.

### Configuration des Tests Feature

**Fichier** : `tests/Feature/Pest.php`

```php
<?php

use App\Services\ImageGenerationService;
use App\Services\VideoGenerationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Mock ImageGenerationService et VideoGenerationService automatiquement pour tous les tests Feature.
 *
 * IMPORTANT: On n'utilise PAS Queue::fake() car cela empêche l'exécution des listeners ShouldQueue
 * même avec QUEUE_CONNECTION=sync. À la place, on mock directement les services pour éviter
 * les appels API réels, tout en permettant l'exécution synchrone des listeners.
 *
 * PROTECTION: Http::preventStrayRequests() bloque tous les appels HTTP non mockés pour éviter
 * les timeouts et les appels externes accidentels dans les tests.
 */
beforeEach(function () {
    // Bloquer tous les appels HTTP non mockés pour éviter les timeouts et appels externes
    Http::preventStrayRequests();

    Storage::fake('s3');

    // Mock ImageGenerationService pour éviter les appels API réels
    $mockImageGenerator = \Mockery::mock(ImageGenerationService::class);
    $mockImageGenerator->shouldReceive('generate')
        ->zeroOrMoreTimes()
        ->with(\Mockery::any(), \Mockery::any(), \Mockery::any())
        ->andReturnUsing(function ($prompt, $provider, $subfolder) {
            $folder = $subfolder ?? 'generated';
            $filename = 'test-'.uniqid().'.png';
            $path = "images/generated/{$folder}/{$filename}";

            return [
                'url' => "https://s3.example.com/{$path}",
                'path' => $path,
                'disk' => 's3',
                'provider' => $provider ?? 'openai',
            ];
        });

    $this->app->instance(ImageGenerationService::class, $mockImageGenerator);

    // Mock VideoGenerationService pour éviter les appels API réels
    $mockVideoGenerator = \Mockery::mock(VideoGenerationService::class);
    $mockVideoGenerator->shouldReceive('generate')
        ->zeroOrMoreTimes()
        ->with(\Mockery::any(), \Mockery::any(), \Mockery::any())
        ->andReturnUsing(function ($prompt, $provider, $subfolder) {
            $folder = $subfolder ?? 'generated';
            $filename = 'test-'.uniqid().'.mp4';
            $path = "videos/generated/{$folder}/{$filename}";

            return [
                'url' => "https://s3.example.com/{$path}",
                'path' => $path,
                'disk' => 's3',
                'provider' => $provider ?? 'openai',
            ];
        });

    $this->app->instance(VideoGenerationService::class, $mockVideoGenerator);
});

afterEach(function () {
    \Mockery::close();
});
```

### Configuration PHPUnit

**Fichier** : `phpunit.xml`

```xml
<php>
    <env name="QUEUE_CONNECTION" value="sync"/>
    <!-- ... autres variables ... -->
</php>
```

**Timeouts pour éviter les blocages** :

```xml
<phpunit
    timeoutForSmallTests="5"
    timeoutForMediumTests="10"
    timeoutForLargeTests="30"
>
```

### Configuration CI

**Fichier** : `.github/workflows/ci.yml`

```yaml
- name: Run Tests with Coverage
  timeout-minutes: 15  # Timeout de sécurité
  env:
    QUEUE_CONNECTION: sync  # Exécution synchrone des listeners
    # ... autres variables ...
  run: ./vendor/bin/pest --coverage-clover=coverage.xml --coverage-html=coverage --log-junit=junit.xml
```

### TestServiceProvider pour E2E

**Fichier** : `app/Providers/TestServiceProvider.php`

Le `TestServiceProvider` mock également les services pour les tests E2E qui ne passent pas par `tests/Feature/Pest.php` :

```php
public function register(): void
{
    $isTesting = $this->app->environment('testing');
    $hasApiKey = ! empty(config('image-generation.providers.openai.api_key'));

    if (! $isTesting && $hasApiKey) {
        return;
    }

    // Bloquer tous les appels HTTP non mockés
    Http::preventStrayRequests();

    // Mock des services...
}
```

## Protections Mises en Place

### 1. Blocage des Appels HTTP Externes

**`Http::preventStrayRequests()`** :
- Bloque **tous** les appels HTTP non mockés
- Les tests échouent immédiatement si un appel externe est tenté
- Évite les timeouts et les appels accidentels

### 2. Mock des Services Externes

**Services mockés** :
- `ImageGenerationService` → Retourne des données mockées instantanément
- `VideoGenerationService` → Retourne des données mockées instantanément
- `Storage::fake('s3')` → Pas d'appels S3 réels

### 3. Timeouts de Sécurité

**Niveaux de protection** :
- **PHPUnit** : Timeouts par taille de test (5s, 10s, 30s)
- **CI GitHub Actions** : Timeout global de 15 minutes sur la step de tests
- **Playwright** : Timeouts configurés dans `playwright.config.cjs`

## Bonnes Pratiques

### ✅ À FAIRE

1. **Mock les services externes** au lieu de fake la queue
   ```php
   $mock = \Mockery::mock(ExternalService::class);
   $this->app->instance(ExternalService::class, $mock);
   ```

2. **Utiliser `QUEUE_CONNECTION=sync`** pour les tests
   - Permet l'exécution synchrone des listeners
   - Les événements sont traités immédiatement
   - Les tests peuvent vérifier les résultats

3. **Bloquer les appels HTTP non mockés**
   ```php
   Http::preventStrayRequests();
   ```

4. **Ajouter des timeouts** pour éviter les blocages
   - Au niveau PHPUnit
   - Au niveau CI/CD

5. **Mock dans `beforeEach`** pour tous les tests Feature
   - Configuration centralisée
   - Cohérence entre tous les tests

### ❌ À ÉVITER

1. **Ne pas utiliser `Queue::fake()`** si vous avez besoin que les listeners s'exécutent
   ```php
   // ❌ MAUVAIS
   Queue::fake();
   ```

2. **Ne pas faire d'appels API réels** dans les tests
   ```php
   // ❌ MAUVAIS - Va faire un vrai appel API
   $service->generate('prompt');
   ```

3. **Ne pas oublier les timeouts** dans le CI
   ```yaml
   # ❌ MAUVAIS - Peut bloquer indéfiniment
   - name: Run Tests
     run: ./vendor/bin/pest
   ```

## Exemple de Test

```php
it('creates user with home planet during registration', function () {
    Livewire::test(\App\Livewire\Register::class)
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertRedirect(route('dashboard'));

    // Vérifier que l'utilisateur a été créé avec une planète
    $user = Auth::user();
    expect($user)->not->toBeNull()
        ->and($user->home_planet_id)->not->toBeNull()
        ->and($user->homePlanet)->not->toBeNull();
});
```

**Ce qui se passe** :
1. L'utilisateur s'inscrit via Livewire
2. L'événement `UserRegistered` est dispatché
3. Le listener `GenerateHomePlanet` s'exécute **synchrone** (grâce à `QUEUE_CONNECTION=sync`)
4. La planète est créée et assignée à l'utilisateur
5. L'événement `PlanetCreated` est dispatché
6. Les listeners `GeneratePlanetImage` et `GeneratePlanetVideo` s'exécutent **synchrone** mais avec les services mockés
7. Le test peut vérifier que `home_planet_id` n'est pas null

## Résumé

| Aspect | Approche |
|--------|----------|
| **Queue** | `QUEUE_CONNECTION=sync` (pas de fake) |
| **Services externes** | Mock avec Mockery |
| **Appels HTTP** | `Http::preventStrayRequests()` |
| **Storage** | `Storage::fake('s3')` |
| **Timeouts** | PHPUnit + CI/CD |
| **Configuration** | `beforeEach` dans `tests/Feature/Pest.php` |

## Références

- [Configuration des Queues](./queues-setup.md)
- [Laravel Events & Listeners](https://laravel.com/docs/events)
- [Laravel HTTP Client Testing](https://laravel.com/docs/http-client#testing)
- [Mockery Documentation](https://docs.mockery.io/)

