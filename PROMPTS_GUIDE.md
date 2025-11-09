# 📝 Guide des Prompts - Space Xplorer

Ce guide contient des prompts prêts à l'emploi pour chaque étape du workflow, utilisant les références Cursor (`@file`, `@folder`) pour inclure automatiquement le contexte nécessaire.

## 🎯 Prompt de Démarrage (Créer la Première Issue)

```
Je suis Alex (Product Manager) et je veux initialiser le projet Space Xplorer en créant la première issue produit pour le MVP.

Contexte du projet :
- Space Xplorer est un jeu d'exploration de l'univers où les joueurs découvrent des planètes
- Stack : Laravel 12, Livewire 3, MySQL, Laravel Sanctum
- Architecture : API-first, event-driven

Action demandée :
1. Lire la documentation du projet :
   @file docs/memory_bank/PROJECT_BRIEF.md
   @file docs/memory_bank/ARCHITECTURE.md
   @file docs/agents/PRODUCT.md
   @file docs/prompts/create-issue.md

2. Créer la première issue produit ISSUE-001 dans docs/issues/ en suivant le guide create-issue.md

3. Cette issue doit couvrir l'implémentation complète du MVP :
   - Système d'inscription/connexion avec authentification Sanctum
   - Génération automatique d'une planète d'origine à l'inscription (via événement UserRegistered)
   - Visualisation de la planète d'origine sur le tableau de bord
   - Gestion du profil utilisateur

4. L'issue doit inclure :
   - Type : Feature
   - Priorité : High (c'est le MVP)
   - Description complète avec contexte métier
   - Critères d'acceptation détaillés
   - Références vers ARCHITECTURE.md et PROJECT_BRIEF.md
   - Section "Suivi et Historique" avec statut "À faire"

5. Respecter le format exact défini dans create-issue.md
```

---

## 📋 Étape 2 : Créer un Plan Technique (Sam)

```
Je suis Sam (Lead Developer). Je veux créer un plan technique pour l'issue ISSUE-001.

Action demandée :
1. Lire l'issue créée :
   @file docs/issues/ISSUE-001-*.md

2. Lire la documentation technique :
   @file docs/memory_bank/ARCHITECTURE.md
   @file docs/memory_bank/STACK.md
   @file docs/agents/LEAD-DEV.md
   @file docs/prompts/create-plan.md

3. Créer une branche Git : feature/ISSUE-001-*

4. Créer le plan technique TASK-001 dans docs/tasks/ en suivant le guide create-plan.md

5. Le plan doit inclure :
   - Vue d'ensemble technique
   - Architecture & Design
   - Tâches de développement décomposées
   - Migrations nécessaires
   - Endpoints API
   - Événements & Listeners
   - Tests à écrire
   - Ordre d'exécution
   - Section "Suivi et Historique" avec statut "À faire"

6. Mettre à jour l'issue associée : statut "En cours"
```

---

## 🔍 Étape 3 : Review Architecturale (Morgan)

```
Je suis Morgan (Architect). Je veux reviewer le plan technique TASK-001.

Action demandée :
1. Lire le plan technique :
   @file docs/tasks/TASK-001-*.md

2. Lire la documentation :
   @file docs/memory_bank/ARCHITECTURE.md
   @file docs/agents/ARCHITECT.md
   @file docs/prompts/review-task.md

3. Effectuer la review architecturale en vérifiant :
   - Cohérence architecturale
   - Qualité technique
   - Performance & Scalabilité
   - Sécurité
   - Tests
   - Documentation

4. Ajouter le résultat de la review dans le plan avec statut :
   - ✅ Approuvé
   - ⚠️ Approuvé avec recommandations
   - ❌ Retour pour modifications

5. Mettre à jour la section "Suivi et Historique" du plan
```

---

## 💻 Étape 4 : Implémentation (Jordan)

```
Je suis Jordan (Fullstack Developer). Je veux implémenter le plan TASK-001.

Action demandée :
1. Lire le plan approuvé :
   @file docs/tasks/TASK-001-*.md

2. Lire la documentation :
   @file docs/memory_bank/ARCHITECTURE.md
   @file docs/agents/FULLSTACK-DEV.md
   @file docs/prompts/implement-task.md

3. Implémenter le plan en respectant l'ordre défini :
   - Créer les migrations
   - Créer les modèles
   - Créer les services
   - Créer les controllers
   - Créer les events & listeners
   - Créer les form requests
   - Écrire les tests
   - Mettre à jour la documentation

4. Mettre à jour le plan régulièrement :
   - Marquer les tâches comme terminées
   - Ajouter des entrées dans l'historique
```

---

## ✅ Étape 5 : Review du Code (Sam)

```
Je suis Sam (Lead Developer). Je veux reviewer le code implémenté pour TASK-001.

Action demandée :
1. Lire le plan et examiner le code :
   @file docs/tasks/TASK-001-*.md
   @folder app/
   @folder database/
   @folder tests/

2. Lire la documentation :
   @file docs/agents/LEAD-DEV.md
   @file docs/prompts/review-implementation.md

3. Effectuer la review en vérifiant :
   - Respect du plan
   - Conventions Laravel
   - Qualité du code
   - Tests complets et passants
   - Documentation mise à jour

4. Ajouter le résultat dans le plan et l'issue avec statut :
   - ✅ Approuvé
   - ⚠️ Approuvé avec modifications mineures
   - ❌ Retour pour corrections
```

---

## 🎯 Étape 6 : Review Fonctionnelle (Alex)

```
Je suis Alex (Product Manager). Je veux reviewer fonctionnellement la fonctionnalité ISSUE-001.

Action demandée :
1. Lire l'issue et tester la fonctionnalité :
   @file docs/issues/ISSUE-001-*.md

2. Lire la documentation :
   @file docs/memory_bank/PROJECT_BRIEF.md
   @file docs/agents/PRODUCT.md
   @file docs/prompts/review-functional.md

3. Effectuer la review fonctionnelle en vérifiant :
   - Les critères d'acceptation de l'issue sont respectés
   - L'expérience utilisateur correspond aux attentes
   - Les fonctionnalités métier sont correctement implémentées
   - Les cas d'usage sont couverts
   - L'interface est intuitive

4. Ajouter le résultat dans l'issue avec statut :
   - ✅ Approuvé fonctionnellement
   - ⚠️ Approuvé avec ajustements mineurs
   - ❌ Retour pour ajustements fonctionnels
```

---

## 🔀 Étape 7 : Créer une PR (Sam)

```
Je suis Sam (Lead Developer). Je veux créer une Pull Request pour ISSUE-001.

Action demandée :
1. Lire l'issue et le plan :
   @file docs/issues/ISSUE-001-*.md
   @file docs/tasks/TASK-001-*.md

2. Lire la documentation :
   @file docs/prompts/create-pr.md

3. Créer la Pull Request vers develop avec :
   - Description de la fonctionnalité
   - Lien vers l'issue associée
   - Lien vers le plan de développement
   - Résumé des changements
   - Checklist de validation

4. Mettre à jour l'issue et le plan : statut "En review"
```

---

## 📊 Utilisation des Références Cursor

### Références de Fichiers (`@file`)
```
@file docs/agents/PRODUCT.md
@file docs/prompts/create-issue.md
@file docs/issues/ISSUE-001-*.md
```

### Références de Dossiers (`@folder`)
```
@folder docs/
@folder app/
@folder database/
@folder tests/
```

### Références de Code (`@code`)
```
@code app/Models/Planet.php
@code routes/api.php
```

## 💡 Conseils

1. **Toujours inclure les références** : Utilisez `@file` pour pointer vers les agents et prompts pertinents
2. **Lire avant d'agir** : Les références garantissent que Cursor a le contexte nécessaire
3. **Suivre le workflow** : Respectez l'ordre des étapes documenté dans WORKFLOW.md
4. **Mettre à jour le tracking** : Toujours mettre à jour les sections "Suivi et Historique"

## 📚 Références Complètes

- `@file AGENTS.md` - Liste des agents
- `@file WORKFLOW.md` - Workflow complet
- `@file GET_STARTED_WORKFLOW.md` - Guide de démarrage
- `@folder docs/agents/` - Documentation des agents
- `@folder docs/prompts/` - Guides d'actions
- `@folder docs/memory_bank/` - Connaissance du projet


