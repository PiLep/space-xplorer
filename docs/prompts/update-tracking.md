# Guide: Mise à Jour et Suivi des Issues et Tasks

## Principe

Les issues (`docs/issues/`) et les tasks (`docs/tasks/`) doivent être mises à jour tout au long du workflow pour :
- **Suivre la progression** : Savoir où en est chaque fonctionnalité
- **Maintenir un historique** : Comprendre l'évolution et les décisions
- **Faciliter la collaboration** : Chaque agent sait ce qui a été fait

## Format Standardisé

### Section à Ajouter dans les Documents

Chaque issue et task doit avoir une section **"Suivi et Historique"** à la fin du document :

```markdown
## Suivi et Historique

### Statut

[À faire | En cours | En review | Approuvé | Terminé]

### Historique

#### [Date] - [Agent] - [Action]
**Statut** : [Nouveau statut]
**Détails** : Description de ce qui a été fait ou changé
**Fichiers modifiés** : [Si applicable]
**Notes** : [Notes additionnelles]

#### [Date] - [Agent] - [Action]
...
```

## Quand Mettre à Jour

### Issue (docs/issues/)

| Agent | Quand Mettre à Jour | Ce qui doit être mis à jour |
|-------|---------------------|----------------------------|
| **Alex** | Lors de la création | Créer la section "Suivi et Historique" avec statut "À faire" |
| **Sam** | Après création du plan | Mettre à jour le statut à "En cours", ajouter une entrée |
| **Sam** | Après review du code | Mettre à jour le statut, ajouter une entrée |
| **Alex** | Après review fonctionnelle | Mettre à jour le statut, ajouter une entrée |
| **Sam** | Après création de la PR | Mettre à jour le statut à "En review", ajouter une entrée |
| **Alex** | Après merge de la PR | Mettre à jour le statut à "Terminé", ajouter une entrée finale |

### Task (docs/tasks/)

| Agent | Quand Mettre à Jour | Ce qui doit être mis à jour |
|-------|---------------------|----------------------------|
| **Sam** | Lors de la création | Créer la section "Suivi et Historique" avec statut "À faire" |
| **Morgan** | Après review architecturale | Mettre à jour le statut, ajouter une entrée avec les recommandations |
| **Jordan** | Pendant l'implémentation | Marquer les tâches comme terminées, mettre à jour le statut global |
| **Sam** | Après review du code | Mettre à jour le statut, ajouter une entrée |
| **Alex** | Après review fonctionnelle | Mettre à jour le statut, ajouter une entrée |
| **Sam** | Après création de la PR | Mettre à jour le statut à "Terminé", ajouter une entrée finale |

## Exemples de Mises à Jour

### Exemple 1 : Mise à jour lors de la création du plan

```markdown
## Suivi et Historique

### Statut

En cours

### Historique

#### 2024-01-15 - Sam (Lead Dev) - Création du plan
**Statut** : En cours
**Détails** : Plan de développement créé. Le plan décompose l'issue en 4 phases avec 12 tâches au total.
**Fichiers modifiés** : docs/tasks/TASK-001-implement-user-registration.md
**Notes** : Estimation totale : ~8h de développement
```

### Exemple 2 : Mise à jour lors de la review architecturale

```markdown
#### 2024-01-16 - Morgan (Architect) - Review architecturale
**Statut** : En cours
**Détails** : Plan reviewé et approuvé avec recommandations. Voir REVIEW-001 pour les détails.
**Fichiers modifiés** : docs/tasks/TASK-001-implement-user-registration.md (section Review Architecturale ajoutée)
**Notes** : Recommandations mineures appliquées. Le plan peut être implémenté.
```

### Exemple 3 : Mise à jour pendant l'implémentation

```markdown
#### 2024-01-17 - Jordan (Fullstack Dev) - Implémentation Phase 1
**Statut** : En cours
**Détails** : Phase 1 terminée (Migrations et Modèles). Toutes les migrations créées et testées. Modèle Planet créé avec relations.
**Fichiers modifiés** : 
- database/migrations/2024_01_01_000000_create_planets_table.php
- database/migrations/2024_01_02_000000_add_home_planet_id_to_users_table.php
- app/Models/Planet.php
**Notes** : Tests unitaires passent. Prêt pour Phase 2.
```

### Exemple 4 : Mise à jour après review fonctionnelle

```markdown
#### 2024-01-20 - Alex (Product) - Review fonctionnelle
**Statut** : Approuvé fonctionnellement
**Détails** : Fonctionnalité testée et approuvée. Tous les critères d'acceptation sont respectés. Quelques ajustements mineurs suggérés (voir FUNCTIONAL-REVIEW-001).
**Fichiers modifiés** : docs/issues/ISSUE-001-implement-user-registration.md
**Notes** : Ajustements suggérés sont optionnels. La fonctionnalité peut être mergée.
```

### Exemple 5 : Mise à jour finale après PR

```markdown
#### 2024-01-21 - Sam (Lead Dev) - Pull Request créée
**Statut** : En review
**Détails** : Pull Request #42 créée vers develop. Tous les tests passent. Code approuvé techniquement et fonctionnellement.
**Fichiers modifiés** : 
- PR #42 : https://github.com/.../pull/42
**Notes** : En attente de merge dans develop.
```

## Format des Entrées d'Historique

Chaque entrée doit suivre ce format :

```markdown
#### [YYYY-MM-DD] - [Agent] ([Rôle]) - [Action]
**Statut** : [Nouveau statut]
**Détails** : [Description détaillée de ce qui a été fait]
**Fichiers modifiés** : [Liste des fichiers créés/modifiés, ou liens vers documents]
**Notes** : [Notes additionnelles, décisions, problèmes rencontrés, etc.]
```

## Statuts Possibles

### Pour les Issues

- **À faire** : Issue créée, pas encore traitée
- **En cours** : Plan créé, développement en cours
- **En review** : Code implémenté, en cours de review
- **Approuvé** : Review fonctionnelle passée, PR créée
- **Terminé** : PR mergée, fonctionnalité déployée

### Pour les Tasks

- **À faire** : Plan créé, pas encore implémenté
- **En cours** : Implémentation en cours
- **En review** : Code en cours de review
- **Approuvé** : Code approuvé, PR créée
- **Terminé** : PR mergée, fonctionnalité terminée

## Bonnes Pratiques

1. **Mettre à jour régulièrement** : Ne pas attendre la fin pour mettre à jour
2. **Être précis** : Décrire clairement ce qui a été fait
3. **Référencer les fichiers** : Lister les fichiers créés/modifiés
4. **Ajouter des notes** : Documenter les décisions importantes
5. **Maintenir l'ordre chronologique** : Les entrées doivent être dans l'ordre temporel

## Intégration dans le Workflow

Chaque agent doit mettre à jour les documents lors de ses actions :

- **Alex** : Met à jour l'issue lors de la création et après la review fonctionnelle
- **Sam** : Met à jour l'issue et la task lors de la création du plan, après la review du code, et après la création de la PR
- **Morgan** : Met à jour la task après la review architecturale
- **Jordan** : Met à jour la task pendant l'implémentation (marquer les tâches comme terminées)

## Mise à Jour de l'Issue GitHub

### Principe

Après chaque commit significatif (completion d'une phase ou d'un ensemble de tâches), mettre à jour l'issue GitHub correspondante avec un commentaire détaillé.

### Quand Mettre à Jour GitHub

| Agent | Quand | Action |
|-------|-------|--------|
| **Jordan** | Après chaque commit de phase complète | Ajouter un commentaire à l'issue GitHub avec le résumé des tâches complétées |
| **Sam** | Après création de PR | Ajouter un commentaire avec le lien vers la PR |
| **Alex** | Après merge de la PR | Ajouter un commentaire de clôture |

### Format du Commentaire GitHub

Le commentaire doit suivre ce format :

```markdown
## [Titre de la phase/tâche] ✅

**[Agent] ([Rôle])** - [Description]

### Tâches complétées

#### Tâche X.X : [Titre] ✅
- [Détail 1]
- [Détail 2]
- [Détail 3]

### Fichiers créés/modifiés
- `chemin/fichier.php` (nouveau/modifié)
- `chemin/fichier.blade.php` (nouveau/modifié)

### Prochaines étapes
- [Prochaine tâche/phase]

**Commit** : `[sha]` - [message du commit]
```

### Exemple de Commentaire GitHub

```markdown
## Phase 6 complète terminée ✅

**Jordan (Fullstack Developer)** - Phase 6 (Frontend - Composants Livewire) complète

### Tâches complétées (6.4 à 6.9)

#### Tâche 6.4 : Composant Register ✅
- Composant Register créé avec validation côté client et serveur
- Appel à POST /api/auth/register via `apiPostPublic()`
- Gestion des erreurs avec affichage des messages de validation

### Fichiers créés/modifiés
- `app/Livewire/Register.php` (nouveau)
- `resources/views/livewire/register.blade.php` (nouveau)
- `routes/web.php` (modifié)

### Prochaines étapes
- Phase 7 : Tests (tâches 7.1 à 7.5)

**Commit** : `d3d76da` - feat: Phase 6 complète - Composants Livewire
```

### Processus Complet

1. **Faire le commit** avec un message descriptif
2. **Mettre à jour le fichier local** de l'issue (`docs/issues/ISSUE-XXX.md`) avec les progrès
3. **Faire un commit** de la mise à jour de l'issue locale
4. **Ajouter un commentaire** à l'issue GitHub avec le résumé des changements
5. **Inclure le SHA du commit** dans le commentaire GitHub pour référence

### Outils Disponibles

- **GitHub MCP** : Utiliser `mcp_github_add_issue_comment` pour ajouter un commentaire
- **Git** : Utiliser `git commit` pour commiter les changements
- **Format** : Suivre le format standardisé ci-dessus

### Bonnes Pratiques

1. **Mettre à jour après chaque phase complète** : Ne pas attendre la fin de toutes les phases
2. **Être concis mais complet** : Résumer les points importants sans être trop verbeux
3. **Référencer les commits** : Inclure le SHA du commit pour traçabilité
4. **Maintenir la synchronisation** : L'issue locale reste la source de vérité, GitHub est la synchronisation
5. **Utiliser des emojis** : ✅ pour les tâches complétées, 🔄 pour en cours, etc.

---

**Rappel** : Le suivi et l'historique sont essentiels pour comprendre l'évolution du projet et faciliter la collaboration entre les agents. La synchronisation avec GitHub permet un suivi visuel et une meilleure communication.

