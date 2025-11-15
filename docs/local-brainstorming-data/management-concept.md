# Gestion – Documentation Complète
Système de gestion légère et quotidienne pour Stellar.

---

# 1. Présentation
Le système de gestion de Stellar introduit une couche stratégique simple, jouable en quelques minutes par jour. Il permet aux joueurs d'améliorer leur station, leur vaisseau, leur équipage et leurs capacités d’exploration, sans créer une boucle de grind ou une obligation de présence prolongée.

L’objectif est d’offrir une progression douce, lisible, et liée à l’exploration.

---

# 2. Principes fondamentaux
- **Simplicité** : aucune micro-gestion complexe.
- **Impact réel** : chaque élément de gestion influence le gameplay.
- **Sessions courtes** : toutes les actions doivent être réalisables en moins de 2 minutes.
- **Progression lente mais satisfaisante** : la gestion accompagne la boucle quotidienne.
- **Intégration au lore** : technologies, modules et crew s’inscrivent dans l’univers.

---

# 3. Éléments du système de gestion
Le système repose sur quatre axes :

## 3.1 Station orbitale (base du joueur)
La station sert de hub principal du joueur.

Fonctionnalités améliorables :
- **Laboratoire** : augmente les données produites quotidiennement.
- **Antenne longue portée** : améliore les scans et mini-jeux de détection.
- **Hangar** : débloque des options pour le vaisseau.
- **Module de stabilisation** : réduit les risques d’événements négatifs.

Améliorations sous forme de niveaux simples : 1 → 5.

## 3.2 Vaisseau
Le vaisseau est le principal outil d’exploration.

Stats principales :
- **Fuel**
- **Intégrité de la coque**
- **Puissance du moteur**
- **Qualité des scanners**
- **Boucliers**

Chaque stat influe sur :
- la portée d’expédition
- les résultats de mini-jeux
- les chances de réussite dans les événements

## 3.3 Modules
Équipements installables sur la station ou le vaisseau.

Exemples :
- Scanner avancé
- Module d’analyse biologique
- Amplificateur de signal
- Blindage léger
- Purificateur d’atmosphère

Les modules :
- influencent directement les probabilités de réussite
- débloquent des interactions ou résultats rares

## 3.4 Crew (optionnel et léger)
Le joueur peut posséder jusqu’à **3 membres d’équipage**.

Chaque membre possède :
- un **rôle** (navigation, science, sécurité)
- un **trait** (calme, instable, méthodique…)
- un état : stable → blessé → rétabli

Le crew modifie les événements :
- un scientifique augmente les chances de réussite dans les analyses
- un agent de sécurité limite les dégâts

---

# 4. Ressources principales
Trois ressources uniquement :

## 4.1 Données scientifiques
Générées quotidiennement.
Servent à améliorer la station et certains modules.

## 4.2 Fragments d’artefacts
Obtenus en expédition ou via mini-jeux.
Servent à débloquer des modules uniques.

## 4.3 Matériaux
Ressource plus rare, utilisée pour les améliorations majeures.

---

# 5. Système d’amélioration
Les améliorations suivent toujours ces règles :
- coût clair et limité
- pas de timers multiples
- un seul bouton : "Améliorer"
- progression linéaire ou légèrement exponentielle

Exemple de modèle simple :
```sql
player_upgrades
- id
- player_id
- target_type   -- 'station', 'ship', 'module'
- target_id
- level
- created_at
- updated_at
```

---

# 6. Cycle quotidien de gestion
Durée totale : 60 à 120 secondes.

1. Collecte des ressources quotidiennes.
2. Vérification de l’intégrité du vaisseau.
3. Réparation rapide si nécessaire.
4. Option d’amélioration disponible.
5. Attribution automatique ou manuelle du crew.
6. Impact des améliorations sur la prochaine expédition.

La gestion ne doit jamais ralentir la session.

---

# 7. Intégration avec les autres systèmes

## 7.1 Avec les mini-games
Les statistiques du vaisseau influencent :
- difficulté des mini-jeux
- score maximal
- possibilités d’apparition d’un bonus rare

## 7.2 Avec les expéditions
La gestion contrôle :
- distance maximale
- rareté des découvertes
- risques subis
- chances d’indices du Parallaxe

## 7.3 Avec le Codex
Certaines améliorations débloquent :
- nouvelles catégories
- meilleures analyses
- des versions plus complètes des découvertes

---

# 8. Progression long terme
Le système de gestion doit soutenir la durée de vie du jeu :
- amélioration de station → boost passif
- amélioration de vaisseau → exploration plus profonde
- modules → personnalisation
- crew → variété et ambiance

L’objectif est de garder une boucle de progression douce et intéressante.

---

# 9. Vision future
Extensions possibles :
- modules rares obtenus via événements hebdomadaires
- amélioration visuelle de la station
- crew unique lié au Parallaxe
- spécialisation du vaisseau (exploration, scan, anomalie)

---

# 10. Conclusion
L’aspect gestion de Stellar repose sur la simplicité, l’impact et la cohérence avec la boucle quotidienne. Il doit renforcer l’expérience sans jamais la complexifier ou l’alourdir.
