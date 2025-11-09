# Action: Create Pull Request

## Description

Cette action permet à l'agent Lead Developer (Sam) de créer une Pull Request (PR) vers la branche `develop` après que la fonctionnalité ait été approuvée fonctionnellement par le Product Manager (Alex).

## Quand Utiliser Cette Action

L'agent Lead Developer doit créer une PR quand :
- Le code a été approuvé par Sam (review technique)
- La fonctionnalité a été approuvée par Alex (review fonctionnelle)
- Tous les tests passent
- La documentation est à jour
- Le code est prêt à être mergé dans `develop`

## Prérequis

Avant de créer la PR, s'assurer que :
- ✅ La branche est à jour avec `develop`
- ✅ Tous les tests passent
- ✅ Le code est formaté avec Pint
- ✅ Les commits sont clairs et bien nommés
- ✅ La documentation est à jour

## Format de la Pull Request

### Titre

**Format** : `[ISSUE-XXX] Titre de la fonctionnalité`

**Exemple** : `[ISSUE-001] Implémenter l'inscription utilisateur avec génération de planète`

### Description

```markdown
## Description

{Description courte de la fonctionnalité implémentée}

## Issue Associée

Closes #[numero] ou Fixes #[numero]

[Lien vers l'issue : ISSUE-001-implement-user-registration.md](../docs/issues/ISSUE-001-implement-user-registration.md)

## Plan de Développement

[Lien vers le plan : TASK-001-implement-user-registration.md](../docs/tasks/TASK-001-implement-user-registration.md)

## Changements

### Nouveaux Fichiers

- `app/Models/Planet.php` - Modèle Planet
- `app/Services/PlanetGeneratorService.php` - Service de génération de planètes
- `app/Events/UserRegistered.php` - Événement d'inscription
- `app/Listeners/GenerateHomePlanet.php` - Listener de génération de planète
- `app/Http/Controllers/Api/AuthController.php` - Controller d'authentification
- `app/Http/Requests/RegisterRequest.php` - FormRequest de validation
- `database/migrations/YYYY_MM_DD_create_planets_table.php` - Migration planets
- `database/migrations/YYYY_MM_DD_add_home_planet_id_to_users_table.php` - Migration home_planet_id
- `tests/Feature/UserRegistrationTest.php` - Tests d'intégration
- `tests/Unit/Services/PlanetGeneratorServiceTest.php` - Tests unitaires

### Fichiers Modifiés

- `routes/api.php` - Ajout de la route POST /api/auth/register
- `app/Providers/EventServiceProvider.php` - Enregistrement du listener

## Fonctionnalités

- ✅ Inscription utilisateur avec validation
- ✅ Génération automatique de planète d'origine
- ✅ Authentification via Sanctum
- ✅ Tests complets (unitaires et d'intégration)

## Tests

- [x] Tests unitaires passent
- [x] Tests d'intégration passent
- [x] Tests fonctionnels passent
- [x] Aucune régression détectée

## Checklist

- [x] Le code respecte les conventions Laravel
- [x] Le code est formaté avec Pint
- [x] Les tests sont écrits et passent
- [x] La documentation est mise à jour
- [x] Les migrations sont créées
- [x] Les validations sont en place
- [x] La gestion d'erreurs est correcte
- [x] Review technique approuvée par Sam
- [x] Review fonctionnelle approuvée par Alex

## Screenshots / Démonstration

{Si applicable, ajouter des captures d'écran ou une démonstration}

## Notes

{Notes additionnelles, considérations spéciales, etc.}

## Références

- [Issue #1](../docs/issues/ISSUE-001-implement-user-registration.md)
- [Plan TASK-001](../docs/tasks/TASK-001-implement-user-registration.md)
- [Review Technique](../docs/tasks/CODE-REVIEW-001.md)
- [Review Fonctionnelle](../docs/issues/FUNCTIONAL-REVIEW-001.md)
```

## Exemple de Pull Request

```markdown
## Description

Implémentation de l'inscription utilisateur avec génération automatique d'une planète d'origine. Chaque nouveau joueur reçoit une planète unique générée aléatoirement lors de son inscription.

## Issue Associée

Closes #1

[ISSUE-001-implement-user-registration.md](../docs/issues/ISSUE-001-implement-user-registration.md)

## Plan de Développement

[TASK-001-implement-user-registration.md](../docs/tasks/TASK-001-implement-user-registration.md)

## Changements

### Nouveaux Fichiers

- `app/Models/Planet.php` - Modèle Planet avec toutes les caractéristiques
- `app/Services/PlanetGeneratorService.php` - Service de génération procédurale de planètes
- `app/Events/UserRegistered.php` - Événement dispatché lors de l'inscription
- `app/Listeners/GenerateHomePlanet.php` - Listener qui génère la planète d'origine
- `app/Http/Controllers/Api/AuthController.php` - Controller d'authentification avec endpoint register
- `app/Http/Requests/RegisterRequest.php` - Validation des données d'inscription
- `database/migrations/2024_01_01_000000_create_planets_table.php` - Migration pour la table planets
- `database/migrations/2024_01_02_000000_add_home_planet_id_to_users_table.php` - Migration pour home_planet_id
- `tests/Feature/UserRegistrationTest.php` - Tests d'intégration de l'inscription
- `tests/Unit/Services/PlanetGeneratorServiceTest.php` - Tests unitaires du service

### Fichiers Modifiés

- `routes/api.php` - Ajout de la route POST /api/auth/register
- `app/Providers/EventServiceProvider.php` - Enregistrement du listener GenerateHomePlanet

## Fonctionnalités

- ✅ Formulaire d'inscription avec validation complète
- ✅ Génération automatique d'une planète d'origine unique
- ✅ Authentification via Laravel Sanctum (token)
- ✅ Redirection vers le tableau de bord après inscription
- ✅ Gestion d'erreurs robuste

## Tests

- [x] Tests unitaires passent (15 tests)
- [x] Tests d'intégration passent (8 tests)
- [x] Tests fonctionnels passent (5 tests)
- [x] Aucune régression détectée

## Checklist

- [x] Le code respecte les conventions Laravel
- [x] Le code est formaté avec Pint
- [x] Les tests sont écrits et passent
- [x] La documentation ARCHITECTURE.md est mise à jour
- [x] Les migrations sont créées et testées
- [x] Les validations sont en place (FormRequest)
- [x] La gestion d'erreurs est correcte
- [x] Review technique approuvée par Sam
- [x] Review fonctionnelle approuvée par Alex

## Notes

- La génération de planète est synchrone pour le MVP (peut être async plus tard)
- Le système de poids pour les types de planètes est configuré dans `config/planets.php`
- Les noms de planètes sont générés aléatoirement avec vérification d'unicité

## Références

- [Issue #1](../docs/issues/ISSUE-001-implement-user-registration.md)
- [Plan TASK-001](../docs/tasks/TASK-001-implement-user-registration.md)
```

## Instructions pour l'Agent Lead Developer

Quand tu crées une PR :

1. **Vérifier les prérequis** : S'assurer que tout est prêt
2. **Mettre à jour la branche** : `git pull origin develop` sur la branche feature
3. **Vérifier les tests** : S'assurer que tous les tests passent
4. **Formater le code** : `./vendor/bin/sail pint`
5. **Créer la PR** : Utiliser le format standardisé
6. **Lier les documents** : Référencer l'issue, le plan, et les reviews
7. **Remplir la checklist** : Vérifier tous les points
8. **Ajouter les reviewers** : Assigner Alex et Morgan si nécessaire
9. **Mettre à jour les documents** : Ajouter une entrée dans l'historique de l'issue et du plan

### Mise à Jour des Documents

Après avoir créé la PR :
- **Dans l'issue (ISSUE-XXX)** : Mettre à jour le statut à "En review" et ajouter une entrée dans l'historique avec le lien vers la PR
- **Dans le plan (TASK-XXX)** : Mettre à jour le statut à "Approuvé" et ajouter une entrée dans l'historique

Voir [update-tracking.md](./update-tracking.md) pour le format exact.

## Commandes Git

### Avant de créer la PR

```bash
# S'assurer d'être sur la branche feature
git checkout feature/ISSUE-001-implement-user-registration

# Mettre à jour avec develop
git fetch origin
git rebase origin/develop

# Vérifier que tout fonctionne
./vendor/bin/sail artisan test
./vendor/bin/sail pint

# Commiter si nécessaire
git add .
git commit -m "chore: format code with Pint"

# Pousser la branche
git push origin feature/ISSUE-001-implement-user-registration
```

### Créer la PR

Sur GitHub/GitLab, créer une PR avec :
- **Base** : `develop`
- **Compare** : `feature/ISSUE-001-implement-user-registration`
- **Titre** : `[ISSUE-001] Implémenter l'inscription utilisateur avec génération de planète`
- **Description** : Utiliser le format ci-dessus

## Checklist de PR

- [ ] Branche à jour avec develop
- [ ] Tous les tests passent
- [ ] Code formaté avec Pint
- [ ] Commits clairs et bien nommés
- [ ] Description complète de la PR
- [ ] Liens vers l'issue et le plan
- [ ] Checklist remplie
- [ ] Review technique approuvée
- [ ] Review fonctionnelle approuvée

## Références

- [WORKFLOW.md](../../WORKFLOW.md) - Workflow complet
- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Architecture du projet

---

**Rappel** : En tant qu'agent Lead Developer, tu crées des PRs claires et complètes qui facilitent la review et le merge. Tu t'assures que tout est prêt avant de créer la PR et que tous les documents sont référencés.

