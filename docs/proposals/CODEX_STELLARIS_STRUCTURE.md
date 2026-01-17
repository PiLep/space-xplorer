# Proposition : Structure Complète du Codex Stellaris

## Vue d'ensemble

Le **Codex Stellaris** est l'encyclopédie collaborative de l'univers Stellar. Il documente tous les éléments de l'univers : planètes, systèmes stellaires, contributeurs, découvertes, missions, et bien plus.

## Structure de Navigation

### Navigation Principale

1. **📚 Accueil** - Page d'accueil avec vue d'ensemble
2. **🪐 Planètes** - Liste et fiches des planètes découvertes
3. **⭐ Systèmes Stellaires** - Documentation des systèmes stellaires
4. **👥 Contributeurs** - Fiches des employés/contributeurs de Stellar
5. **🏢 Organigramme** - Structure organisationnelle de Stellar
6. **🏆 Hall of Fame** - Classements et récompenses

### Catégories d'Articles

#### 1. Planètes (Planets)
- **Fiche individuelle** : Caractéristiques, découvreur, contributions
- **Filtres** : Type, taille, température, atmosphère
- **Statistiques** : Nombre total, planètes nommées, récemment découvertes

#### 2. Systèmes Stellaires (Star Systems)
- **Fiche système** : Étoile centrale, planètes du système, coordonnées
- **Informations** : Type d'étoile, nombre de planètes, découvreur
- **Carte** : Visualisation spatiale du système

#### 3. Contributeurs (Contributors)
- **Fiche contributeur** : Profil, contributions, statistiques personnelles
- **Informations** :
  - Nom, matricule, rôle dans Stellar
  - Nombre de contributions approuvées
  - Planètes découvertes
  - Systèmes explorés
  - Date d'embauche / inscription
  - Département / Division
- **Historique** : Chronologie des contributions

#### 4. Découvertes (Discoveries)
- **Articles spéciaux** : Découvertes majeures, artefacts, anomalies
- **Catégories** : Planètes rares, systèmes inhabituels, phénomènes spatiaux

#### 5. Missions (Missions)
- **Documentation des missions** : Expéditions, explorations, recherches
- **Historique** : Missions passées et à venir

## Sections Spéciales

### 🏢 Organigramme Stellar

**Structure proposée** :

```
Stellar Corporation
├── Direction Générale
│   ├── CEO / Directeur Général
│   └── Conseil d'Administration
├── Division Exploration
│   ├── Équipe Cartographie
│   ├── Équipe Découverte
│   └── Équipe Analyse
├── Division Recherche
│   ├── Laboratoire Planétologie
│   ├── Laboratoire Astrophysique
│   └── Laboratoire Exobiologie
├── Division Communication
│   ├── Service Codex Stellaris
│   ├── Relations Publiques
│   └── Documentation
└── Division Technique
    ├── Ingénierie Spatiale
    ├── Systèmes d'Information
    └── Maintenance
```

**Fonctionnalités** :
- Visualisation hiérarchique interactive
- Fiches individuelles pour chaque poste
- Liens vers les contributeurs
- Historique des changements organisationnels

### 🏆 Hall of Fame

**Catégories de classements** :

1. **Top Découvreurs**
   - Nombre de planètes découvertes
   - Nombre de systèmes explorés
   - Découvertes rares

2. **Top Contributeurs**
   - Nombre de contributions approuvées
   - Qualité des contributions (votes)
   - Articles les plus consultés

3. **Explorateurs Légendaires**
   - Explorateurs ayant fait des découvertes majeures
   - Premiers explorateurs de systèmes
   - Records de distance parcourue

4. **Contributeurs du Mois**
   - Meilleur contributeur mensuel
   - Contribution la plus appréciée
   - Découverte du mois

5. **Récompenses Spéciales**
   - Badges et achievements
   - Titres honorifiques
   - Médailles de service

## Modifications de Base de Données Proposées

### Extension de `codex_entries`

Ajouter un champ `entry_type` pour supporter différents types d'articles :

```php
// Migration à créer
Schema::table('codex_entries', function (Blueprint $table) {
    $table->string('entry_type')->default('planet'); // planet, star_system, contributor, discovery, mission
    $table->string('entry_subtype')->nullable(); // Pour sous-catégories
    $table->json('metadata')->nullable(); // Données spécifiques au type
});
```

### Nouvelle Table : `contributor_profiles`

Pour les fiches de contributeurs :

```php
Schema::create('contributor_profiles', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->foreignUlid('user_id')->unique()->constrained()->onDelete('cascade');
    $table->string('matricule')->unique();
    $table->string('department')->nullable(); // Division/Service
    $table->string('position')->nullable(); // Poste
    $table->text('bio')->nullable(); // Biographie
    $table->date('hire_date')->nullable(); // Date d'embauche
    $table->json('achievements')->nullable(); // Badges, récompenses
    $table->json('stats')->nullable(); // Statistiques personnelles
    $table->timestamps();
});
```

### Nouvelle Table : `organigram_entries`

Pour l'organigramme :

```php
Schema::create('organigram_entries', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->string('name'); // Nom du poste/service
    $table->string('type'); // department, position, team
    $table->foreignUlid('parent_id')->nullable()->constrained('organigram_entries')->onDelete('set null');
    $table->foreignUlid('head_user_id')->nullable()->constrained('users')->onDelete('set null');
    $table->text('description')->nullable();
    $table->integer('level')->default(0); // Niveau hiérarchique
    $table->integer('order')->default(0); // Ordre d'affichage
    $table->json('metadata')->nullable();
    $table->timestamps();
});
```

## Routes Proposées

```php
// Routes principales
Route::prefix('codex')->name('codex.')->group(function () {
    Route::get('/', CodexIndex::class)->name('index');
    
    // Articles par catégorie
    Route::get('/planets', [CodexController::class, 'planets'])->name('planets');
    Route::get('/planets/{id}', [CodexController::class, 'planet'])->name('planet');
    
    Route::get('/star-systems', [CodexController::class, 'starSystems'])->name('star-systems');
    Route::get('/star-systems/{id}', [CodexController::class, 'starSystem'])->name('star-system');
    
    Route::get('/contributors', [CodexController::class, 'contributors'])->name('contributors');
    Route::get('/contributors/{id}', [CodexController::class, 'contributor'])->name('contributor');
    
    // Sections spéciales
    Route::get('/organigram', [CodexController::class, 'organigram'])->name('organigram');
    Route::get('/hall-of-fame', [CodexController::class, 'hallOfFame'])->name('hall-of-fame');
    
    // Découvertes et missions
    Route::get('/discoveries', [CodexController::class, 'discoveries'])->name('discoveries');
    Route::get('/discoveries/{id}', [CodexController::class, 'discovery'])->name('discovery');
    
    Route::get('/missions', [CodexController::class, 'missions'])->name('missions');
    Route::get('/missions/{id}', [CodexController::class, 'mission'])->name('mission');
});
```

## Composants Livewire Proposés

1. **CodexIndex** - Page d'accueil (existant, à adapter)
2. **CodexPlanet** - Fiche planète (existant)
3. **CodexStarSystem** - Fiche système stellaire (à créer)
4. **CodexContributor** - Fiche contributeur (à créer)
5. **CodexOrganigram** - Visualisation organigramme (à créer)
6. **CodexHallOfFame** - Hall of Fame (à créer)
7. **CodexDiscovery** - Fiche découverte (à créer)
8. **CodexMission** - Fiche mission (à créer)

## Prochaines Étapes

1. ✅ Sidebar avec navigation complète
2. ⏳ Créer les migrations pour les nouvelles tables
3. ⏳ Créer les modèles (ContributorProfile, OrganigramEntry)
4. ⏳ Créer les contrôleurs et composants Livewire
5. ⏳ Créer les vues pour chaque type d'article
6. ⏳ Implémenter l'organigramme interactif
7. ⏳ Implémenter le Hall of Fame avec classements

## Notes

- La structure actuelle est centrée sur les planètes, mais peut être étendue progressivement
- Les fiches contributeurs peuvent être créées à partir des utilisateurs existants
- L'organigramme peut être géré par les administrateurs
- Le Hall of Fame peut être calculé automatiquement à partir des contributions

