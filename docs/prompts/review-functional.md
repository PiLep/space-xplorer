# Action: Review Functional

## Description

Cette action permet à l'agent Product Manager (Alex) de reviewer fonctionnellement une implémentation. La review vérifie que la fonctionnalité répond aux besoins métier, respecte les critères d'acceptation de l'issue, et offre une bonne expérience utilisateur.

## Quand Utiliser Cette Action

L'agent Product Manager doit faire une review fonctionnelle quand :
- Le code a été approuvé par le Lead Developer (Sam) (vérifier le statut dans la task : "Code Review approuvé")
- La fonctionnalité est testable (déployée en environnement de test ou localement)
- Une validation métier est nécessaire avant la mise en production
- Il faut vérifier que les critères d'acceptation sont respectés

### Comment Identifier une Issue Prête pour Review Fonctionnelle

1. **Vérifier le statut de la task** (`docs/tasks/TASK-XXX.md`) :
   - Le statut doit indiquer "Code Review approuvé" ou "Implémentation terminée - Code Review approuvé"
   - L'historique doit contenir une entrée de Sam (Lead Dev) avec "Code Review approuvé"

2. **Vérifier le statut de l'issue** (`docs/issues/ISSUE-XXX.md`) :
   - Le statut doit indiquer "En cours - Code Review approuvé" ou similaire
   - Il ne doit pas y avoir encore d'entrée de review fonctionnelle dans l'historique

3. **Vérifier les reviews existantes** (`docs/reviews/`) :
   - Il doit y avoir une `CODE-REVIEW-XXX.md` approuvée
   - Il ne doit pas encore y avoir de `FUNCTIONAL-REVIEW-XXX.md`

## Format de la Review

La review peut être créée de deux façons :

1. **Fichier de review séparé** : `FUNCTIONAL-REVIEW-{numero}-{titre-kebab-case}.md`
2. **Section dans l'issue** : Ajouter une section "Review Fonctionnelle" dans l'issue originale

## Structure de la Review

```markdown
# FUNCTIONAL-REVIEW-{numero} : {Titre de la review}

## Issue Associée

[Lien vers l'issue produit]

## Plan Implémenté

[Lien vers le plan de développement]

## Statut

[✅ Approuvé fonctionnellement | ⚠️ Approuvé avec ajustements mineurs | ❌ Retour pour ajustements fonctionnels]

## Vue d'Ensemble

{Résumé de la review fonctionnelle et décision globale}

## Critères d'Acceptation

### ✅ Critères Respectés

- [x] Critère 1 : Description
- [x] Critère 2 : Description
- [x] Critère 3 : Description

### ⚠️ Critères Partiellement Respectés

- [ ] Critère X : Description
  - **Problème** : Ce qui ne correspond pas exactement
  - **Impact** : Impact sur l'expérience utilisateur
  - **Ajustement nécessaire** : Ce qui doit être modifié

### ❌ Critères Non Respectés

- [ ] Critère Y : Description
  - **Problème** : Ce qui manque ou ne fonctionne pas
  - **Impact** : Impact sur l'expérience utilisateur
  - **Ajustement nécessaire** : Ce qui doit être fait

## Expérience Utilisateur

### Points Positifs

- Point positif 1
- Point positif 2

### Points à Améliorer

- Point à améliorer 1 avec explication
- Point à améliorer 2 avec explication

### Problèmes Identifiés

- Problème 1 avec explication et impact utilisateur
- Problème 2 avec explication et impact utilisateur

## Fonctionnalités Métier

### Fonctionnalités Implémentées

- ✅ Fonctionnalité 1 : Description
- ✅ Fonctionnalité 2 : Description

### Fonctionnalités Manquantes

- ❌ Fonctionnalité X : Description
  - **Impact** : Impact sur l'expérience utilisateur
  - **Priorité** : [High | Medium | Low]

### Fonctionnalités à Ajuster

- ⚠️ Fonctionnalité Y : Description
  - **Problème** : Ce qui ne correspond pas aux attentes
  - **Ajustement** : Ce qui doit être modifié

## Cas d'Usage

### Cas d'Usage Testés

- ✅ Cas 1 : Description du cas testé et résultat
- ✅ Cas 2 : Description du cas testé et résultat

### Cas d'Usage Non Couverts

- ❌ Cas X : Description
  - **Impact** : Impact sur l'expérience utilisateur
  - **Nécessité** : Est-ce critique ou peut être ajouté plus tard ?

## Interface & UX

### Points Positifs

- Point positif sur l'interface
- Point positif sur l'UX

### Points à Améliorer

- Point à améliorer avec suggestion
- Point à améliorer avec suggestion

### Problèmes UX

- Problème UX avec explication et suggestion

## Ajustements Demandés

Si des ajustements sont nécessaires :

### Ajustement 1 : [Titre]

**Problème** : Description du problème fonctionnel
**Impact** : Impact sur l'expérience utilisateur
**Ajustement** : Ce qui doit être modifié
**Priorité** : [High | Medium | Low]
**Section concernée** : Partie de la fonctionnalité à ajuster

### Ajustement 2 : [Titre]
...

## Questions & Clarifications

- Question 1 : [Question pour l'équipe de développement]
- Question 2 : [Question pour l'équipe de développement]

## Conclusion

{Résumé final et prochaines étapes}

## Références

- [Lien vers l'issue]
- [Lien vers le plan]
- [Lien vers PROJECT_BRIEF.md]
```

## Exemple de Review Fonctionnelle

```markdown
# FUNCTIONAL-REVIEW-001 : Review fonctionnelle de l'inscription utilisateur

## Issue Associée

[ISSUE-001-implement-user-registration.md](../issues/ISSUE-001-implement-user-registration.md)

## Plan Implémenté

[TASK-001-implement-user-registration.md](../tasks/TASK-001-implement-user-registration.md)

## Statut

✅ Approuvé fonctionnellement avec ajustements mineurs

## Vue d'Ensemble

L'implémentation de l'inscription utilisateur est globalement excellente et répond aux besoins métier. Le flux est fluide et l'expérience utilisateur est agréable. Quelques ajustements mineurs sont suggérés pour améliorer encore l'expérience.

## Critères d'Acceptation

### ✅ Critères Respectés

- [x] Formulaire d'inscription avec validation (nom, email, mot de passe)
- [x] Création du compte utilisateur en base de données
- [x] Génération automatique d'une planète d'origine
- [x] Attribution de la planète au joueur
- [x] Retour d'un token Sanctum pour l'authentification
- [x] Redirection vers le tableau de bord après inscription

### ⚠️ Critères Partiellement Respectés

Aucun

### ❌ Critères Non Respectés

Aucun

## Expérience Utilisateur

### Points Positifs

- Le formulaire d'inscription est simple et clair
- La génération de planète est transparente pour l'utilisateur
- La redirection vers le tableau de bord est fluide
- Les messages d'erreur sont clairs et utiles

### Points à Améliorer

- **Message de bienvenue** : Il serait bien d'afficher un message de bienvenue après l'inscription pour accueillir le joueur
  - **Impact** : Améliorerait l'expérience d'onboarding
  - **Priorité** : Low

### Problèmes Identifiés

Aucun problème majeur identifié

## Fonctionnalités Métier

### Fonctionnalités Implémentées

- ✅ Inscription avec validation complète
- ✅ Génération automatique de planète d'origine
- ✅ Authentification via Sanctum
- ✅ Affichage de la planète d'origine après inscription

### Fonctionnalités Manquantes

Aucune fonctionnalité manquante pour le MVP

### Fonctionnalités à Ajuster

Aucune fonctionnalité nécessitant des ajustements majeurs

## Cas d'Usage

### Cas d'Usage Testés

- ✅ Inscription réussie : Un nouvel utilisateur peut s'inscrire avec succès
- ✅ Validation des erreurs : Les erreurs de validation sont bien affichées
- ✅ Génération de planète : Une planète unique est générée pour chaque utilisateur
- ✅ Authentification : Le token Sanctum permet l'authentification

### Cas d'Usage Non Couverts

- ⚠️ Cas limite : Que se passe-t-il si deux utilisateurs s'inscrivent exactement en même temps ?
  - **Impact** : Faible, cas très rare
  - **Nécessité** : Peut être géré plus tard si nécessaire

## Interface & UX

### Points Positifs

- Interface claire et intuitive
- Formulaire bien structuré
- Feedback visuel lors de la soumission
- Messages d'erreur bien positionnés

### Points à Améliorer

- **Animation de chargement** : Ajouter une animation pendant la génération de planète pour indiquer que quelque chose se passe
  - **Impact** : Améliorerait la perception de la rapidité
  - **Priorité** : Medium

### Problèmes UX

Aucun problème UX majeur identifié

## Ajustements Demandés

### Ajustement 1 : Message de bienvenue

**Problème** : Pas de message de bienvenue après l'inscription réussie
**Impact** : L'expérience d'onboarding pourrait être améliorée
**Ajustement** : Ajouter un message de bienvenue avec le nom du joueur et une présentation de sa planète d'origine
**Priorité** : Low
**Section concernée** : Page de redirection après inscription

### Ajustement 2 : Animation de chargement

**Problème** : Pas d'indication visuelle pendant la génération de planète
**Impact** : L'utilisateur pourrait penser que l'application est bloquée
**Ajustement** : Ajouter une animation de chargement avec un message "Génération de votre planète d'origine..."
**Priorité** : Medium
**Section concernée** : Processus d'inscription

## Questions & Clarifications

- **Question 1** : Le message de bienvenue doit-il être une notification toast ou une page dédiée ?
  - **Suggestion** : Notification toast serait plus fluide

- **Question 2** : Faut-il prévoir un système de retry si la génération de planète échoue ?
  - **Réponse attendue** : Oui, c'est prévu dans le code mais pas visible pour l'utilisateur

## Conclusion

L'implémentation fonctionnelle est excellente et répond parfaitement aux besoins du MVP. Les ajustements suggérés sont mineurs et peuvent être faits dans une prochaine itération si nécessaire. La fonctionnalité peut être approuvée pour la production.

**Prochaines étapes** :
1. ✅ Fonctionnalité approuvée fonctionnellement
2. ⚠️ Appliquer les ajustements suggérés (optionnel)
3. ✅ Peut être déployée en production

## Références

- [ISSUE-001-implement-user-registration.md](../issues/ISSUE-001-implement-user-registration.md)
- [PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md) - Parcours utilisateur
```

## Instructions pour l'Agent Product Manager

### Processus Étape par Étape

Quand tu reviews fonctionnellement une implémentation, suis ces étapes dans l'ordre :

#### Étape 1 : Préparation et Lecture

1. **Identifier l'issue à reviewer** :
   - Chercher dans `docs/issues/` les issues avec statut "En cours - Code Review approuvé"
   - Vérifier qu'il n'y a pas encore de review fonctionnelle dans `docs/reviews/FUNCTIONAL-REVIEW-XXX.md`

2. **Lire l'issue complète** (`docs/issues/ISSUE-XXX.md`) :
   - Revoir les critères d'acceptation
   - Comprendre le contexte métier et la valeur utilisateur
   - Noter les fonctionnalités attendues

3. **Lire le plan de développement** (`docs/tasks/TASK-XXX.md`) :
   - Comprendre ce qui a été implémenté
   - Vérifier que le code a été approuvé par Sam (Lead Dev)

4. **Lire la review de code** (`docs/reviews/CODE-REVIEW-XXX.md`) :
   - Comprendre ce qui a été validé techniquement
   - Noter les points d'attention mentionnés

#### Étape 2 : Préparation de l'Environnement de Test

1. **Vérifier que l'application est lancée** :
   ```bash
   ./vendor/bin/sail ps  # Vérifier que les conteneurs sont actifs
   ```
   - Si l'application n'est pas lancée, la lancer avec `./vendor/bin/sail up -d`

2. **Ouvrir l'application dans Chrome DevTools MCP** :
   - Utiliser `mcp_chrome-devtools_new_page` avec l'URL de l'application (généralement `http://localhost`)
   - Naviguer vers la page concernée par la fonctionnalité

#### Étape 3 : Test Fonctionnel

1. **Tester la fonctionnalité comme un utilisateur final** :
   - Utiliser Chrome DevTools MCP pour interagir avec l'interface :
     - `mcp_chrome-devtools_take_snapshot` pour voir l'état de l'interface
     - `mcp_chrome-devtools_fill` pour remplir les formulaires
     - `mcp_chrome-devtools_click` pour cliquer sur les boutons
     - `mcp_chrome-devtools_wait_for` pour attendre les changements
   - Tester tous les cas d'usage principaux
   - Tester les cas limites si possible

2. **Vérifier visuellement** :
   - Prendre des snapshots de l'interface pour vérifier le design
   - Vérifier que l'interface est cohérente avec le style du projet
   - Vérifier l'accessibilité (attributs ARIA, labels, etc.)

3. **Vérifier les tests automatisés** :
   - Lire les tests dans `tests/` pour comprendre ce qui est testé
   - Vérifier que les tests passent (optionnel, généralement déjà vérifié par Sam)

#### Étape 4 : Analyse et Évaluation

1. **Vérifier les critères d'acceptation** :
   - Pour chaque critère de l'issue, vérifier qu'il est respecté
   - Noter les critères partiellement respectés ou non respectés

2. **Évaluer l'expérience utilisateur** :
   - Le parcours est-il fluide ?
   - Les messages d'erreur sont-ils clairs ?
   - L'interface est-elle intuitive ?
   - La fonctionnalité répond-elle au besoin métier ?

3. **Identifier les ajustements** :
   - Noter les points à améliorer (priorité High/Medium/Low)
   - Noter les problèmes identifiés
   - Suggérer des améliorations si nécessaire

#### Étape 5 : Documentation

1. **Créer la review fonctionnelle** :
   - Créer un fichier `docs/reviews/FUNCTIONAL-REVIEW-XXX-{titre-kebab-case}.md`
   - Suivre la structure complète fournie dans ce guide
   - Remplir toutes les sections pertinentes

2. **Mettre à jour l'issue** (`docs/issues/ISSUE-XXX.md`) :
   - Ajouter une entrée dans l'historique avec le résultat de la review
   - Mettre à jour le statut selon la décision (Approuvé / Approuvé avec ajustements / Retour)
   - Voir [update-tracking.md](./update-tracking.md) pour le format exact

3. **Mettre à jour le plan** (`docs/tasks/TASK-XXX.md`) :
   - Ajouter une entrée dans l'historique avec le résultat de la review
   - Mettre à jour le statut si nécessaire

#### Étape 6 : Décision Finale

1. **Prendre une décision** :
   - ✅ **Approuvé fonctionnellement** : La fonctionnalité répond aux besoins, peut être déployée
   - ⚠️ **Approuvé avec ajustements mineurs** : La fonctionnalité est bonne mais des améliorations sont suggérées (non bloquantes)
   - ❌ **Retour pour ajustements fonctionnels** : La fonctionnalité nécessite des modifications avant validation

2. **Documenter la décision** :
   - La décision doit être claire dans la section "Statut" de la review
   - Les ajustements demandés doivent être détaillés dans la section "Ajustements Demandés"

### Mise à Jour des Documents

Après avoir reviewé fonctionnellement, mettre à jour les documents suivants :

1. **Dans l'issue (ISSUE-XXX.md)** :
   - Ajouter une entrée dans "Suivi et Historique" avec le résultat de la review fonctionnelle
   - Mettre à jour le statut :
     - Si approuvé : "✅ Approuvé fonctionnellement" ou "✅ Approuvé fonctionnellement avec ajustements mineurs"
     - Si retour : Garder "En cours" ou mettre "En review fonctionnelle"
   - Format : Voir [update-tracking.md](./update-tracking.md) pour le format exact

2. **Dans le plan (TASK-XXX.md)** :
   - Ajouter une entrée dans l'historique avec le résultat de la review fonctionnelle
   - Mettre à jour le statut :
     - Si approuvé : Ajouter "Review fonctionnelle approuvée" au statut existant
     - Si retour : Garder le statut actuel
   - Format : Voir [update-tracking.md](./update-tracking.md) pour le format exact

3. **Créer le fichier de review** :
   - Créer `docs/reviews/FUNCTIONAL-REVIEW-XXX-{titre-kebab-case}.md`
   - Suivre la structure complète fournie dans ce guide

## Checklist de Review Fonctionnelle

### Avant de Commencer

- [ ] L'issue a été identifiée (statut "En cours - Code Review approuvé")
- [ ] L'issue a été lue complètement (critères d'acceptation compris)
- [ ] Le plan de développement a été lu (TASK-XXX.md)
- [ ] La review de code a été lue (CODE-REVIEW-XXX.md)
- [ ] L'application est lancée et accessible
- [ ] Chrome DevTools MCP est configuré et fonctionnel

### Pendant la Review

- [ ] La fonctionnalité a été testée comme un utilisateur final
- [ ] Tous les critères d'acceptation de l'issue sont vérifiés
- [ ] L'expérience utilisateur est fluide et agréable
- [ ] Les fonctionnalités métier sont correctement implémentées
- [ ] Les cas d'usage principaux sont couverts
- [ ] L'interface est intuitive et cohérente avec le design system
- [ ] Les messages d'erreur sont clairs et utiles
- [ ] La fonctionnalité répond aux besoins du persona
- [ ] Les tests automatisés ont été vérifiés (si disponibles)

### Après la Review

- [ ] Le fichier FUNCTIONAL-REVIEW-XXX.md a été créé
- [ ] Toutes les sections pertinentes ont été remplies
- [ ] L'issue (ISSUE-XXX.md) a été mise à jour avec le résultat
- [ ] Le plan (TASK-XXX.md) a été mis à jour avec le résultat
- [ ] La décision finale est claire (Approuvé / Ajustements / Retour)

## Statuts de Review

- **✅ Approuvé fonctionnellement** : La fonctionnalité répond aux besoins, peut être déployée
- **⚠️ Approuvé avec ajustements mineurs** : La fonctionnalité est bonne mais des améliorations sont suggérées
- **❌ Retour pour ajustements fonctionnels** : La fonctionnalité nécessite des modifications avant validation

## Organisation

Les reviews fonctionnelles sont organisées dans `docs/issues/` ou dans un dossier dédié `docs/reviews/functional/` selon l'organisation choisie. Elles peuvent être :
- Utilisées par l'équipe de développement pour ajuster la fonctionnalité
- Référencées lors du déploiement
- Utilisées pour suivre la qualité fonctionnelle
- Archivées pour référence future

---

**Rappel** : En tant qu'agent Product Manager, tu reviews la fonctionnalité du point de vue utilisateur. Tu t'assures que les besoins métier sont respectés et que l'expérience utilisateur est optimale. Tu fournis des retours constructifs pour améliorer l'expérience de jeu.

