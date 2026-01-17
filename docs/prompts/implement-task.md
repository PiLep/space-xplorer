# Action: Implement Task

## Description

Cette action permet à l'agent Fullstack Developer d'implémenter un plan de développement. L'agent lit le plan, suit les tâches dans l'ordre défini, et crée le code nécessaire pour réaliser la fonctionnalité.

## Quand Utiliser Cette Action

L'agent Fullstack Developer doit implémenter un plan quand :
- Un plan de développement a été créé et reviewé
- Le plan a été approuvé par l'Architecte
- L'implémentation peut commencer
- Le code doit être créé selon les spécifications du plan

## Processus d'Implémentation

### 1. Préparation

1. **Lire le plan** : Analyser le plan de développement dans `docs/tasks/`
2. **Vérifier la review** : S'assurer que le plan a été reviewé et approuvé
3. **Comprendre le contexte** : Lire l'issue produit associée si nécessaire
4. **Identifier les dépendances** : Vérifier les dépendances techniques

### 2. Implémentation Phase par Phase

Pour chaque phase du plan :

1. **Lire la phase** : Comprendre les tâches de la phase
2. **Implémenter les tâches** : Créer les fichiers et écrire le code
3. **Tester** : Écrire et exécuter les tests
4. **Vérifier** : S'assurer que tout fonctionne
5. **Marquer comme terminé** : Indiquer la progression dans le plan

### 3. Ordre d'Exécution

**Respecter strictement l'ordre défini dans le plan** :
- Les migrations avant les modèles
- Les modèles avant les services
- Les services avant les controllers
- Les controllers avant les routes
- Les tests en parallèle de l'implémentation

## Conventions de Code

### Migrations

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            // ... autres colonnes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planets');
    }
};
```

### Modèles

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Planet extends Model
{
    protected $fillable = [
        'name',
        'type',
        // ... autres champs
    ];

    public function users(): BelongsTo
    {
        return $this->hasMany(User::class, 'home_planet_id');
    }
}
```

### Services

```php
<?php

namespace App\Services;

class PlanetGeneratorService
{
    public function generate(): Planet
    {
        // Logique de génération
    }

    private function selectPlanetType(): string
    {
        // Logique de sélection
    }
}
```

### Controllers

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return response()->json([
            'data' => $user,
            'message' => 'User registered successfully',
            'status' => 'success'
        ], 201);
    }
}
```

### Form Requests

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
```

### Events

```php
<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegistered
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user
    ) {}
}
```

### Listeners

```php
<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\PlanetGeneratorService;

class GenerateHomePlanet
{
    public function __construct(
        private PlanetGeneratorService $planetGenerator
    ) {}

    public function handle(UserRegistered $event): void
    {
        $planet = $this->planetGenerator->generate();
        $event->user->update(['home_planet_id' => $planet->id]);
    }
}
```

## Checklist d'Implémentation

Pour chaque tâche du plan :

### Avant de Commencer

- [ ] J'ai lu et compris la tâche
- [ ] J'ai vérifié les dépendances
- [ ] Je sais quels fichiers créer/modifier

### Pendant l'Implémentation

- [ ] Je crée les fichiers nécessaires
- [ ] J'écris le code selon les conventions
- [ ] Je gère les erreurs correctement
- [ ] Je valide les entrées
- [ ] J'écris des commentaires si nécessaire

### Après l'Implémentation

- [ ] J'écris les tests prévus
- [ ] Je vérifie que le code fonctionne
- [ ] Je formate le code avec Pint
- [ ] Je mets à jour la documentation si nécessaire
- [ ] Je marque la tâche comme terminée dans le plan

## Tests

### Framework de Test

Le projet utilise **Pest** (framework de test moderne pour PHP). Utiliser la syntaxe Pest avec `it()` et `expect()`.

### Structure des Tests Feature

```php
<?php

use App\Models\User;

it('allows user to register successfully', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = $this->postJson('/api/auth/register', $userData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => ['user', 'token'],
            'message',
            'status'
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com'
    ]);
});

it('validates required fields during registration', function () {
    $response = $this->postJson('/api/auth/register', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});
```

### Structure des Tests Unitaires

```php
<?php

use App\Models\Planet;
use App\Services\PlanetGeneratorService;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
    $this->service = new PlanetGeneratorService;
});

it('generates a valid planet with all required fields', function () {
    $planet = $this->service->generate();

    expect($planet)
        ->toBeInstanceOf(Planet::class)
        ->and($planet->id)->not->toBeNull()
        ->and($planet->name)->not->toBeNull()
        ->and($planet->type)->not->toBeNull();
});

it('generates a planet with valid type from configuration', function () {
    $validTypes = ['terrestrial', 'gaseous', 'icy', 'desert', 'oceanic'];
    $planet = $this->service->generate();

    expect($planet->type)->toBeIn($validTypes);
});
```

### Configuration des Tests

Les tests sont configurés dans `tests/Pest.php` :
- **Feature tests** : Utilisent `RefreshDatabase` (migrations complètes)
- **Unit tests** : Utilisent `DatabaseTransactions` (plus rapide)
- **Browser tests** : Utilisent `RefreshDatabase` (migrations complètes)

### Exécution des Tests

**⚠️ IMPORTANT - Optimisation du temps de développement** :

**NE JAMAIS relancer tous les tests** pendant le développement. Exécuter uniquement les tests ajoutés ou modifiés pour gagner du temps (~130-150s pour tous les tests vs quelques secondes pour un fichier).

```bash
# ✅ CORRECT - Exécuter uniquement les tests modifiés/ajoutés (recommandé pendant le dev)
sail artisan test --filter AuthControllerTest
sail artisan test tests/Feature/Api/AuthControllerTest.php
sail artisan test tests/Unit/Services/MyNewServiceTest.php

# Tests en parallèle pour les tests modifiés uniquement
sail artisan test --filter AuthControllerTest --parallel --processes=10

# Tests d'une suite spécifique (si plusieurs fichiers modifiés)
sail artisan test --testsuite=Unit
sail artisan test --testsuite=Feature

# ❌ ÉVITER - Ne pas relancer tous les tests pendant le développement
sail artisan test  # Trop lent (~130-150s), à éviter pendant le dev

# Tests complets uniquement avant commit/PR
sail composer test:fast  # Exécuter tous les tests avant de créer une PR
```

**Stratégie recommandée** :
1. **Pendant le développement** : Exécuter uniquement les tests du fichier modifié/ajouté
2. **Avant commit** : Exécuter la suite complète avec `sail composer test:fast`
3. **Avant PR** : S'assurer que tous les tests passent

**Note** : Toujours utiliser `sail` pour exécuter les tests dans Docker.

### Helpers et Bonnes Pratiques Pest

#### beforeEach() pour la Configuration

```php
beforeEach(function () {
    // Configuration commune pour tous les tests du fichier
    Event::fake();
    $this->service = new PlanetGeneratorService();
});
```

#### Mocking avec Mockery

Pour mocker des services (comme `ImageGenerationService`), utiliser la méthode helper du `TestCase` :

```php
it('creates user with mocked image generation', function () {
    $this->mockImageGenerationService(); // Helper défini dans TestCase
    
    $user = User::factory()->create();
    
    expect($user)->toBeInstanceOf(User::class);
});
```

#### Assertions avec expect()

```php
// Assertions simples
expect($value)->toBe(42);
expect($value)->not->toBeNull();
expect($array)->toHaveCount(3);
expect($string)->toContain('hello');

// Assertions multiples avec and()
expect($planet)
    ->toBeInstanceOf(Planet::class)
    ->and($planet->name)->not->toBeNull()
    ->and($planet->type)->toBeIn(['terrestrial', 'gaseous']);
```

#### Tests de Validation API

```php
it('validates required fields', function () {
    $response = $this->postJson('/api/endpoint', []);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['field1', 'field2']);
});

it('validates email format', function () {
    $response = $this->postJson('/api/endpoint', [
        'email' => 'invalid-email',
    ]);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrorFor('email');
});
```

#### Grouper les Tests avec describe()

```php
describe('PlanetGeneratorService', function () {
    beforeEach(function () {
        $this->service = new PlanetGeneratorService();
    });

    it('generates valid planet', function () {
        // Test ici
    });

    it('generates planet with valid type', function () {
        // Test ici
    });
});
```

#### Tests Browser (E2E avec Playwright)

Les tests Browser sont dans `tests/Browser/` et utilisent Playwright pour les tests end-to-end.

```php
<?php

use Tests\Browser\PlaywrightHelper;

it('completes registration flow end-to-end', function () {
    $helper = new PlaywrightHelper('http://localhost');
    
    $result = $helper->run($helper->createTestScript('registration', function () {
        return <<<'JS'
            await page.goto('/register');
            await page.fill('input[name="name"]', 'John Doe');
            await page.fill('input[name="email"]', 'john@example.com');
            await page.fill('input[name="password"]', 'password123');
            await page.fill('input[name="password_confirmation"]', 'password123');
            await page.click('button[type="submit"]');
            await expect(page).toHaveURL(/\/dashboard/);
        JS;
    }));
    
    expect($result['success'])->toBeTrue();
});
```

**Note** : Les tests Browser nécessitent que l'application soit démarrée. Voir `tests/Browser/README.md` pour plus de détails.

## Formatage du Code

Utiliser Laravel Pint pour formater le code :

```bash
# Formater tout le code
./vendor/bin/sail pint

# Formater un fichier spécifique
./vendor/bin/sail pint app/Services/PlanetGeneratorService.php
```

## Gestion de la Progression

### Dans le Plan

Marquer les tâches comme terminées dans le plan :

```markdown
## Tâches de Développement

### Phase 1 : Modèles et Migrations

#### Tâche 1.1 : Créer la migration pour la table planets
- [x] ✅ Terminée
- **Fichiers créés** : `database/migrations/2024_01_01_000000_create_planets_table.php`
- **Tests** : ✅ Passés

#### Tâche 1.2 : Créer le modèle Planet
- [x] ✅ Terminée
- **Fichiers créés** : `app/Models/Planet.php`
- **Tests** : ✅ Passés
```

### Mise à Jour de l'Historique

Après chaque phase ou tâche importante, mettre à jour la section "Suivi et Historique" du plan :

```markdown
## Suivi et Historique

### Statut

En cours

### Historique

#### 2024-01-17 - Jordan (Fullstack Dev) - Implémentation Phase 1
**Statut** : En cours
**Détails** : Phase 1 terminée (Migrations et Modèles). Toutes les migrations créées et testées. Modèle Planet créé avec relations.
**Fichiers modifiés** : 
- database/migrations/2024_01_01_000000_create_planets_table.php
- database/migrations/2024_01_02_000000_add_home_planet_id_to_users_table.php
- app/Models/Planet.php
**Notes** : Tests unitaires passent. Prêt pour Phase 2.
```

Voir [update-tracking.md](./update-tracking.md) pour le format exact.

## Erreurs Courantes à Éviter

1. **Ne pas respecter l'ordre** : Implémenter les tâches dans le désordre
2. **Oublier les tests** : Ne pas écrire les tests prévus
3. **Ignorer les conventions** : Ne pas suivre les conventions Laravel
4. **Code non formaté** : Oublier de formater avec Pint
5. **Documentation non mise à jour** : Oublier de mettre à jour ARCHITECTURE.md
6. **Gestion d'erreurs manquante** : Ne pas gérer les cas d'erreur
7. **Validation manquante** : Ne pas valider les entrées

## Exemple d'Implémentation Complète

### Étape 1 : Migration

```php
// database/migrations/2024_01_01_000000_create_planets_table.php
Schema::create('planets', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('type');
    $table->string('size');
    $table->string('temperature');
    $table->string('atmosphere');
    $table->string('terrain');
    $table->string('resources');
    $table->text('description')->nullable();
    $table->timestamps();
});
```

### Étape 2 : Modèle

```php
// app/Models/Planet.php
class Planet extends Model
{
    protected $fillable = [
        'name', 'type', 'size', 'temperature',
        'atmosphere', 'terrain', 'resources', 'description'
    ];
}
```

### Étape 3 : Service

```php
// app/Services/PlanetGeneratorService.php
class PlanetGeneratorService
{
    public function generate(): Planet
    {
        $type = $this->selectPlanetType();
        $characteristics = $this->generateCharacteristics($type);
        
        return Planet::create([
            'name' => $this->generateName(),
            'type' => $type,
            ...$characteristics,
            'description' => $this->generateDescription($characteristics),
        ]);
    }
}
```

### Étape 4 : Tests

```php
<?php

// tests/Unit/Services/PlanetGeneratorServiceTest.php
use App\Models\Planet;
use App\Services\PlanetGeneratorService;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
    $this->service = new PlanetGeneratorService();
});

it('generates planet with valid characteristics', function () {
    $planet = $this->service->generate();

    expect($planet)
        ->toBeInstanceOf(Planet::class)
        ->and($planet->name)->not->toBeNull()
        ->and($planet->type)->not->toBeNull();
});
```

## Références

- [Laravel Documentation](https://laravel.com/docs)
- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Architecture du projet
- [STACK.md](../memory_bank/STACK.md) - Stack technique

---

**Rappel** : Implémenter les plans de manière méthodique, tâche par tâche, en respectant l'ordre défini. Écrire du code propre, testé, et conforme aux conventions. Marquer la progression dans le plan au fur et à mesure.

