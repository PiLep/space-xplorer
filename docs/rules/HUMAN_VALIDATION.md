# Points de Validation Humaine - Space Xplorer

Ce document liste tous les points critiques du workflow qui nécessitent une validation humaine avant de continuer.

## Principes

Certaines décisions et actions sont **trop critiques** pour être laissées uniquement aux agents IA. Une validation humaine est requise pour :

- Garantir la qualité et la cohérence
- Éviter les erreurs coûteuses
- Maintenir l'alignement avec la vision produit
- Assurer la sécurité et la stabilité du projet

## Points de Validation Critique

### 1. Nouvelles Règles Techniques

**Quand** : Quand Morgan (Architect) ou Sam (Lead Developer) propose une nouvelle règle technique

**Validateur** : Lead Developer humain ou Tech Lead

**Critères** :
- La règle est-elle pertinente et justifiée ?
- L'impact sur le code existant est-il acceptable ?
- La règle est-elle claire et applicable ?
- Y a-t-il des cas particuliers à considérer ?

**Action** : Voir [propose-technical-rule.md](../prompts/propose-technical-rule.md)

**Statut** : ⚠️ **Validation humaine requise avant application**

---

### 2. Modifications de la Memory Bank

**Quand** : Quand Morgan (Architect) ou Sam (Lead Developer) propose une modification de ARCHITECTURE.md ou STACK.md

**Validateur** : 
- **ARCHITECTURE.md** : Tech Lead ou Lead Developer humain
- **STACK.md** : Tech Lead ou Lead Developer humain
- **PROJECT_BRIEF.md** : Product Owner (modifications rares)

**Critères** :
- La modification est-elle justifiée par l'évolution du projet ?
- L'impact sur tous les agents est-il acceptable ?
- La documentation reste-t-elle cohérente ?
- Y a-t-il des impacts sur d'autres documents ?

**Action** : Voir [update-memory-bank.md](../prompts/update-memory-bank.md)

**Statut** : ⚠️ **Validation humaine requise avant application**

---

### 3. Merge de Pull Request dans `main` ou `master`

**Quand** : Quand une PR doit être mergée dans la branche principale (production)

**Validateur** : Lead Developer humain ou Tech Lead

**Critères** :
- Toutes les reviews sont-elles passées ?
- Les tests passent-ils tous ?
- La documentation est-elle à jour ?
- Y a-t-il des risques de régression ?

**Action** : Voir étape 9 du [WORKFLOW.md](../../WORKFLOW.md)

**Statut** : ⚠️ **Validation humaine requise avant merge en production**

**Note** : Les merges dans `develop` peuvent être automatisés après validation technique et fonctionnelle.

---

### 4. Décisions Architecturales Majeures

**Quand** : Quand une décision architecturale impacte significativement le projet

**Exemples** :
- Changement de framework ou de stack principale
- Refactoring majeur de l'architecture
- Introduction d'une nouvelle technologie critique
- Modification du modèle de données principal

**Validateur** : Tech Lead ou Lead Developer humain

**Critères** :
- La décision est-elle justifiée ?
- L'impact est-il acceptable ?
- Y a-t-il des alternatives à considérer ?
- Le plan de migration est-il réaliste ?

**Statut** : ⚠️ **Validation humaine requise avant implémentation**

---

### 5. Modifications de Sécurité Critiques

**Quand** : Quand des modifications touchent à la sécurité du système

**Exemples** :
- Changements d'authentification
- Modifications des permissions
- Gestion des données sensibles
- Configuration de sécurité

**Validateur** : Tech Lead ou Security Lead

**Critères** :
- Les risques de sécurité sont-ils identifiés ?
- Les mesures de sécurité sont-elles adéquates ?
- Y a-t-il besoin d'un audit de sécurité ?

**Statut** : ⚠️ **Validation humaine requise avant implémentation**

---

### 6. Changements de Scope Produit

**Quand** : Quand une issue ou une fonctionnalité nécessite un changement de scope significatif

**Validateur** : Product Owner ou Product Manager humain

**Critères** :
- Le changement de scope est-il justifié ?
- L'impact sur le roadmap est-il acceptable ?
- Les ressources sont-elles disponibles ?

**Statut** : ⚠️ **Validation humaine requise avant modification**

---

## Processus de Validation

### 1. Identification

L'agent identifie qu'une action nécessite une validation humaine.

### 2. Création de la Proposition

L'agent crée une proposition documentée dans le dossier approprié :
- `docs/rules/proposals/` pour les règles techniques
- `docs/memory_bank/proposals/` pour les modifications de Memory Bank

### 3. Notification

L'agent notifie le validateur humain avec :
- Le type de validation nécessaire
- Le document de proposition
- Le contexte et la justification
- L'urgence si applicable

### 4. Review Humaine

Le validateur humain :
- Lit la proposition complète
- Évalue les critères de validation
- Pose des questions si nécessaire
- Prend une décision (Approuvé / Rejeté / Modifications demandées)

### 5. Application ou Ajustement

- Si **Approuvé** : L'agent applique la modification
- Si **Rejeté** : L'agent archive la proposition avec la raison
- Si **Modifications demandées** : L'agent ajuste et resoumet

## Format de Validation

Chaque proposition doit inclure une section "Validation" :

```markdown
## Validation

### Validation Humaine Requise

⚠️ **Cette [action] nécessite une validation humaine avant application.**

**Raison** : [Pourquoi une validation humaine est nécessaire]

**Validateur** : [Nom du validateur humain]

**Date de validation** : [À remplir après validation]

**Commentaires du validateur** : [À remplir après validation]

**Statut** : [En attente | Approuvé | Rejeté]
```

## Rôles et Responsabilités

| Rôle | Validations Responsables |
|------|-------------------------|
| **Tech Lead** | Règles techniques, Memory Bank, Architecture, Sécurité, Merge production |
| **Lead Developer** | Règles techniques, Memory Bank, Architecture, Merge production |
| **Product Owner** | Scope produit, PROJECT_BRIEF.md |
| **Security Lead** | Modifications de sécurité |

## Exceptions

Les actions suivantes **ne nécessitent PAS** de validation humaine (après validation technique/fonctionnelle) :
- ✅ Merge dans `develop` après reviews techniques et fonctionnelles
- ✅ Création d'issues produit (Alex)
- ✅ Création de plans techniques (Sam)
- ✅ Reviews techniques et fonctionnelles (processus normal)
- ✅ Implémentation selon plan approuvé (Jordan)

## Références

- [WORKFLOW.md](../../WORKFLOW.md) : Workflow complet
- [propose-technical-rule.md](../prompts/propose-technical-rule.md) : Guide pour proposer des règles
- [update-memory-bank.md](../prompts/update-memory-bank.md) : Guide pour modifier la Memory Bank

---

**Rappel** : La validation humaine garantit la qualité et la cohérence du projet. Ne pas contourner ces validations.

