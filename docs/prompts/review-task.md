# Action: Review Task

## Description

Cette action permet à l'agent Architecte de reviewer un plan de développement créé par le Lead Developer. La review vérifie la cohérence architecturale, la qualité technique, et suggère des améliorations si nécessaire.

## Quand Utiliser Cette Action

L'agent Architecte doit reviewer un plan quand :
- Un plan de développement a été créé dans `docs/tasks/`
- Une validation architecturale est nécessaire avant l'implémentation
- Des choix techniques doivent être validés
- Des risques architecturaux doivent être identifiés

## Format de la Review

La review peut être créée de deux façons :

1. **Fichier de review séparé** : `REVIEW-{numero}-{titre-kebab-case}.md`
2. **Annotations dans le plan** : Ajouter une section "Review Architecturale" dans le plan original

## Structure de la Review

```markdown
# REVIEW-{numero} : {Titre de la review}

## Plan Reviewé

[Lien vers le plan de développement]

## Statut

[✅ Approuvé | ⚠️ Approuvé avec recommandations | ❌ Retour pour modifications]

## Vue d'Ensemble

{Résumé de la review et décision globale}

## Cohérence Architecturale

### ✅ Points Positifs

- Point positif 1
- Point positif 2

### ⚠️ Points d'Attention

- Point d'attention 1 avec explication
- Point d'attention 2 avec explication

### ❌ Problèmes Identifiés

- Problème 1 avec explication et suggestion
- Problème 2 avec explication et suggestion

## Qualité Technique

### Choix Techniques

- **Choix 1** : ✅ Validé / ⚠️ À améliorer / ❌ À revoir
  - Explication

### Structure & Organisation

- **Structure** : ✅ Cohérente / ⚠️ À améliorer
  - Commentaires

### Dépendances

- **Dépendances** : ✅ Bien gérées / ⚠️ À clarifier
  - Commentaires

## Performance & Scalabilité

### Points Positifs

- Point positif

### Recommandations

- Recommandation avec justification

## Sécurité

### Validations

- ✅ Validations prévues / ⚠️ Validations manquantes
  - Détails

### Authentification & Autorisation

- ✅ Gestion correcte / ⚠️ À améliorer
  - Détails

## Tests

### Couverture

- ✅ Tests complets / ⚠️ Tests à compléter
  - Détails

### Recommandations

- Recommandation de tests additionnels

## Documentation

### Mise à Jour

- ✅ Documentation prévue / ⚠️ Documentation incomplète
  - Détails

## Recommandations Spécifiques

### Recommandation 1 : [Titre]

**Problème** : Description du problème
**Impact** : Impact sur l'architecture/qualité
**Suggestion** : Solution proposée
**Priorité** : [High | Medium | Low]

### Recommandation 2 : [Titre]
...

## Modifications Demandées

Si le plan nécessite des modifications :

### Modification 1 : [Titre]

**Raison** : Pourquoi cette modification est nécessaire
**Action** : Ce qui doit être modifié dans le plan
**Section concernée** : Section du plan à modifier

### Modification 2 : [Titre]
...

## Questions & Clarifications

- Question 1 : [Question pour le Lead Developer]
- Question 2 : [Question pour le Lead Developer]

## Conclusion

{Résumé final et prochaines étapes}

## Références

- [Lien vers architecture]
- [Lien vers documentation pertinente]
```

## Exemple de Review

```markdown
# REVIEW-001 : Review du plan d'inscription utilisateur

## Plan Reviewé

[TASK-001-implement-user-registration.md](../tasks/TASK-001-implement-user-registration.md)

## Statut

✅ Approuvé avec recommandations

## Vue d'Ensemble

Le plan est globalement bien structuré et respecte l'architecture définie. L'approche API-first est correctement suivie, et l'utilisation d'événements pour la génération de planète est appropriée. Quelques recommandations pour améliorer la robustesse et la maintenabilité.

## Cohérence Architecturale

### ✅ Points Positifs

- L'approche API-first est respectée
- L'utilisation d'événements pour découpler la génération de planète est excellente
- La structure des fichiers respecte l'organisation du projet
- Les services sont bien utilisés pour la logique métier

### ⚠️ Points d'Attention

- Le service `PlanetGeneratorService` devrait être dans `app/Services/` mais vérifier qu'il n'y a pas de conflit avec d'autres services
- La gestion d'erreurs lors de la génération de planète pourrait être plus détaillée

### ❌ Problèmes Identifiés

- Aucun problème majeur identifié

## Qualité Technique

### Choix Techniques

- **Événements & Listeners** : ✅ Validé
  - Excellente utilisation de l'architecture événementielle pour découpler la logique

- **Service PlanetGeneratorService** : ✅ Validé
  - Bon choix pour encapsuler la logique de génération

- **FormRequest pour validation** : ✅ Validé
  - Respect des bonnes pratiques Laravel

### Structure & Organisation

- **Structure** : ✅ Cohérente
  - Les phases sont logiques et bien ordonnées
  - Les dépendances sont clairement identifiées

### Dépendances

- **Dépendances** : ✅ Bien gérées
  - L'ordre d'exécution est clair
  - Les prérequis sont bien identifiés

## Performance & Scalabilité

### Points Positifs

- La génération synchrone est acceptable pour le MVP
- La structure permet d'évoluer vers une génération asynchrone si nécessaire

### Recommandations

- **Recommandation** : Considérer l'utilisation de queues pour la génération de planète si le processus devient plus complexe
  - **Justification** : Pour le MVP, synchrone est OK, mais prévoir l'évolution

## Sécurité

### Validations

- ✅ Validations prévues
  - FormRequest avec règles appropriées
  - Validation d'email unique
  - Validation de mot de passe avec confirmation

### Authentification & Autorisation

- ✅ Gestion correcte
  - Utilisation de Sanctum pour les tokens
  - Pas de rôles nécessaires pour le MVP (conforme à l'architecture)

## Tests

### Couverture

- ✅ Tests complets
  - Tests unitaires pour le service
  - Tests d'intégration pour l'endpoint
  - Tests fonctionnels pour le flux complet

### Recommandations

- **Test additionnel** : Tester le cas où la génération de planète échoue
  - **Priorité** : Medium
  - **Raison** : Assurer la robustesse du système

## Documentation

### Mise à Jour

- ✅ Documentation prévue
  - Mise à jour de ARCHITECTURE.md prévue
  - Documentation du service prévue

## Recommandations Spécifiques

### Recommandation 1 : Gestion d'erreurs robuste

**Problème** : Le plan ne détaille pas suffisamment la gestion d'erreurs si la génération de planète échoue
**Impact** : Risque de laisser un utilisateur sans planète d'origine
**Suggestion** : Ajouter une tâche pour gérer les erreurs (rollback de l'utilisateur ou retry de la génération)
**Priorité** : High

### Recommandation 2 : Unicité du nom de planète

**Problème** : Le plan mentionne "nom unique ou gérer les collisions" mais ne détaille pas la solution
**Impact** : Risque de collision de noms
**Suggestion** : Prévoir un mécanisme de génération unique (UUID dans le nom, ou vérification d'unicité)
**Priorité** : Medium

### Recommandation 3 : Configuration des types de planètes

**Problème** : Le choix entre `config/planets.php` et `app/Data/PlanetTypes.php` n'est pas justifié
**Impact** : Cohérence du projet
**Suggestion** : Utiliser `config/planets.php` pour la configuration, plus standard dans Laravel
**Priorité** : Low

## Modifications Demandées

Aucune modification majeure demandée. Le plan peut être approuvé avec les recommandations ci-dessus.

## Questions & Clarifications

- **Question 1** : Le service `PlanetGeneratorService` sera-t-il réutilisé pour d'autres générations de planètes à l'avenir ?
  - **Impact** : Si oui, prévoir une interface ou une abstraction

- **Question 2** : Y a-t-il une limite au nombre de tentatives de génération de planète en cas d'erreur ?
  - **Impact** : Éviter les boucles infinies

## Conclusion

Le plan est approuvé avec quelques recommandations pour améliorer la robustesse. Les modifications suggérées sont principalement des améliorations, pas des blocages. Le plan peut être implémenté tel quel, en tenant compte des recommandations.

**Prochaines étapes** :
1. Implémenter le plan en suivant les recommandations
2. Ajouter la gestion d'erreurs robuste
3. Prévoir l'unicité des noms de planètes

## Références

- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Architecture événementielle
- [STACK.md](../memory_bank/STACK.md) - Stack technique
```

## Instructions pour l'Agent Architecte

Quand tu reviews un plan :

1. **Lire attentivement** : Analyser le plan dans son intégralité
2. **Vérifier la cohérence** : Comparer avec l'architecture définie
3. **Identifier les risques** : Détecter les problèmes potentiels
4. **Suggérer des améliorations** : Proposer des optimisations
5. **Être constructif** : Toujours expliquer le "pourquoi"
6. **Prioriser** : Distinguer les problèmes critiques des améliorations
7. **Valider ou demander des modifications** : Prendre une décision claire
8. **Documenter** : Créer une review complète et actionnable
9. **Mettre à jour le plan** : Ajouter une entrée dans l'historique du plan

### Mise à Jour des Documents

Après avoir reviewé le plan :
- **Dans le plan (TASK-XXX)** : Ajouter une entrée dans "Suivi et Historique" avec le résultat de la review
- **Mettre à jour le statut** : Si approuvé, passer à "En cours", si retour, garder "À faire"

Voir [update-tracking.md](./update-tracking.md) pour le format exact.

## Statuts de Review

- **✅ Approuvé** : Le plan est validé, peut être implémenté tel quel
- **⚠️ Approuvé avec recommandations** : Le plan est validé mais des améliorations sont suggérées
- **❌ Retour pour modifications** : Le plan nécessite des modifications avant validation

## Organisation

Les reviews sont organisées dans `docs/tasks/` ou dans un dossier dédié `docs/reviews/` selon l'organisation choisie. Elles peuvent être :
- Utilisées par le Lead Developer pour améliorer le plan
- Référencées lors de l'implémentation
- Utilisées pour suivre la qualité architecturale
- Archivées pour référence future

