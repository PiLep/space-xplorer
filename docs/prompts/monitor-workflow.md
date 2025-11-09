# Action: Monitor Workflow

## Description

Cette action permet à l'agent Manager (Taylor) de surveiller le déroulement du workflow, d'analyser les processus, et de générer un rapport d'amélioration.

## Quand Utiliser Cette Action

L'agent Manager doit créer un rapport de monitoring quand :
- Une issue complète son cycle de développement (de la création au merge)
- Plusieurs issues ont été traitées et un rapport périodique est nécessaire
- Un problème récurrent est identifié dans le workflow
- Une amélioration significative du workflow est nécessaire
- Un bilan trimestriel ou mensuel est demandé

## Format du Rapport

Chaque rapport doit être créé dans `docs/reports/` avec le format suivant :

**Nom du fichier** : `REPORT-{YYYY-MM-DD}-{issue-number}-{titre}.md`

Exemple : `REPORT-2024-01-15-ISSUE-001-implement-user-registration.md`

Pour un rapport périodique : `REPORT-{YYYY-MM-DD}-PERIODIC-{periode}.md`

Exemple : `REPORT-2024-01-31-PERIODIC-monthly.md`

## Structure du Rapport

```markdown
# REPORT-{date} : {Titre du rapport}

## Métadonnées

- **Date** : YYYY-MM-DD
- **Période analysée** : [Date début] - [Date fin]
- **Issues analysées** : [Liste des issues]
- **Agent Manager** : Taylor

## Résumé Exécutif

{Un résumé de 3-5 lignes des points clés du rapport}

## 1. Analyse du Workflow

### 1.1 Respect du Workflow

- ✅ / ❌ Les étapes ont-elles été suivies dans l'ordre ?
- ✅ / ❌ Les vérifications finales ont-elles été effectuées ?
- ✅ / ❌ Les documents ont-ils été mis à jour régulièrement ?

**Observations** :
- [Détails des observations]

### 1.2 Temps de Cycle

| Étape | Temps estimé | Temps réel | Écart | Commentaire |
|-------|--------------|------------|-------|-------------|
| Création Issue | - | Xh | - | - |
| Création Plan | - | Xh | - | - |
| Review Architecturale | - | Xh | - | - |
| Implémentation | - | Xh | - | - |
| Review Code | - | Xh | - | - |
| Review Fonctionnelle | - | Xh | - | - |
| Création PR | - | Xh | - | - |
| Merge | - | Xh | - | - |
| **TOTAL** | - | Xh | - | - |

### 1.3 Itérations et Retours

| Étape de Review | Nombre d'itérations | Raison des retours | Commentaire |
|-----------------|---------------------|-------------------|-------------|
| Review Architecturale | X | [Raisons] | - |
| Review Code | X | [Raisons] | - |
| Review Fonctionnelle | X | [Raisons] | - |

### 1.4 Blocages et Dépendances

- **Blocages identifiés** : [Liste]
- **Dépendances gérées** : [Liste]
- **Impact sur le workflow** : [Description]

### 1.5 Points Forts du Workflow

- ✅ [Point fort 1]
- ✅ [Point fort 2]
- ✅ [Point fort 3]

### 1.6 Points d'Amélioration du Workflow

- ⚠️ [Point d'amélioration 1]
- ⚠️ [Point d'amélioration 2]
- ⚠️ [Point d'amélioration 3]

## 2. Analyse des Agents

### 2.1 Alex (Product Manager)

**Points forts** :
- ✅ [Point fort 1]
- ✅ [Point fort 2]

**Points d'amélioration** :
- ⚠️ [Point d'amélioration 1]
- ⚠️ [Point d'amélioration 2]

**Recommandations** :
- 💡 [Recommandation 1]
- 💡 [Recommandation 2]

### 2.2 Sam (Lead Developer)

**Points forts** :
- ✅ [Point fort 1]
- ✅ [Point fort 2]

**Points d'amélioration** :
- ⚠️ [Point d'amélioration 1]
- ⚠️ [Point d'amélioration 2]

**Recommandations** :
- 💡 [Recommandation 1]
- 💡 [Recommandation 2]

### 2.3 Morgan (Architect)

**Points forts** :
- ✅ [Point fort 1]
- ✅ [Point fort 2]

**Points d'amélioration** :
- ⚠️ [Point d'amélioration 1]
- ⚠️ [Point d'amélioration 2]

**Recommandations** :
- 💡 [Recommandation 1]
- 💡 [Recommandation 2]

### 2.4 Jordan (Fullstack Developer)

**Points forts** :
- ✅ [Point fort 1]
- ✅ [Point fort 2]

**Points d'amélioration** :
- ⚠️ [Point d'amélioration 1]
- ⚠️ [Point d'amélioration 2]

**Recommandations** :
- 💡 [Recommandation 1]
- 💡 [Recommandation 2]

## 3. Analyse des Documents

### 3.1 Issues (docs/issues/)

**Qualité globale** : [Excellent / Bon / À améliorer]

**Points observés** :
- ✅ / ❌ Complétude des informations
- ✅ / ❌ Clarté des critères d'acceptation
- ✅ / ❌ Tracking à jour
- ✅ / ❌ Documentation des dépendances

**Recommandations** :
- 💡 [Recommandation 1]
- 💡 [Recommandation 2]

### 3.2 Tasks (docs/tasks/)

**Qualité globale** : [Excellent / Bon / À améliorer]

**Points observés** :
- ✅ / ❌ Détails techniques suffisants
- ✅ / ❌ Ordre d'exécution clair
- ✅ / ❌ Tracking à jour
- ✅ / ❌ Documentation des dépendances techniques

**Recommandations** :
- 💡 [Recommandation 1]
- 💡 [Recommandation 2]

### 3.3 Code

**Qualité globale** : [Excellent / Bon / À améliorer]

**Points observés** :
- ✅ / ❌ Respect du plan technique
- ✅ / ❌ Qualité du code
- ✅ / ❌ Tests complets
- ✅ / ❌ Documentation du code

**Recommandations** :
- 💡 [Recommandation 1]
- 💡 [Recommandation 2]

## 4. Métriques et Statistiques

### 4.1 Métriques Globales

- **Nombre d'issues traitées** : X
- **Temps moyen par issue** : Xh
- **Taux de réussite** : X%
- **Nombre moyen d'itérations par review** : X

### 4.2 Métriques par Agent

| Agent | Issues traitées | Temps moyen | Qualité moyenne |
|-------|----------------|-------------|-----------------|
| Alex | X | Xh | [Excellent/Bon/À améliorer] |
| Sam | X | Xh | [Excellent/Bon/À améliorer] |
| Morgan | X | Xh | [Excellent/Bon/À améliorer] |
| Jordan | X | Xh | [Excellent/Bon/À améliorer] |

## 5. Recommandations d'Amélioration

### 5.1 Améliorations Workflow

#### Recommandation 1 : [Titre]
- **Problème identifié** : [Description]
- **Impact** : [Impact sur le workflow]
- **Solution proposée** : [Description de la solution]
- **Priorité** : [Haute / Moyenne / Basse]
- **Effort estimé** : [Faible / Moyen / Élevé]

#### Recommandation 2 : [Titre]
...

### 5.2 Améliorations Agents

#### Recommandation pour [Agent] : [Titre]
- **Problème identifié** : [Description]
- **Impact** : [Impact sur le workflow]
- **Solution proposée** : [Description de la solution]
- **Priorité** : [Haute / Moyenne / Basse]

### 5.3 Améliorations Documents

#### Recommandation 1 : [Titre]
- **Problème identifié** : [Description]
- **Solution proposée** : [Description de la solution]
- **Priorité** : [Haute / Moyenne / Basse]

## 6. Plan d'Action

### Actions Immédiates (Cette semaine)

- [ ] [Action 1] - Responsable : [Agent] - Date : [Date]
- [ ] [Action 2] - Responsable : [Agent] - Date : [Date]

### Actions Court Terme (Ce mois)

- [ ] [Action 1] - Responsable : [Agent] - Date : [Date]
- [ ] [Action 2] - Responsable : [Agent] - Date : [Date]

### Actions Long Terme (Ce trimestre)

- [ ] [Action 1] - Responsable : [Agent] - Date : [Date]
- [ ] [Action 2] - Responsable : [Agent] - Date : [Date]

## 7. Conclusion

{Conclusion du rapport avec les points clés à retenir}

## Références

- [WORKFLOW.md](../../WORKFLOW.md)
- [Issues analysées](../issues/)
- [Tasks analysées](../tasks/)
```

## Instructions pour l'Agent Manager

Quand tu crées un rapport de monitoring :

1. **Collecte les données** : Lit tous les documents pertinents (issues, tasks, code, historique)
2. **Analyse objectivement** : Identifie les faits, pas les opinions
3. **Mesure les métriques** : Calcule les temps, itérations, taux de réussite
4. **Identifie les patterns** : Cherche les répétitions et problèmes récurrents
5. **Propose des solutions** : Tes recommandations doivent être actionnables
6. **Priorise** : Classe les améliorations par priorité et impact
7. **Sois constructif** : Focus sur l'amélioration, pas sur la critique

### Collecte d'Informations

Pour analyser une issue complète :
- Lire l'issue (`docs/issues/ISSUE-XXX.md`)
- Lire le plan associé (`docs/tasks/TASK-XXX.md`)
- Examiner l'historique dans les deux documents
- Analyser le code implémenté (si disponible)
- Vérifier les commits et la PR (si disponible)

### Analyse

Pour chaque aspect analysé :
- **Identifier** : Ce qui s'est passé
- **Mesurer** : Quantifier quand c'est possible
- **Comparer** : Avec les attentes ou les standards
- **Évaluer** : Qualité, efficacité, pertinence
- **Recommander** : Proposer des améliorations concrètes

## Organisation

Les rapports sont organisés dans `docs/reports/` et peuvent être :
- Utilisés pour améliorer le workflow
- Référencés dans les discussions d'amélioration
- Utilisés pour suivre l'évolution du workflow
- Partagés avec tous les agents pour transparence

---

**Rappel** : L'objectif est d'améliorer continuellement le workflow pour qu'il soit plus efficace et produise une meilleure qualité.

