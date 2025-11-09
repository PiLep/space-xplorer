# Action: Update Memory Bank

## Description

Cette action permet aux agents Architect (Morgan) et Lead Developer (Sam) de proposer des modifications à la Memory Bank (ARCHITECTURE.md, STACK.md) pour refléter l'évolution du projet.

## Quand Utiliser Cette Action

Les agents peuvent proposer une modification de la Memory Bank quand :
- L'architecture évolue et nécessite une mise à jour
- La stack technique change ou s'enrichit
- De nouveaux patterns ou conventions sont adoptés
- Des décisions architecturales importantes sont prises
- La documentation ne reflète plus la réalité du projet

## Format de la Proposition

Chaque proposition doit être créée dans `docs/memory_bank/proposals/` avec le format suivant :

**Nom du fichier** : `PROPOSAL-{YYYY-MM-DD}-{document}-{titre-kebab-case}.md`

Exemples :
- `PROPOSAL-2024-01-15-ARCHITECTURE-new-pattern.md`
- `PROPOSAL-2024-01-15-STACK-new-dependency.md`

## Structure de la Proposition

```markdown
# PROPOSAL-{date} : Modification de {Document}

## Métadonnées

- **Date** : YYYY-MM-DD
- **Document concerné** : [ARCHITECTURE.md | STACK.md | PROJECT_BRIEF.md]
- **Proposé par** : [Morgan (Architect) | Sam (Lead Developer)]
- **Statut** : [En attente de validation | Approuvé | Rejeté]
- **Type de modification** : [Ajout | Modification | Suppression]

## Contexte

{Pourquoi cette modification est nécessaire ? Quel changement dans le projet la justifie ?}

## Modification Proposée

### Section Concernée

{Quelle section du document est concernée ?}

### Contenu Actuel

{Le contenu actuel de la section}

### Contenu Proposé

{Le nouveau contenu proposé}

### Différences

{Description des changements apportés}

## Justification

{Pourquoi cette modification améliore la documentation ?}

### Raisons Techniques

- [Raison 1]
- [Raison 2]
- [Raison 3]

### Impact

- **Sur l'architecture** : [Description]
- **Sur les développeurs** : [Description]
- **Sur les futures fonctionnalités** : [Description]

## Validation

### Validation Humaine Requise

⚠️ **Cette modification nécessite une validation humaine avant application.**

**Raison** : La Memory Bank est la source de vérité du projet et impacte tous les agents.

**Validateur** : [Nom du validateur humain]

**Date de validation** : [À remplir après validation]

**Commentaires du validateur** : [À remplir après validation]

## Références

- [Lien vers issue ou task associée si applicable]
- [Lien vers documentation pertinente]
- [Lien vers décision architecturale si applicable]
```

## Processus de Validation

### 1. Proposition

L'agent (Morgan ou Sam) crée la proposition dans `docs/memory_bank/proposals/`

### 2. Review Interne

- Si proposé par **Sam** : Morgan review la proposition
- Si proposé par **Morgan** : Sam review la proposition
- Discussion et ajustements si nécessaire

### 3. Validation Humaine

⚠️ **Toute modification de la Memory Bank nécessite une validation humaine avant application.**

**Validateur** : Lead Developer humain, Tech Lead, ou Product Owner selon le type de modification

**Critères de validation** :
- La modification est-elle justifiée ?
- L'impact est-il acceptable ?
- La documentation reste-t-elle cohérente ?
- Y a-t-il des impacts sur d'autres documents ?

### 4. Application

Une fois validée :
- La modification est appliquée au document concerné
- Le statut de la proposition est mis à jour à "Approuvé"
- Les agents sont informés de la modification
- Un commit est créé avec la modification

## Instructions pour les Agents

Quand tu proposes une modification de la Memory Bank :

1. **Identifier le besoin** : Pourquoi cette modification est nécessaire ?
2. **Documenter clairement** : La proposition doit être précise et complète
3. **Justifier** : Expliquer pourquoi cette modification améliore la documentation
4. **Évaluer l'impact** : Considérer l'impact sur tous les agents et le projet
5. **Demander validation** : Toute modification nécessite validation humaine avant application

## Documents de la Memory Bank

- **ARCHITECTURE.md** : Architecture technique, modèle de données, API endpoints, flux métier
- **STACK.md** : Stack technique (Laravel, Livewire, MySQL), dépendances, outils
- **PROJECT_BRIEF.md** : Vision métier, fonctionnalités, personas, flux utilisateurs (modifications rares, généralement par Alex)

## Organisation

Les propositions sont organisées dans `docs/memory_bank/proposals/` et peuvent être :
- Reviewées par l'autre agent technique (Morgan/Sam)
- Validées par un humain
- Appliquées après validation
- Archivées pour référence future

---

**Rappel** : La Memory Bank est la source de vérité du projet. Toute modification doit être validée par un humain avant application.

