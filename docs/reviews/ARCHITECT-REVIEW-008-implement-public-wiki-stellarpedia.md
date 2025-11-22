# ARCHITECT-REVIEW-008 : Review du plan d'implémentation du Wiki Public Stellarpedia

## Plan Reviewé

[TASK-008-implement-public-wiki-stellarpedia.md](../tasks/TASK-008-implement-public-wiki-stellarpedia.md)

## Statut

⚠️ Approuvé avec recommandations

## Vue d'Ensemble

Le plan est globalement bien structuré et respecte l'architecture définie. L'approche API-first est correctement suivie, l'utilisation d'événements pour la création automatique d'articles est appropriée, et la structure des services est cohérente avec le reste du projet. Quelques recommandations importantes pour améliorer la robustesse, la sécurité, et la cohérence avec les patterns existants.

## Cohérence Architecturale

### ✅ Points Positifs

- **Approche API-first respectée** : Les endpoints API sont définis en premier, les composants Livewire consomment l'API
- **Architecture événementielle appropriée** : Utilisation de listeners sur `PlanetCreated` et `PlanetExplored` pour créer automatiquement les articles wiki
- **Structure des services cohérente** : `WikiService` et `AIDescriptionService` suivent le pattern des services existants (`ImageGenerationService`)
- **Séparation des responsabilités** : Services pour la logique métier, contrôleurs minces, modèles pour les relations
- **Utilisation des FormRequests** : Validation centralisée via FormRequests pour les endpoints API
- **Structure des fichiers respectée** : Tous les fichiers sont bien placés dans la structure du projet

### ⚠️ Points d'Attention

- **Composants Livewire et API** : Le plan mentionne que les composants Livewire utilisent les endpoints API publics. Selon l'architecture hybride documentée dans ARCHITECTURE.md, les composants Livewire devraient utiliser directement les services Laravel plutôt que de passer par l'API. Cette approche est plus performante et plus simple. **Recommandation** : Clarifier que les composants Livewire appellent directement `WikiService` plutôt que les endpoints API.

- **Table wiki_contributions optionnelle** : Le plan mentionne que la table `wiki_contributions` est optionnelle pour le MVP. Si elle n'est pas créée, il faut clarifier comment les contributions sont gérées (modification directe de `wiki_entries.description` ?). **Recommandation** : Soit créer la table dès le départ pour avoir une traçabilité, soit documenter clairement l'approche simplifiée sans table.

### ❌ Problèmes Identifiés

- **ULIDs manquants dans les migrations** : Le plan mentionne "id ULID" dans la tâche 1.1 mais ne précise pas explicitement l'utilisation de `$table->ulid('id')->primary()` dans les migrations. Toutes les tables métier doivent utiliser des ULIDs selon l'architecture. **Action requise** : Vérifier que les migrations utilisent bien `$table->ulid('id')->primary()` pour `wiki_entries` et `wiki_contributions`.

- **Foreign keys avec ULIDs** : Les foreign keys (`planet_id`, `discovered_by_user_id`, `contributor_user_id`) doivent être de type `ulid()` et non `unsignedBigInteger()`. **Action requise** : S'assurer que toutes les foreign keys vers `planets` et `users` utilisent `$table->ulid('column_name')`.

## Qualité Technique

### Choix Techniques

- **Service AIDescriptionService** : ✅ Validé
  - Pattern similaire à `ImageGenerationService` : excellente cohérence
  - Gestion des erreurs, retry, cache : approche robuste
  - Configuration dans `config/text-generation.php` : standard Laravel

- **Service WikiService** : ✅ Validé
  - Encapsulation de la logique métier : bon choix
  - Méthodes bien définies : structure claire
  - Validation des noms centralisée : bonne pratique

- **Listeners sur événements** : ✅ Validé
  - Découplage de la logique : excellent
  - Création automatique d'articles : approche événementielle appropriée

- **FormRequests pour validation** : ✅ Validé
  - Respect des bonnes pratiques Laravel
  - Validation centralisée et réutilisable

### Structure & Organisation

- **Structure** : ✅ Cohérente
  - Les phases sont logiques et bien ordonnées
  - Les dépendances sont clairement identifiées
  - L'ordre d'exécution est cohérent

- **Estimation** : ⚠️ À vérifier
  - Estimation totale : ~20h semble raisonnable
  - La génération IA peut être plus longue que prévu (dépend de l'API externe)
  - **Recommandation** : Prévoir un buffer pour les tâches liées à l'IA

### Dépendances

- **Dépendances** : ✅ Bien gérées
  - L'ordre d'exécution est clair
  - Les prérequis sont bien identifiés
  - Les phases sont séquentielles et logiques

## Performance & Scalabilité

### Points Positifs

- **Index sur les colonnes de recherche** : Le plan mentionne des index sur `name` et `fallback_name` dans les notes techniques
- **Pagination Laravel** : Utilisation de la pagination standard pour les listes
- **Cache des descriptions IA** : Mise en cache prévue pour éviter les régénérations
- **Eager loading** : Mentionné dans les notes techniques pour optimiser les requêtes

### Recommandations

- **Index composite pour la recherche** : ⚠️ Recommandation Medium Priority
  - **Problème** : La recherche par nom/fallback_name pourrait bénéficier d'un index composite ou d'un index full-text selon le volume de données
  - **Suggestion** : Ajouter un index full-text sur `name` et `fallback_name` si MySQL le supporte, ou un index composite selon les patterns de recherche
  - **Impact** : Performance de recherche améliorée avec beaucoup d'articles

- **Index sur discovered_by_user_id** : ⚠️ Recommandation Medium Priority
  - **Problème** : Pas d'index mentionné sur `discovered_by_user_id` qui sera utilisé pour filtrer par découvreur
  - **Suggestion** : Ajouter un index sur `discovered_by_user_id` dans la migration
  - **Impact** : Performance améliorée pour les requêtes filtrant par découvreur

- **Cache de la liste des planètes** : ⚠️ Recommandation Low Priority
  - **Problème** : La liste des planètes récemment découvertes et les plus consultées pourrait bénéficier d'un cache
  - **Suggestion** : Prévoir un cache Redis pour les listes fréquemment accédées (TTL de 5-10 minutes)
  - **Impact** : Réduction de la charge sur la base de données pour les pages publiques

- **Génération IA asynchrone** : ⚠️ Recommandation High Priority
  - **Problème** : La génération de description IA dans les listeners pourrait bloquer la création de planète si synchrone
  - **Suggestion** : Considérer l'utilisation d'une queue pour la génération IA (comme pour `GeneratePlanetImage` et `GeneratePlanetVideo`)
  - **Impact** : Performance améliorée, pas de blocage lors de la création de planète
  - **Note** : Pour le MVP, synchrone peut être acceptable, mais prévoir l'évolution vers asynchrone

## Sécurité

### Validations

- ✅ **Validations prévues** : FormRequests avec règles appropriées
  - Validation de nom : longueur, caractères autorisés, unicité, mots interdits
  - Validation de contribution : longueur min/max, mots interdits
  - Validation côté serveur : toujours effectuée

### Authentification & Autorisation

- ✅ **Gestion correcte** : Utilisation de Sanctum pour les routes authentifiées
  - Routes publiques : pas d'authentification requise (lecture seule)
  - Routes authentifiées : middleware `auth:sanctum` pour nommage et contribution
  - Vérification des permissions : `canUserNamePlanet()` et `canUserContribute()` dans le service

### Données Publiques

- ✅ **Protection des données sensibles** : Mentionné dans les notes techniques
  - Pas d'emails exposés
  - Pas d'IDs utilisateurs complets exposés
  - Seulement les données nécessaires pour l'affichage public

### Recommandations Sécurité

- **Rate limiting sur les endpoints publics** : ⚠️ Recommandation Medium Priority
  - **Problème** : Les endpoints publics (`GET /api/wiki/planets`, `GET /api/wiki/search`) pourraient être sujets à des abus
  - **Suggestion** : Ajouter du rate limiting sur les endpoints de recherche et de liste (ex: 60 requêtes/minute par IP)
  - **Impact** : Protection contre les abus et le scraping

- **Rate limiting sur le nommage** : ⚠️ Recommandation Medium Priority
  - **Problème** : Un utilisateur pourrait tenter de nommer plusieurs planètes rapidement
  - **Suggestion** : Ajouter du rate limiting sur `POST /api/wiki/planets/{id}/name` (ex: 5 tentatives/minute par utilisateur)
  - **Impact** : Protection contre les abus et les tentatives de spam

- **Validation des mots interdits** : ⚠️ Recommandation High Priority
  - **Problème** : Le plan mentionne une liste de mots interdits mais ne détaille pas comment elle est gérée
  - **Suggestion** : 
    - Créer une configuration `config/wiki.php` avec une liste de mots interdits
    - Utiliser une validation case-insensitive
    - Prévoir une liste extensible (facile à mettre à jour)
  - **Impact** : Protection contre les contenus inappropriés

## Tests

### Couverture

- ✅ **Tests complets prévus** : Tests unitaires, d'intégration, et fonctionnels
  - Tests unitaires pour les services
  - Tests d'intégration pour les listeners et endpoints API
  - Tests fonctionnels pour les composants Livewire

### Recommandations

- **Tests de performance pour la recherche** : ⚠️ Recommandation Medium Priority
  - **Problème** : Le plan mentionne "Tests de performance pour la recherche" mais ne détaille pas les critères
  - **Suggestion** : Définir des critères de performance (ex: recherche < 500ms avec 10 000 articles)
  - **Impact** : Assurance que la recherche reste performante à l'échelle

- **Tests de génération IA avec mocks** : ✅ Déjà prévu
  - Tests avec mocks de l'API IA : bonne pratique
  - Tests de gestion d'erreurs et retry : important

- **Tests de validation des noms** : ⚠️ Recommandation Medium Priority
  - **Problème** : Le plan mentionne les tests mais ne détaille pas tous les cas limites
  - **Suggestion** : Tester tous les cas de validation :
    - Noms valides (différentes longueurs, caractères autorisés)
    - Noms invalides (trop courts, trop longs, caractères interdits)
    - Unicité (collision de noms)
    - Mots interdits (différentes casse, avec accents)
  - **Impact** : Assurance que la validation fonctionne correctement dans tous les cas

## Documentation

### Mise à Jour

- ✅ **Documentation prévue** : Mise à jour de ARCHITECTURE.md prévue
  - Documentation des nouveaux endpoints
  - Documentation des modèles
  - Documentation des services

### Recommandations

- **Documentation des règles de validation** : ⚠️ Recommandation Low Priority
  - **Problème** : Les règles de validation des noms sont dans `config/wiki.php` mais pourraient bénéficier d'une documentation
  - **Suggestion** : Ajouter des commentaires dans le fichier de configuration expliquant chaque règle
  - **Impact** : Facilité de maintenance et compréhension

## Recommandations Spécifiques

### Recommandation 1 : Clarifier l'utilisation des services par Livewire

**Problème** : Le plan mentionne que les composants Livewire utilisent les endpoints API publics. Selon l'architecture hybride documentée dans ARCHITECTURE.md, les composants Livewire devraient utiliser directement les services Laravel plutôt que de passer par l'API.

**Impact** : Performance améliorée, simplicité accrue, cohérence avec l'architecture existante

**Suggestion** : 
- Modifier la tâche 6.1 et 6.2 pour préciser que les composants Livewire appellent directement `WikiService` et `AIDescriptionService`
- Les endpoints API restent disponibles pour les clients externes (applications mobiles, SPAs distants)
- Les composants Livewire utilisent `app(WikiService::class)` ou injection de dépendance

**Priorité** : High

**Section concernée** : Phase 6 - Frontend - Composants Livewire

### Recommandation 2 : Utiliser des ULIDs dans les migrations

**Problème** : Le plan mentionne "id ULID" mais ne précise pas explicitement l'utilisation de `$table->ulid('id')->primary()` dans les migrations. Toutes les tables métier doivent utiliser des ULIDs selon l'architecture.

**Impact** : Cohérence architecturale, sécurité améliorée, URLs-friendly

**Suggestion** : 
- Modifier la tâche 1.1 pour préciser : `$table->ulid('id')->primary()`
- Modifier la tâche 1.2 pour préciser : `$table->ulid('id')->primary()`
- Modifier toutes les foreign keys pour utiliser `$table->ulid('column_name')` au lieu de `unsignedBigInteger()`

**Priorité** : High

**Section concernée** : Phase 1 - Modèle de Données et Migrations

### Recommandation 3 : Génération IA asynchrone

**Problème** : La génération de description IA dans les listeners pourrait bloquer la création de planète si synchrone.

**Impact** : Performance améliorée, pas de blocage lors de la création de planète

**Suggestion** : 
- Considérer l'utilisation d'une queue pour la génération IA (comme pour `GeneratePlanetImage` et `GeneratePlanetVideo`)
- Pour le MVP, synchrone peut être acceptable, mais prévoir l'évolution vers asynchrone
- Ajouter une tâche optionnelle pour l'évolution vers asynchrone si nécessaire

**Priorité** : High (pour l'évolution future)

**Section concernée** : Phase 2 - Service de Génération IA, Phase 4 - Événements & Listeners

### Recommandation 4 : Index de performance

**Problème** : Certains index ne sont pas mentionnés explicitement dans les migrations.

**Impact** : Performance de recherche améliorée avec beaucoup d'articles

**Suggestion** : 
- Ajouter un index sur `discovered_by_user_id` dans la migration `wiki_entries`
- Considérer un index full-text sur `name` et `fallback_name` pour la recherche (si MySQL le supporte)
- Ajouter un index sur `wiki_entry_id` dans `wiki_contributions` (déjà prévu implicitement via foreign key)

**Priorité** : Medium

**Section concernée** : Phase 1 - Modèle de Données et Migrations, Notes Techniques

### Recommandation 5 : Rate limiting

**Problème** : Les endpoints publics et le nommage pourraient être sujets à des abus.

**Impact** : Protection contre les abus et le scraping

**Suggestion** : 
- Ajouter du rate limiting sur les endpoints de recherche et de liste (ex: 60 requêtes/minute par IP)
- Ajouter du rate limiting sur `POST /api/wiki/planets/{id}/name` (ex: 5 tentatives/minute par utilisateur)
- Documenter le rate limiting dans les routes

**Priorité** : Medium

**Section concernée** : Phase 5 - API Endpoints

### Recommandation 6 : Clarifier la gestion des contributions

**Problème** : Le plan mentionne que la table `wiki_contributions` est optionnelle pour le MVP, mais ne précise pas comment les contributions sont gérées sans cette table.

**Impact** : Clarification de l'approche MVP

**Suggestion** : 
- Soit créer la table dès le départ pour avoir une traçabilité (recommandé)
- Soit documenter clairement l'approche simplifiée : modifications directes sur `wiki_entries.description` avec historique dans un champ JSON ou log
- Si approche simplifiée, prévoir l'évolution vers la table `wiki_contributions` dans une version future

**Priorité** : Medium

**Section concernée** : Phase 1 - Modèle de Données et Migrations

### Recommandation 7 : Validation des mots interdits

**Problème** : Le plan mentionne une liste de mots interdits mais ne détaille pas comment elle est gérée.

**Impact** : Protection contre les contenus inappropriés

**Suggestion** : 
- Créer une configuration `config/wiki.php` avec une liste de mots interdits (tableau)
- Utiliser une validation case-insensitive
- Prévoir une liste extensible (facile à mettre à jour)
- Documenter comment ajouter de nouveaux mots interdits

**Priorité** : High

**Section concernée** : Phase 3 - Service Wiki, Tâche 3.2

## Modifications Demandées

Aucune modification majeure demandée. Le plan peut être approuvé avec les recommandations ci-dessus. Les modifications suggérées sont principalement des clarifications et des améliorations, pas des blocages.

## Questions & Clarifications

- **Question 1** : Les composants Livewire doivent-ils utiliser directement les services Laravel ou passer par l'API ? 
  - **Impact** : Clarification de l'architecture hybride
  - **Réponse attendue** : Confirmation que Livewire utilise directement les services (cohérent avec ARCHITECTURE.md)

- **Question 2** : La génération IA doit-elle être synchrone ou asynchrone pour le MVP ?
  - **Impact** : Performance et complexité
  - **Réponse attendue** : Synchrone acceptable pour MVP, mais prévoir l'évolution vers asynchrone

- **Question 3** : La table `wiki_contributions` doit-elle être créée dès le MVP ou peut-elle être omise ?
  - **Impact** : Traçabilité et évolution future
  - **Réponse attendue** : Recommandation de créer la table dès le départ pour la traçabilité

## Conclusion

Le plan est approuvé avec plusieurs recommandations importantes pour améliorer la robustesse, la sécurité, et la cohérence avec l'architecture existante. Les principales recommandations concernent :

1. **Clarification de l'utilisation des services par Livewire** (High Priority)
2. **Utilisation explicite des ULIDs dans les migrations** (High Priority)
3. **Génération IA asynchrone** (High Priority pour l'évolution future)
4. **Rate limiting** (Medium Priority)
5. **Index de performance** (Medium Priority)
6. **Clarification de la gestion des contributions** (Medium Priority)
7. **Validation des mots interdits** (High Priority)

Le plan peut être implémenté tel quel, en tenant compte des recommandations. Les modifications suggérées sont principalement des améliorations et des clarifications, pas des blocages.

**Prochaines étapes** :
1. Clarifier l'utilisation des services par Livewire (recommandation 1)
2. Vérifier l'utilisation des ULIDs dans les migrations (recommandation 2)
3. Implémenter le plan en suivant les recommandations
4. Ajouter le rate limiting et les index de performance
5. Documenter la validation des mots interdits

## Références

- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Architecture technique, modèle de données, API endpoints
- [STACK.md](../memory_bank/STACK.md) - Stack technique
- [EVENTS.md](../EVENTS.md) - Documentation des événements (PlanetCreated, PlanetExplored)
- [TASK-008-implement-public-wiki-stellarpedia.md](../tasks/TASK-008-implement-public-wiki-stellarpedia.md) - Plan de développement

