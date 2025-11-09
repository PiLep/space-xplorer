# Action: Review Visual

## Description

Cette action permet à l'agent Designer (Riley) de reviewer visuellement une implémentation. La review vérifie que le design est correctement appliqué, que l'identité visuelle est respectée, et que l'expérience utilisateur visuelle est optimale.

## Quand Utiliser Cette Action

L'agent Designer doit faire une review visuelle quand :
- Le code a été approuvé par le Lead Developer (Sam)
- La fonctionnalité est testable (déployée en environnement de test ou localement)
- Une validation visuelle est nécessaire avant la mise en production
- Il faut vérifier que le design respecte l'identité visuelle définie
- Il faut s'assurer que l'expérience utilisateur visuelle est optimale

## Outils Utilisés

- **Chrome DevTools MCP** : Pour tester visuellement la fonctionnalité, prendre des screenshots, analyser l'interface, vérifier la console et les requêtes réseau
- Navigation dans l'application via Chrome DevTools
- Screenshots à chaque étape du parcours utilisateur
- Analyse de la console pour les erreurs JavaScript
- Analyse des requêtes réseau pour vérifier les appels API
- Analyse visuelle des composants et de la mise en page

## Format de la Review

La review peut être créée de deux façons :

1. **Fichier de review séparé** : `VISUAL-REVIEW-{numero}-{titre-kebab-case}.md`
2. **Section dans l'issue** : Ajouter une section "Review Visuelle" dans l'issue originale

## Structure de la Review

```markdown
# VISUAL-REVIEW-{numero} : {Titre de la review}

## Issue Associée

[Lien vers l'issue produit]

## Plan Implémenté

[Lien vers le plan de développement]

## Statut

[✅ Approuvé visuellement | ⚠️ Approuvé avec ajustements visuels mineurs | ❌ Retour pour ajustements visuels]

## Vue d'Ensemble

{Résumé de la review visuelle et décision globale}

## Identité Visuelle

### ✅ Éléments Respectés

- [x] Palette de couleurs cohérente
- [x] Typographie correcte
- [x] Espacements cohérents
- [x] Composants conformes au design system

### ⚠️ Éléments Partiellement Respectés

- [ ] Élément X : Description
  - **Problème** : Ce qui ne correspond pas exactement
  - **Impact** : Impact sur l'identité visuelle
  - **Ajustement nécessaire** : Ce qui doit être modifié

### ❌ Éléments Non Respectés

- [ ] Élément Y : Description
  - **Problème** : Ce qui manque ou ne correspond pas
  - **Impact** : Impact sur l'identité visuelle
  - **Ajustement nécessaire** : Ce qui doit être fait

## Cohérence Visuelle

### Points Positifs

- Point positif 1
- Point positif 2

### Points à Améliorer

- Point à améliorer 1 avec explication
- Point à améliorer 2 avec explication

### Incohérences Identifiées

- Incohérence 1 avec explication et impact
- Incohérence 2 avec explication et impact

## Hiérarchie Visuelle

### ✅ Hiérarchie Respectée

- [x] Titres et sous-titres bien hiérarchisés
- [x] Importance visuelle des éléments respectée
- [x] Navigation claire et intuitive

### ⚠️ Problèmes de Hiérarchie

- [ ] Problème X : Description
  - **Impact** : Impact sur la compréhension
  - **Ajustement** : Ce qui doit être modifié

## Responsive Design

### ✅ Points Positifs

- [x] Design adapté aux différentes tailles d'écran
- [x] Navigation mobile fonctionnelle
- [x] Contenu lisible sur tous les appareils

### ⚠️ Problèmes Responsive

- [ ] Problème X : Description
  - **Appareil concerné** : Mobile / Tablet / Desktop
  - **Impact** : Impact sur l'expérience utilisateur
  - **Ajustement** : Ce qui doit être modifié

## Accessibilité Visuelle

### ✅ Points Positifs

- [x] Contraste suffisant pour la lisibilité
- [x] Tailles de texte appropriées
- [x] Indicateurs visuels clairs

### ⚠️ Problèmes d'Accessibilité

- [ ] Problème X : Description
  - **Impact** : Impact sur l'accessibilité
  - **Ajustement** : Ce qui doit être modifié

## Interactions & Animations

### ✅ Points Positifs

- [x] Interactions fluides
- [x] Animations subtiles et appropriées
- [x] Feedback visuel clair sur les actions

### ⚠️ Problèmes d'Interaction

- [ ] Problème X : Description
  - **Impact** : Impact sur l'expérience utilisateur
  - **Ajustement** : Ce qui doit être modifié

## Screenshots & Analyse Visuelle

### Screenshot 1 : [Titre de l'écran]

**Fichier** : `screenshot-{numero}-{description}.png`

**Analyse** :
- Point positif 1
- Point à améliorer 1
- Problème identifié 1

### Screenshot 2 : [Titre de l'écran]

**Fichier** : `screenshot-{numero}-{description}.png`

**Analyse** :
- Point positif 2
- Point à améliorer 2
- Problème identifié 2

## Ajustements Demandés

Si des ajustements sont nécessaires :

### Ajustement 1 : [Titre]

**Problème** : Description du problème visuel
**Impact** : Impact sur l'identité visuelle ou l'UX
**Ajustement** : Ce qui doit être modifié
**Priorité** : [High | Medium | Low]
**Section concernée** : Partie de l'interface à ajuster
**Screenshot** : Référence au screenshot si applicable

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
- [Lien vers DESIGNER.md]
```

## Exemple de Review Visuelle

```markdown
# VISUAL-REVIEW-001 : Review visuelle de l'inscription utilisateur

## Issue Associée

[ISSUE-001-implement-user-registration.md](../issues/ISSUE-001-implement-user-registration.md)

## Plan Implémenté

[TASK-001-implement-user-registration.md](../tasks/TASK-001-implement-user-registration.md)

## Statut

✅ Approuvé visuellement avec ajustements mineurs

## Vue d'Ensemble

L'implémentation visuelle de l'inscription utilisateur est globalement excellente et respecte bien l'identité visuelle définie. Le design est cohérent et l'expérience utilisateur est agréable. Quelques ajustements mineurs sont suggérés pour améliorer encore l'expérience visuelle.

## Identité Visuelle

### ✅ Éléments Respectés

- [x] Palette de couleurs cohérente avec le thème spatial
- [x] Typographie moderne et lisible
- [x] Espacements cohérents entre les éléments
- [x] Composants conformes au design system

### ⚠️ Éléments Partiellement Respectés

- [ ] Couleur des boutons : La couleur primaire pourrait être légèrement ajustée pour mieux correspondre à la palette définie
  - **Problème** : Nuance légèrement différente de la palette de référence
  - **Impact** : Impact mineur sur la cohérence visuelle
  - **Ajustement nécessaire** : Ajuster la couleur pour correspondre exactement à la palette

### ❌ Éléments Non Respectés

Aucun

## Cohérence Visuelle

### Points Positifs

- Le formulaire d'inscription est visuellement cohérent avec le reste de l'application
- Les espacements sont harmonieux et équilibrés
- La hiérarchie visuelle est claire

### Points à Améliorer

- **Alignement des labels** : Les labels des champs pourraient être mieux alignés pour une meilleure lisibilité
  - **Impact** : Améliorerait la lisibilité du formulaire
  - **Priorité** : Low

### Incohérences Identifiées

Aucune incohérence majeure identifiée

## Hiérarchie Visuelle

### ✅ Hiérarchie Respectée

- [x] Titre principal bien mis en évidence
- [x] Champs du formulaire bien organisés
- [x] Bouton d'action clairement visible
- [x] Messages d'erreur bien positionnés

### ⚠️ Problèmes de Hiérarchie

Aucun problème majeur identifié

## Responsive Design

### ✅ Points Positifs

- [x] Design adapté aux différentes tailles d'écran
- [x] Formulaire lisible sur mobile
- [x] Boutons accessibles sur tous les appareils

### ⚠️ Problèmes Responsive

- [ ] Espacement sur mobile : Les espacements pourraient être légèrement ajustés sur mobile
  - **Appareil concerné** : Mobile
  - **Impact** : Impact mineur sur l'expérience mobile
  - **Ajustement** : Ajuster les espacements pour mobile

## Accessibilité Visuelle

### ✅ Points Positifs

- [x] Contraste suffisant pour la lisibilité
- [x] Tailles de texte appropriées
- [x] Indicateurs visuels clairs pour les erreurs

### ⚠️ Problèmes d'Accessibilité

Aucun problème majeur identifié

## Interactions & Animations

### ✅ Points Positifs

- [x] Interactions fluides lors de la soumission
- [x] Feedback visuel clair sur les actions
- [x] Transitions subtiles et appropriées

### ⚠️ Problèmes d'Interaction

- [ ] Animation de chargement : Ajouter une animation pendant la génération de planète
  - **Impact** : Améliorerait la perception de la rapidité
  - **Ajustement** : Ajouter une animation de chargement avec un message

## Screenshots & Analyse Visuelle

### Screenshot 1 : Page d'inscription

**Fichier** : `screenshot-001-inscription-page.png`

**Analyse** :
- ✅ Design cohérent avec l'identité visuelle
- ✅ Formulaire bien structuré et lisible
- ⚠️ Couleur du bouton à ajuster légèrement
- ⚠️ Alignement des labels à améliorer

### Screenshot 2 : Messages d'erreur

**Fichier** : `screenshot-002-validation-errors.png`

**Analyse** :
- ✅ Messages d'erreur bien visibles
- ✅ Contraste suffisant pour la lisibilité
- ✅ Positionnement approprié

## Ajustements Demandés

### Ajustement 1 : Couleur du bouton primaire

**Problème** : La couleur du bouton primaire ne correspond pas exactement à la palette définie
**Impact** : Impact mineur sur la cohérence visuelle
**Ajustement** : Ajuster la couleur pour correspondre exactement à la palette de référence
**Priorité** : Low
**Section concernée** : Bouton de soumission du formulaire
**Screenshot** : screenshot-001-inscription-page.png

### Ajustement 2 : Animation de chargement

**Problème** : Pas d'animation pendant la génération de planète
**Impact** : L'utilisateur pourrait penser que l'application est bloquée
**Ajustement** : Ajouter une animation de chargement avec un message "Génération de votre planète d'origine..."
**Priorité** : Medium
**Section concernée** : Processus d'inscription

## Questions & Clarifications

- **Question 1** : L'animation de chargement doit-elle être une animation CSS ou JavaScript ?
  - **Suggestion** : Animation CSS serait plus performante

- **Question 2** : Faut-il prévoir un design pour les états de chargement des autres fonctionnalités ?
  - **Réponse attendue** : Oui, cela devrait faire partie du design system

## Conclusion

L'implémentation visuelle est excellente et respecte bien l'identité visuelle définie. Les ajustements suggérés sont mineurs et peuvent être faits dans une prochaine itération si nécessaire. Le design peut être approuvé pour la production.

**Prochaines étapes** :
1. ✅ Design approuvé visuellement
2. ⚠️ Appliquer les ajustements suggérés (optionnel)
3. ✅ Peut être déployé en production

## Références

- [ISSUE-001-implement-user-registration.md](../issues/ISSUE-001-implement-user-registration.md)
- [DESIGNER.md](../agents/DESIGNER.md) - Identité visuelle et design system
```

## Instructions pour l'Agent Designer

Quand tu reviews visuellement une implémentation :

1. **Lire l'issue** : Revoir les besoins utilisateurs et les objectifs de l'issue
2. **Tester visuellement** : Utiliser Chrome DevTools MCP pour naviguer dans l'application
3. **Prendre des screenshots** : Capturer les écrans importants à chaque étape
4. **Analyser le design** : Vérifier la cohérence visuelle, l'alignement, les espacements
5. **Évaluer l'UX visuelle** : Vérifier que l'expérience utilisateur visuelle est optimale
6. **Vérifier l'accessibilité** : S'assurer que le design est accessible visuellement
7. **Identifier les ajustements** : Suggérer des améliorations si nécessaire
8. **Prendre une décision** : Approuver ou demander des ajustements
9. **Documenter** : Créer une review complète avec screenshots et recommandations
10. **Mettre à jour les documents** : Ajouter une entrée dans l'historique de l'issue et du plan

### Processus de Review Visuelle avec Chrome DevTools MCP

1. **Navigation** : Utiliser Chrome DevTools MCP pour naviguer vers l'application (http://localhost)
2. **Parcours utilisateur** : Tester chaque étape du parcours (inscription, connexion, visualisation planète, profil)
3. **Screenshots** : Prendre des screenshots à chaque étape importante pour analyse visuelle
4. **Analyse visuelle** : Analyser chaque screenshot pour identifier les problèmes d'interface et d'UX
5. **Console** : Vérifier la console pour les erreurs JavaScript et warnings
6. **Réseau** : Analyser les requêtes réseau pour vérifier que les appels API fonctionnent
7. **Documentation** : Inclure les screenshots dans la review avec annotations si nécessaire

### Mise à Jour des Documents

Après avoir reviewé visuellement :
- **Dans l'issue (ISSUE-XXX)** : Ajouter une entrée dans "Suivi et Historique" avec le résultat de la review visuelle
- **Dans le plan (TASK-XXX)** : Ajouter une entrée dans l'historique
- **Mettre à jour le statut** : Si approuvé, passer à "Approuvé", si retour, garder "En review"

Voir [update-tracking.md](./update-tracking.md) pour le format exact.

## Checklist de Review Visuelle

- [ ] L'identité visuelle est respectée (couleurs, typographie, espacements)
- [ ] La cohérence visuelle est maintenue à travers l'application
- [ ] La hiérarchie visuelle est claire et intuitive
- [ ] Le design est responsive sur tous les appareils
- [ ] L'accessibilité visuelle est assurée (contraste, lisibilité)
- [ ] Les interactions sont fluides et appropriées
- [ ] Les animations sont subtiles et engageantes
- [ ] Les screenshots documentent bien l'état visuel

## Statuts de Review

- **✅ Approuvé visuellement** : Le design respecte l'identité visuelle, peut être déployé
- **⚠️ Approuvé avec ajustements visuels mineurs** : Le design est bon mais des améliorations sont suggérées
- **❌ Retour pour ajustements visuels** : Le design nécessite des modifications avant validation

## Organisation

Les reviews visuelles sont organisées dans `docs/issues/` ou dans un dossier dédié `docs/reviews/visual/` selon l'organisation choisie. Elles peuvent être :
- Utilisées par l'équipe de développement pour ajuster le design
- Référencées lors du déploiement
- Utilisées pour suivre la qualité visuelle
- Archivées pour référence future

---

**Rappel** : En tant qu'agent Designer, tu reviews le design du point de vue visuel et UX. Tu t'assures que l'identité visuelle est respectée et que l'expérience utilisateur visuelle est optimale. Tu fournis des retours constructifs avec screenshots pour améliorer l'expérience visuelle de jeu.

