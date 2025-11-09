# Action: Propose Technical Rule

## Description

Cette action permet aux agents Architect (Morgan) et Lead Developer (Sam) de proposer de nouvelles règles techniques pour améliorer les bonnes pratiques de l'équipe.

## Quand Utiliser Cette Action

Les agents peuvent proposer une nouvelle règle technique quand :
- Une bonne pratique récurrente est identifiée lors des reviews
- Un pattern technique mérite d'être standardisé
- Une amélioration de qualité de code est nécessaire
- Une convention d'équipe doit être formalisée
- Un problème technique récurrent nécessite une règle

## Format de la Proposition

Chaque proposition doit être créée dans `docs/rules/proposals/` avec le format suivant :

**Nom du fichier** : `PROPOSAL-{YYYY-MM-DD}-{titre-kebab-case}.md`

Exemple : `PROPOSAL-2024-01-15-naming-conventions.md`

## Structure de la Proposition

```markdown
# PROPOSAL-{date} : {Titre de la règle}

## Métadonnées

- **Date** : YYYY-MM-DD
- **Proposé par** : [Morgan (Architect) | Sam (Lead Developer)]
- **Statut** : [En attente de validation | Approuvé | Rejeté]
- **Priorité** : [Haute | Moyenne | Basse]

## Contexte

{Pourquoi cette règle est nécessaire ? Quel problème résout-elle ?}

## Règle Proposée

### Titre de la Règle

{Description détaillée de la règle}

### Exemples

**Bon exemple** :
```php
// Exemple de code conforme à la règle
```

**Mauvais exemple** :
```php
// Exemple de code non conforme
```

### Justification

{Pourquoi cette règle améliore la qualité du code ?}

### Impact

- **Sur le code existant** : [Description]
- **Sur les nouvelles fonctionnalités** : [Description]
- **Sur les développeurs** : [Description]

## Validation

### Validation Humaine Requise

⚠️ **Cette proposition nécessite une validation humaine avant application.**

**Raison** : [Pourquoi une validation humaine est nécessaire]

**Validateur** : [Nom du validateur humain]

**Date de validation** : [À remplir après validation]

**Commentaires du validateur** : [À remplir après validation]

## Références

- [Lien vers issue ou task associée si applicable]
- [Lien vers documentation pertinente]
```

## Processus de Validation

### 1. Proposition

L'agent (Morgan ou Sam) crée la proposition dans `docs/rules/proposals/`

### 2. Review Interne

- Si proposé par **Sam** : Morgan review la proposition
- Si proposé par **Morgan** : Sam review la proposition
- Discussion et ajustements si nécessaire

### 3. Validation Humaine

⚠️ **Toute nouvelle règle technique nécessite une validation humaine avant application.**

**Validateur** : Lead Developer humain ou Tech Lead

**Critères de validation** :
- La règle est-elle pertinente ?
- L'impact est-il acceptable ?
- La règle est-elle claire et applicable ?
- Y a-t-il des cas particuliers à considérer ?

### 4. Application

Une fois validée :
- La règle est ajoutée dans `docs/rules/TECHNICAL_RULES.md`
- Le statut de la proposition est mis à jour à "Approuvé"
- Les agents sont informés de la nouvelle règle
- La règle s'applique aux nouvelles fonctionnalités

## Instructions pour les Agents

Quand tu proposes une règle technique :

1. **Identifier le besoin** : Pourquoi cette règle est nécessaire ?
2. **Rédiger clairement** : La règle doit être compréhensible et applicable
3. **Fournir des exemples** : Bon et mauvais exemples pour illustrer
4. **Évaluer l'impact** : Considérer l'impact sur le code existant et futur
5. **Demander validation** : Toute règle nécessite validation humaine avant application

## Organisation

Les propositions sont organisées dans `docs/rules/proposals/` et peuvent être :
- Reviewées par l'autre agent technique (Morgan/Sam)
- Validées par un humain
- Appliquées après validation
- Archivées pour référence future

---

**Rappel** : Les règles techniques améliorent la qualité du code mais doivent être validées par un humain avant application.

