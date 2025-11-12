# Analyse des Tests : Performance et Couverture

Date: 2025-01-27

## 📊 Résumé Exécutif

### Couverture de Code Actuelle

| Fichier | Couverture | Lignes Non Couvertes | Priorité |
|---------|-----------|---------------------|----------|
| `GeneratePlanetVideo` | **17.1%** | 72-75, 84-199, 248-324, 76-196, 256-308 | 🔴 CRITIQUE |
| `ResourceGenerationService` | **39.5%** | 48-355, 418-419, 428-429, 438-439, 448-449, 458-459, 486, 495, 517, 522, 535, 598, 604, 62-353 | 🔴 CRITIQUE |
| `Register` (Livewire) | **61.1%** | 42, 58-67 | 🟡 MOYEN |
| `Planet` (Model) | **62.6%** | 86-94, 109-119, 175-183, 198-208, 95, 117, 184, 206 | 🟡 MOYEN |
| `Resource` (Model) | **56.9%** | 97, 107-152, 208, 239, 119-150 | 🟡 MOYEN |
| `User` (Model) | **66.1%** | 109-117, 132-142, 118, 140 | 🟡 MOYEN |
| `GenerateAvatar` | **73.1%** | 64-66, 94-109, 177-192, 209-251, 270-277, 401, 96-104, 187-193, 219-278 | 🟢 ACCEPTABLE |
| `GeneratePlanetImage` | **70.4%** | 78-95, 163-178, 195-237, 257-264, 380, 80-90, 173-179, 205-265 | 🟢 ACCEPTABLE |

## 🚨 Goulots d'Étranglement de Performance

### 1. Tests avec Boucles Multiples (CRITIQUE)

#### `GeneratePlanetVideoTest.php` - Problème Majeur

**Problème** : Plusieurs tests utilisent des boucles jusqu'à **100 itérations** à cause du comportement non-déterministe de `rand()` dans le listener.

**Tests affectés** :
- `generates planet video successfully` : **100 tentatives max** (lignes 58-99)
- `uses template video when available` : **50 tentatives max** (lignes 160-192)
- `generates prompt with planet characteristics` : **50 tentatives max** (lignes 251-282)
- `resets generating status on S3 error` : **20 tentatives max** (lignes 308-326)
- `resets generating status on video generation exception` : **20 tentatives max** (lignes 344-362)
- `resets generating status on generic exception` : **20 tentatives max** (lignes 380-398)

**Impact** :
- Chaque tentative fait `refresh()` et `update()` sur la base de données
- 100 tentatives = **200+ requêtes DB** par test
- Tests skippés actuellement mais code mort présent

**Solution Recommandée** :
```php
// Refactoriser le listener pour injecter une fonction de randomisation
public function __construct(
    private VideoGenerationService $videoGenerator,
    private ?callable $randomFunction = null
) {
    $this->randomFunction = $randomFunction ?? fn() => rand(1, 100);
}

// Dans handle() :
$useTemplate = ($this->randomFunction)() <= 70;
```

#### `PlanetGeneratorServiceTest.php` - Test Statistique Lourd

**Test** : `selects planet types respecting weighted probability` (lignes 99-122)
- **1000 itérations** pour vérifier la distribution statistique
- Pas de requêtes DB mais calculs répétés

**Impact** : Test peut prendre plusieurs secondes

**Solution Recommandée** :
- Réduire à 500 itérations (suffisant pour la précision statistique)
- Ou marquer comme test "slow" avec `@group slow`

### 2. Tests avec Multiples Refresh/Update DB

#### Pattern Problématique Répandu

**Problème** : Beaucoup de tests font plusieurs `refresh()` et `update()` dans des boucles :

```php
for ($i = 0; $i < $maxAttempts; $i++) {
    $planet->refresh();  // Requête DB
    $planet->update([...]);  // Requête DB
    // ...
}
```

**Fichiers affectés** :
- `GeneratePlanetVideoTest.php` : 6 tests avec ce pattern
- Potentiellement d'autres tests avec boucles

**Impact** : Multiplie les requêtes DB inutilement

### 3. Tests avec Création de Données Massives

#### `PlanetGeneratorServiceTest.php`

**Test** : `can generate multiple planets without conflicts` (lignes 158-175)
- Crée **50 planètes** en série
- Chaque création = plusieurs requêtes DB (insert + relations)

**Impact** : Test plus lent mais acceptable pour un test d'intégration

**Recommandation** : Garder mais documenter comme test d'intégration

### 4. Tests avec Mockery `zeroOrMoreTimes()`

**Problème** : Utilisation excessive de `zeroOrMoreTimes()` dans les mocks

**Fichiers affectés** :
- `GeneratePlanetVideoTest.php` : Mocks avec `zeroOrMoreTimes()`
- `tests/Feature/Pest.php` : Mocks globaux avec `zeroOrMoreTimes()`

**Impact** : 
- Moins de validation stricte
- Peut masquer des bugs
- Performance acceptable mais qualité de test réduite

## 📈 Plan d'Amélioration

### Phase 1 : Corrections Critiques (Priorité Haute)

1. **Refactoriser `GeneratePlanetVideo` pour testabilité**
   - Injecter fonction de randomisation
   - Permettre de forcer le choix template/direct
   - **Impact** : Réduire de 100 à 1 tentative par test
   - **Gain estimé** : ~95% de réduction du temps de test

2. **Créer tests unitaires pour `ResourceGenerationService`**
   - Actuellement **0% de tests unitaires**
   - Tester toutes les méthodes publiques
   - **Impact** : Augmenter couverture de 39.5% à ~80%+

3. **Améliorer tests pour `Register` (Livewire)**
   - Tester les cas d'erreur (catch blocks)
   - Tester le flag `isSubmitting`
   - **Impact** : Augmenter couverture de 61.1% à ~85%+

### Phase 2 : Optimisations Performance (Priorité Moyenne)

1. **Réduire boucles dans tests statistiques**
   - `PlanetGeneratorServiceTest` : 1000 → 500 itérations
   - **Gain estimé** : ~50% de réduction du temps

2. **Optimiser refresh/update dans boucles**
   - Utiliser `fresh()` au lieu de `refresh()` quand possible
   - Éviter `update()` inutiles dans les boucles
   - **Gain estimé** : ~30% de réduction des requêtes DB

3. **Remplacer `zeroOrMoreTimes()` par attentes spécifiques**
   - Améliorer la qualité des tests
   - Détecter plus de bugs potentiels

### Phase 3 : Amélioration Couverture (Priorité Basse)

1. **Tester les accessors avec gestion d'erreurs S3**
   - `Planet::imageUrl()` et `videoUrl()`
   - `Resource::fileUrl()`
   - `User::avatarUrl()`
   - Cas d'erreur S3Exception directe

2. **Tester les méthodes helper des Models**
   - `Planet::hasImage()`, `hasVideo()`, etc.
   - `Resource::hasValidFile()`, `isApproved()`, etc.
   - `User::hasAvatar()`, `isAvatarGenerating()`

## 🎯 Métriques Cibles

### Couverture de Code
- **Minimum acceptable** : 70% par fichier
- **Cible** : 80% par fichier
- **Excellence** : 90%+ pour les fichiers critiques

### Performance Tests
- **Temps total de suite** : < 30 secondes
- **Test individuel max** : < 2 secondes
- **Tests "slow"** : < 5 secondes (marqués avec `@group slow`)

### Qualité Tests
- **0 tests skippés** (sauf temporairement)
- **Mocks spécifiques** au lieu de `zeroOrMoreTimes()`
- **Pas de boucles > 10** sans justification

## 📝 Recommandations Immédiates

### Action 1 : Refactoriser GeneratePlanetVideo (URGENT)

**Fichier** : `app/Listeners/GeneratePlanetVideo.php`

**Changement** :
```php
// Avant
$useTemplate = rand(1, 100) <= 70;

// Après
$useTemplate = $this->shouldUseTemplate();
```

**Méthode privée** :
```php
private function shouldUseTemplate(): bool
{
    // En production : comportement aléatoire
    // En test : peut être mocké ou injecté
    return rand(1, 100) <= 70;
}
```

**Ou mieux** : Injecter via constructeur
```php
public function __construct(
    private VideoGenerationService $videoGenerator,
    private ?callable $randomFunction = null
) {
    $this->randomFunction = $randomFunction ?? fn() => rand(1, 100);
}
```

### Action 2 : Créer Tests ResourceGenerationService (URGENT)

**Fichier** : `tests/Unit/Services/ResourceGenerationServiceTest.php` (à créer)

**Tests à ajouter** :
- `generateAvatarTemplate()` - succès
- `generateAvatarTemplate()` - erreur ImageGenerationException
- `generateAvatarTemplate()` - erreur StorageException
- `generatePlanetImageTemplate()` - tous les cas
- `generatePlanetVideoTemplate()` - tous les cas
- `generateAvatarTemplateForResource()` - tous les cas
- `generatePlanetImageTemplateForResource()` - tous les cas
- `generatePlanetVideoTemplateForResource()` - tous les cas
- `extractPlanetTagsFromPrompt()` - différents prompts
- `extractAvatarTagsFromPrompt()` - différents prompts
- `extractNameFromPrompt()` - différents formats
- `detectGenderFromName()` - différents noms

### Action 3 : Optimiser Tests Performance

**Fichier** : `tests/Unit/Services/PlanetGeneratorServiceTest.php`

**Changement** :
```php
// Ligne 101
$iterations = 500; // Réduit de 1000 à 500 (suffisant pour précision)
```

**Fichier** : `tests/Unit/Listeners/GeneratePlanetVideoTest.php`

**Changement** : Après refactoring du listener, remplacer toutes les boucles par des tests directs

## 🔍 Tests à Surveiller

### Tests Lents Actuels
1. `PlanetGeneratorServiceTest::selects planet types respecting weighted probability` - ~1-2s
2. `PlanetGeneratorServiceTest::can generate multiple planets without conflicts` - ~0.5-1s
3. `GeneratePlanetVideoTest` (si activés) - ~5-10s par test avec boucles

### Tests avec Beaucoup de Requêtes DB
1. `GeneratePlanetVideoTest` - 200+ requêtes par test (si activés)
2. Tests avec boucles et `refresh()`/`update()`

## ✅ Checklist d'Implémentation

- [ ] Refactoriser `GeneratePlanetVideo` pour testabilité
- [ ] Mettre à jour tous les tests `GeneratePlanetVideoTest` pour utiliser le nouveau pattern
- [ ] Créer `ResourceGenerationServiceTest` avec tous les cas
- [ ] Améliorer tests `RegisterTest` pour couvrir les erreurs
- [ ] Réduire itérations dans `PlanetGeneratorServiceTest`
- [ ] Optimiser refresh/update dans les boucles
- [ ] Remplacer `zeroOrMoreTimes()` par attentes spécifiques
- [ ] Ajouter tests pour accessors avec erreurs S3
- [ ] Documenter tests "slow" avec `@group slow`
- [ ] Vérifier que tous les tests passent après modifications

## 📚 Références

- [Laravel Testing Best Practices](https://laravel.com/docs/testing)
- [Pest PHP Documentation](https://pestphp.com/docs)
- [Mockery Documentation](https://docs.mockery.io/)





