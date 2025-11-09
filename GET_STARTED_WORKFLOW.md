# Get Started - Usine Logicielle Space Xplorer

Guide pour démarrer avec le système de workflow et les agents IA de Space Xplorer.

## Vue d'Ensemble

Space Xplorer utilise un système de workflow structuré avec des **agents IA** spécialisés qui collaborent pour développer des fonctionnalités de manière organisée et traçable.

### Les Agents

- **Alex** (Product Manager) : Vision produit, création d'issues, review fonctionnelle
- **Sam** (Lead Developer) : Plans techniques, review du code, création de PRs
- **Morgan** (Architect) : Review architecturale, cohérence technique
- **Jordan** (Fullstack Developer) : Implémentation du code
- **Taylor** (Workflow Manager) : Surveillance et amélioration du workflow

## Architecture du Workflow

Le workflow suit un processus en **9 étapes** :

1. **Création d'Issue** (Alex) → Issue produit documentée
2. **Création de Branche** (Sam) → Branche Git créée
3. **Création du Plan** (Sam) → Plan technique détaillé
4. **Review Architecturale** (Morgan) → Plan approuvé + Vérification finale (Sam)
5. **Implémentation** (Jordan) → Code implémenté selon le plan
6. **Review du Code** (Sam) → Code approuvé + Vérification finale (Sam)
7. **Review Fonctionnelle** (Alex) → Fonctionnalité approuvée + Vérification finale (Alex)
8. **Création de PR** (Sam) → Pull Request créée
9. **Merge** (Sam) → Code mergé dans `develop` + Documents mis à jour

## Premiers Pas

### 1. Comprendre la Structure

Explorez la documentation :

```bash
# Documentation principale
cat AGENTS.md          # Liste des agents et leurs rôles
cat WORKFLOW.md        # Workflow complet détaillé

# Documentation des agents
ls docs/agents/        # Descriptions détaillées de chaque agent

# Documentation des prompts (actions)
ls docs/prompts/       # Guides pour chaque action
```

### 2. Explorer un Exemple Complet

Pour comprendre le workflow, explorez :

- **Issues** : `docs/issues/` - Exemples d'issues produit
- **Tasks** : `docs/tasks/` - Exemples de plans de développement
- **Reports** : `docs/reports/` - Rapports de monitoring (si disponibles)

### 3. Comprendre les Documents Clés

- **[WORKFLOW.md](./WORKFLOW.md)** : Workflow complet avec toutes les étapes
- **[AGENTS.md](./AGENTS.md)** : Liste des agents et leurs responsabilités
- **[docs/memory_bank/](./docs/memory_bank/)** : Architecture, stack, vision produit
- **[docs/rules/HUMAN_VALIDATION.md](./docs/rules/HUMAN_VALIDATION.md)** : Points nécessitant validation humaine

## Utiliser le Workflow

### Scénario 1 : Créer une Nouvelle Fonctionnalité

#### Étape 1 : Créer une Issue (Alex)

**Action** : Utiliser le prompt `create-issue`

**Fichier** : `docs/prompts/create-issue.md`

**Résultat** : Une issue dans `docs/issues/ISSUE-XXX-titre.md`

**Contenu requis** :
- Description de la fonctionnalité
- Contexte métier
- Critères d'acceptation
- Priorité
- Section "Suivi et Historique"

**Exemple de commande pour l'IA** :
```
Je veux créer une nouvelle issue. Je suis Alex (Product Manager).
Sujet : Implémenter l'exploration de planètes
[Suivre le guide create-issue.md]
```

#### Étape 2 : Créer une Branche (Sam)

**Action** : Créer une branche Git

**Commande** :
```bash
git checkout develop
git pull origin develop
git checkout -b feature/ISSUE-001-explore-planets
```

#### Étape 3 : Créer le Plan (Sam)

**Action** : Utiliser le prompt `create-plan`

**Fichier** : `docs/prompts/create-plan.md`

**Input** : Lire l'issue créée à l'étape 1

**Résultat** : Un plan dans `docs/tasks/TASK-001-titre.md`

**Contenu requis** :
- Vue d'ensemble technique
- Architecture & Design
- Tâches décomposées
- Migrations, endpoints, événements
- Tests à écrire
- Ordre d'exécution

**Important** : Mettre à jour l'issue associée (statut "En cours")

#### Étape 4 : Review Architecturale (Morgan)

**Action** : Utiliser le prompt `review-task`

**Fichier** : `docs/prompts/review-task.md`

**Input** : Le plan créé à l'étape 3

**Vérifications** :
- Cohérence architecturale
- Qualité technique
- Performance & Scalabilité
- Sécurité
- Tests
- Documentation

**Statuts possibles** :
- ✅ Approuvé
- ⚠️ Approuvé avec recommandations
- ❌ Retour pour modifications

**Important** : Si retour pour modifications, boucler jusqu'à approbation (max 3 itérations)

#### Étape 5 : Vérification Finale du Plan (Sam)

**Action** : Vérification perfectionniste

Même après approbation par Morgan, Sam effectue une dernière vérification complète du plan avant de passer à l'implémentation.

#### Étape 6 : Implémentation (Jordan)

**Action** : Utiliser le prompt `implement-task`

**Fichier** : `docs/prompts/implement-task.md`

**Input** : Le plan approuvé

**Tâches** :
- Créer les migrations
- Créer les modèles
- Créer les services
- Créer les controllers
- Créer les events & listeners
- Créer les form requests
- Écrire les tests
- Mettre à jour la documentation

**Important** : Mettre à jour le plan régulièrement pendant l'implémentation (marquer les tâches terminées)

#### Étape 7 : Review du Code (Sam)

**Action** : Utiliser le prompt `review-implementation`

**Fichier** : `docs/prompts/review-implementation.md`

**Vérifications** :
- Respect du plan
- Conventions Laravel
- Qualité du code
- Tests complets et passants
- Documentation mise à jour

**Statuts possibles** :
- ✅ Approuvé
- ⚠️ Approuvé avec modifications mineures
- ❌ Retour pour corrections

**Important** : Boucler jusqu'à perfection technique (max 3 itérations)

#### Étape 8 : Vérification Finale du Code (Sam)

**Action** : Vérification perfectionniste

Même après approbation, Sam effectue une dernière vérification complète du code avant la review fonctionnelle.

#### Étape 9 : Review Fonctionnelle (Alex)

**Action** : Utiliser le prompt `review-functional`

**Fichier** : `docs/prompts/review-functional.md`

**Vérifications** :
- Les critères d'acceptation de l'issue sont respectés
- L'expérience utilisateur correspond aux attentes
- Les fonctionnalités métier sont correctement implémentées
- Les cas d'usage sont couverts
- L'interface est intuitive

**Statuts possibles** :
- ✅ Approuvé fonctionnellement
- ⚠️ Approuvé avec ajustements mineurs
- ❌ Retour pour ajustements fonctionnels

**Important** : Boucler jusqu'à perfection fonctionnelle (max 3 itérations)

#### Étape 10 : Vérification Finale Fonctionnelle (Alex)

**Action** : Vérification perfectionniste

Même après approbation, Alex effectue une dernière vérification complète de la fonctionnalité avant la création de la PR.

#### Étape 11 : Création de PR (Sam)

**Action** : Utiliser le prompt `create-pr`

**Fichier** : `docs/prompts/create-pr.md`

**Contenu de la PR** :
- Description de la fonctionnalité
- Lien vers l'issue associée
- Lien vers le plan de développement
- Résumé des changements
- Checklist de validation

**Important** : Mettre à jour l'issue et le plan (statut "En review")

#### Étape 12 : Merge (Sam)

**Action** : Merger la PR

**Conditions préalables** :
- ✅ Code approuvé techniquement
- ✅ Fonctionnalité approuvée fonctionnellement
- ✅ Tous les tests passent
- ✅ Aucun conflit avec `develop`
- ✅ Documentation à jour

**Processus** :
1. Vérification finale
2. **Validation humaine** si merge en production (`main`/`master`)
3. Merge dans `develop`
4. Nettoyage (supprimer la branche)
5. Tracking final (statut "Terminé")

## Tracking et Documentation

### Mise à Jour des Documents

À chaque étape, les documents doivent être mis à jour :

**Format** : Voir [update-tracking.md](./docs/prompts/update-tracking.md)

**Issue** (`docs/issues/`) :
- Mise à jour par Alex (création, review fonctionnelle)
- Mise à jour par Sam (création du plan, review du code, création de PR, merge final)

**Task** (`docs/tasks/`) :
- Mise à jour par Sam (création, merge final)
- Mise à jour par Morgan (review architecturale)
- Mise à jour par Jordan (implémentation)
- Mise à jour par Sam (review du code)
- Mise à jour par Alex (review fonctionnelle)

### Format de Tracking

Chaque document doit contenir une section "Suivi et Historique" :

```markdown
## Suivi et Historique

### Statut

[À faire | En cours | En review | Approuvé | Terminé]

### Historique

#### YYYY-MM-DD - [Agent] ([Rôle]) - [Action]
**Statut** : [Nouveau statut]
**Détails** : [Description de ce qui a été fait]
**Fichiers modifiés** : [Liste des fichiers]
**Notes** : [Notes additionnelles]
```

## Points de Validation Humaine

Certaines actions critiques nécessitent une **validation humaine** :

1. **Nouvelles Règles Techniques** (Morgan/Sam)
   - ⚠️ Validation par Lead Developer humain ou Tech Lead requise
   - Référence : [propose-technical-rule.md](./docs/prompts/propose-technical-rule.md)

2. **Modifications de la Memory Bank** (Morgan/Sam)
   - ⚠️ Validation par Tech Lead ou Lead Developer humain requise
   - Référence : [update-memory-bank.md](./docs/prompts/update-memory-bank.md)

3. **Merge en Production** (`main`/`master`)
   - ⚠️ Validation par Lead Developer humain ou Tech Lead requise

4. **Décisions Architecturales Majeures**
   - ⚠️ Validation par Tech Lead requise

5. **Modifications de Sécurité**
   - ⚠️ Validation par Tech Lead ou Security Lead requise

6. **Changements de Scope Produit**
   - ⚠️ Validation par Product Owner requise

**Référence complète** : [HUMAN_VALIDATION.md](./docs/rules/HUMAN_VALIDATION.md)

## Monitoring et Amélioration Continue

### Agent Taylor (Workflow Manager)

Taylor surveille le workflow et génère des rapports d'amélioration :

**Quand intervient Taylor** :
- Après chaque issue complète (de la création au merge)
- Périodiquement (mensuel/trimestriel) pour un bilan global
- Quand un problème récurrent est identifié

**Action** : Utiliser le prompt `monitor-workflow`

**Fichier** : `docs/prompts/monitor-workflow.md`

**Résultat** : Rapport dans `docs/reports/REPORT-{date}-{issue-number}-{titre}.md`

## Commandes Utiles pour l'IA

### Créer une Issue

```
Je suis Alex (Product Manager). Je veux créer une nouvelle issue.
Sujet : [Description de la fonctionnalité]
[Suivre le guide docs/prompts/create-issue.md]
```

### Créer un Plan

```
Je suis Sam (Lead Developer). Je veux créer un plan pour l'issue ISSUE-001.
[Lire l'issue docs/issues/ISSUE-001-*.md]
[Suivre le guide docs/prompts/create-plan.md]
```

### Reviewer un Plan

```
Je suis Morgan (Architect). Je veux reviewer le plan TASK-001.
[Lire le plan docs/tasks/TASK-001-*.md]
[Suivre le guide docs/prompts/review-task.md]
```

### Implémenter un Plan

```
Je suis Jordan (Fullstack Developer). Je veux implémenter le plan TASK-001.
[Lire le plan docs/tasks/TASK-001-*.md]
[Suivre le guide docs/prompts/implement-task.md]
```

### Reviewer le Code

```
Je suis Sam (Lead Developer). Je veux reviewer le code implémenté pour TASK-001.
[Lire le plan et examiner le code]
[Suivre le guide docs/prompts/review-implementation.md]
```

### Reviewer Fonctionnellement

```
Je suis Alex (Product Manager). Je veux reviewer fonctionnellement la fonctionnalité ISSUE-001.
[Lire l'issue et tester la fonctionnalité]
[Suivre le guide docs/prompts/review-functional.md]
```

### Créer une PR

```
Je suis Sam (Lead Developer). Je veux créer une PR pour ISSUE-001.
[Suivre le guide docs/prompts/create-pr.md]
```

### Monitorer le Workflow

```
Je suis Taylor (Workflow Manager). Je veux créer un rapport de monitoring pour ISSUE-001.
[Analyser le workflow complet de l'issue]
[Suivre le guide docs/prompts/monitor-workflow.md]
```

## Bonnes Pratiques

### 1. Toujours Lire Avant d'Agir

Chaque agent doit lire les documents pertinents avant d'agir :
- Sam lit l'issue avant de créer le plan
- Morgan lit le plan avant la review
- Jordan lit le plan approuvé avant l'implémentation
- Alex lit l'issue avant la review fonctionnelle

### 2. Mettre à Jour Immédiatement

Chaque agent met à jour les documents immédiatement après son action pour que les autres agents voient l'état actuel.

### 3. Documenter les Décisions

Toutes les décisions importantes doivent être documentées dans l'historique des documents.

### 4. Boucler Jusqu'à Perfection

Les reviews bouclent jusqu'à ce que tout soit parfait, avec un garde-fou de 3 itérations maximum.

### 5. Vérifications Finales

Même après approbation, effectuer une vérification finale perfectionniste avant de continuer.

## Structure des Dossiers

```
space-xplorer/
├── docs/
│   ├── agents/              # Documentation des agents
│   │   ├── PRODUCT.md       # Alex
│   │   ├── LEAD-DEV.md      # Sam
│   │   ├── ARCHITECT.md     # Morgan
│   │   ├── FULLSTACK-DEV.md # Jordan
│   │   └── MANAGER.md       # Taylor
│   ├── prompts/             # Guides d'actions
│   │   ├── create-issue.md
│   │   ├── create-plan.md
│   │   ├── review-task.md
│   │   ├── implement-task.md
│   │   ├── review-implementation.md
│   │   ├── review-functional.md
│   │   ├── create-pr.md
│   │   ├── update-tracking.md
│   │   ├── monitor-workflow.md
│   │   ├── propose-technical-rule.md
│   │   └── update-memory-bank.md
│   ├── issues/              # Issues produit
│   │   └── ISSUE-XXX-*.md
│   ├── tasks/               # Plans de développement
│   │   └── TASK-XXX-*.md
│   ├── reports/             # Rapports de monitoring
│   │   └── REPORT-XXX-*.md
│   ├── memory_bank/         # Architecture, stack, vision
│   │   ├── ARCHITECTURE.md
│   │   ├── STACK.md
│   │   ├── PROJECT_BRIEF.md
│   │   └── proposals/       # Propositions de modifications
│   └── rules/               # Règles et validations
│       ├── MARKDOWN_RULES.md
│       ├── HUMAN_VALIDATION.md
│       ├── TECHNICAL_RULES.md
│       └── proposals/       # Propositions de règles
├── AGENTS.md                # Liste des agents
└── WORKFLOW.md              # Workflow complet
```

## Ressources

- **[WORKFLOW.md](./WORKFLOW.md)** : Workflow complet détaillé
- **[AGENTS.md](./AGENTS.md)** : Liste complète des agents et prompts
- **[docs/memory_bank/](./docs/memory_bank/)** : Architecture, stack, vision produit
- **[docs/prompts/](./docs/prompts/)** : Guides pour chaque action
- **[docs/rules/HUMAN_VALIDATION.md](./docs/rules/HUMAN_VALIDATION.md)** : Points de validation humaine

## Prochaines Étapes

1. **Lire le WORKFLOW.md** pour comprendre le processus complet
2. **Explorer les prompts** dans `docs/prompts/` pour voir les guides d'actions
3. **Lire un exemple d'issue** dans `docs/issues/` (si disponible)
4. **Lire un exemple de task** dans `docs/tasks/` (si disponible)
5. **Commencer par créer une première issue** avec Alex

---

**Bon développement avec l'usine logicielle ! 🚀**

