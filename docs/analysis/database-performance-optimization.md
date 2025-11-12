# Analyse et Optimisations de Performance - Modèle de Données

**Date** : 2025-01-27  
**Auteur** : Analyse automatique du modèle de données

## 📊 Vue d'ensemble du modèle de données

### Tables principales

1. **users** (ULID)
   - Relations : `home_planet_id` → `planets.id`
   - Champs clés : `email` (unique), `home_planet_id` (FK), `is_super_admin`, `email_verified_at`

2. **planets** (ULID)
   - Relations : `users` (hasMany via `home_planet_id`)
   - Champs clés : `name`, `type`, `size`, `temperature`, `atmosphere`, `terrain`, `resources`

3. **resources** (ULID)
   - Relations : `created_by` → `users.id`, `approved_by` → `users.id`
   - Champs clés : `type`, `status`, `tags` (JSON), `created_by`, `approved_by`

## 🔍 Analyse des performances actuelles

### ✅ Points positifs

1. **Foreign keys indexées automatiquement** : MySQL crée automatiquement des index sur les foreign keys
   - `users.home_planet_id` → indexé
   - `resources.created_by` → indexé
   - `resources.approved_by` → indexé

2. **Index composites existants** :
   - `resources` : index sur `['type', 'status']` ✅

3. **Eager loading utilisé** :
   - `UserController::index()` : `with('homePlanet')` ✅
   - `ResourceController::index()` : `with(['creator', 'approver'])` ✅
   - `UserController::getHomePlanet()` : `with('homePlanet')` ✅

### ⚠️ Problèmes identifiés

#### 1. Index manquants sur colonnes fréquemment filtrées

**Impact** : Requêtes lentes sur les filtres

- `planets.name` : Utilisé pour vérifier l'unicité, mais pas d'index dédié
- `planets.type` : Filtré potentiellement, pas d'index
- `planets.created_at` : Utilisé pour `latest()`, pas d'index dédié
- `resources.created_at` : Utilisé pour `latest()`, pas d'index dédié
- `users.created_at` : Utilisé pour `latest()`, pas d'index dédié

#### 2. Index JSON manquant pour recherches de tags

**Impact** : Requêtes `whereJsonContains` très lentes sur grandes tables

- `resources.tags` : Recherches avec `scopeWithMatchingTags()` utilisent `whereJsonContains` sans index JSON
- MySQL 8.0 supporte les index JSON natifs (GENERATED COLUMN + index)

#### 3. Accessors coûteux sans cache

**Impact** : Appels S3 répétés à chaque accès

- `User::avatarUrl` : Vérifie l'existence du fichier S3 à chaque accès
- `Planet::imageUrl` : Vérifie l'existence du fichier S3 à chaque accès
- `Planet::videoUrl` : Vérifie l'existence du fichier S3 à chaque accès
- `Resource::fileUrl` : Vérifie l'existence du fichier S3 à chaque accès

**Problème** : Ces accessors font des appels réseau coûteux (S3) à chaque fois qu'on accède à l'attribut, même si le fichier n'a pas changé.

#### 4. Index composites manquants pour requêtes fréquentes

**Impact** : Requêtes avec plusieurs conditions moins performantes

- `resources` : Requêtes fréquentes sur `['type', 'status', 'created_at']` (liste admin)
- `users` : Requêtes potentielles sur `['is_super_admin', 'created_at']`

#### 5. Pas d'index sur colonnes de tri

**Impact** : `ORDER BY` peut être lent sur grandes tables

- `planets.created_at` : Tri fréquent avec `latest()`
- `resources.created_at` : Tri fréquent avec `latest()`
- `users.created_at` : Tri fréquent avec `latest()`

## 🚀 Recommandations d'optimisation

### Priorité 1 : Index critiques (impact immédiat)

#### 1.1 Index sur `planets.name` pour recherches d'unicité

**Migration** :
```php
Schema::table('planets', function (Blueprint $table) {
    $table->index('name');
});
```

**Bénéfice** : Accélère les vérifications d'unicité lors de la génération de planètes

#### 1.2 Index JSON sur `resources.tags` pour recherches de tags

**Migration** :
```php
Schema::table('resources', function (Blueprint $table) {
    // MySQL 8.0 : Index JSON via generated column
    $table->json('tags_normalized')->virtualAs('JSON_ARRAY(LOWER(JSON_UNQUOTE(JSON_EXTRACT(tags, "$[*]"))))')->nullable();
    $table->index('tags_normalized', 'resources_tags_index');
});
```

**Alternative plus simple** (si MySQL < 8.0 ou problème avec virtual column) :
```php
// Ajouter un index sur la colonne JSON directement (MySQL 8.0+)
DB::statement('ALTER TABLE resources ADD INDEX idx_tags ((CAST(tags AS CHAR(255) ARRAY)))');
```

**Bénéfice** : Accélère considérablement les recherches `whereJsonContains` sur les tags

#### 1.3 Index composites pour requêtes fréquentes

**Migration** :
```php
Schema::table('resources', function (Blueprint $table) {
    // Pour les listes admin filtrées par type et status, triées par date
    $table->index(['type', 'status', 'created_at'], 'resources_type_status_created_index');
});

Schema::table('users', function (Blueprint $table) {
    // Pour les listes admin filtrées par is_super_admin
    $table->index(['is_super_admin', 'created_at'], 'users_admin_created_index');
});
```

**Bénéfice** : Optimise les requêtes de liste avec filtres multiples

### Priorité 2 : Optimisation des accessors (impact moyen)

#### 2.1 Cache des URLs générées

**Stratégie** : Ajouter une colonne `*_url_cached` et mettre à jour lors du changement de `*_path`

**Migration** :
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('avatar_url_cached')->nullable()->after('avatar_url');
});

Schema::table('planets', function (Blueprint $table) {
    $table->string('image_url_cached')->nullable()->after('image_url');
    $table->string('video_url_cached')->nullable()->after('video_url');
});

Schema::table('resources', function (Blueprint $table) {
    $table->string('file_url_cached')->nullable()->after('file_path');
});
```

**Modification des modèles** :
- Ajouter un observer/listener qui met à jour `*_url_cached` quand `*_path` change
- Modifier les accessors pour utiliser `*_url_cached` en priorité
- Vérifier l'existence du fichier seulement si `*_url_cached` est null ou si on force la vérification

**Bénéfice** : Réduit drastiquement les appels S3 (de N appels à 0-1 appel par session)

#### 2.2 Cache Laravel pour les URLs

**Alternative** : Utiliser le cache Laravel (Redis) pour stocker les URLs

**Avantage** : Pas besoin de modifier le schéma
**Inconvénient** : Cache peut expirer, nécessite une stratégie d'invalidation

### Priorité 3 : Index supplémentaires (impact faible mais utile)

#### 3.1 Index sur colonnes de tri

**Migration** :
```php
Schema::table('planets', function (Blueprint $table) {
    $table->index('created_at');
});

Schema::table('resources', function (Blueprint $table) {
    // Déjà couvert par l'index composite si créé
    // Sinon : $table->index('created_at');
});

Schema::table('users', function (Blueprint $table) {
    // Déjà couvert par l'index composite si créé
    // Sinon : $table->index('created_at');
});
```

**Bénéfice** : Accélère les `ORDER BY created_at DESC` sur grandes tables

#### 3.2 Index sur colonnes de filtrage potentiel

**Migration** :
```php
Schema::table('planets', function (Blueprint $table) {
    $table->index('type');
    $table->index('size');
    // Index composite si filtres combinés fréquents
    // $table->index(['type', 'size'], 'planets_type_size_index');
});
```

**Bénéfice** : Prépare le terrain pour les futures fonctionnalités de recherche/filtrage

## 📈 Impact estimé des optimisations

### Avant optimisations

- **Recherche de tags** : O(n) scan complet de la table `resources`
- **Vérification unicité planète** : Scan complet de `planets` par nom
- **Listes admin filtrées** : Scan complet avec filtres en mémoire
- **Accès aux URLs** : N appels S3 par requête (coût réseau élevé)

### Après optimisations

- **Recherche de tags** : O(log n) avec index JSON
- **Vérification unicité planète** : O(log n) avec index sur `name`
- **Listes admin filtrées** : Utilisation d'index composite (très rapide)
- **Accès aux URLs** : 0-1 appel S3 par session (cache)

## 🎯 Plan d'implémentation recommandé

### Phase 1 : Index critiques (1-2h)
1. Index sur `planets.name`
2. Index JSON sur `resources.tags`
3. Index composites pour requêtes fréquentes

### Phase 2 : Cache des URLs (2-3h)
1. Ajouter colonnes `*_url_cached`
2. Créer observers/listeners pour mise à jour automatique
3. Modifier accessors pour utiliser le cache

### Phase 3 : Index supplémentaires (1h)
1. Index sur colonnes de tri
2. Index sur colonnes de filtrage potentiel

## ⚠️ Points d'attention

1. **Index JSON MySQL 8.0** : Vérifier la version MySQL et la syntaxe exacte supportée
2. **Cache des URLs** : Gérer l'invalidation du cache si les fichiers S3 sont supprimés
3. **Taille des index** : Surveiller l'espace disque utilisé par les index supplémentaires
4. **Migration en production** : Tester les migrations sur un environnement de staging d'abord

## 📝 Notes techniques

### MySQL 8.0 et index JSON

MySQL 8.0 supporte les index JSON via :
- **GENERATED COLUMN** : Créer une colonne virtuelle/stored à partir du JSON
- **Fonctionnalité native** : `CAST(tags AS CHAR(255) ARRAY)` pour indexer les tableaux JSON

### Cache des URLs

Deux approches possibles :
1. **Colonne dédiée** : Stocker l'URL dans la base (simple, mais duplication)
2. **Cache Laravel** : Utiliser Redis (plus flexible, mais nécessite Redis)

Recommandation : Colonne dédiée pour la simplicité et la performance.

## 🔗 Références

- [MySQL 8.0 JSON Indexing](https://dev.mysql.com/doc/refman/8.0/en/create-index.html#create-index-functional-key-parts)
- [Laravel Query Optimization](https://laravel.com/docs/queries#database-indexes)
- [Eloquent Performance](https://laravel.com/docs/eloquent#eager-loading)

