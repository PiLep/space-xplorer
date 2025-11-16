# Mini-Games – Documentation Complète
Système de mini-jeux courts et quotidiens pour Stellar.

---

# 1. Objectifs des mini-games
Les mini-jeux sont des interactions courtes (10–90 secondes) jouées une fois par jour. Ils remplissent plusieurs fonctions :

- Ajouter un moment d’interactivité dans la session quotidienne.
- Donner un sentiment de progression rapide.
- Offrir des récompenses variées (données, fragments, découvertes).
- Introduire de la tension et du risque léger.
- Servir parfois de support à un indice du Parallaxe.
- Rester suffisamment simples pour ne jamais devenir chronophages.

---

# 2. Caractéristiques générales

## 2.1 Durée
- Minimum : 10 secondes
- Maximum : 90 secondes

## 2.2 Nombre par jour
- 1 mini-jeu quotidien recommandé
- Des versions spéciales peuvent apparaître lors d'événements majeurs

## 2.3 Difficulté
- Légère, jamais punitive
- Basée sur la précision, la rapidité ou la logique simple

## 2.4 Récompenses possibles
- Données scientifiques
- Fragments d’artefacts
- Informations partielles
- Découvertes rares
- Bonus temporaires
- Potentiels indices du Parallaxe

## 2.5 Risques
Léger mais existant :
- petite perte de ressources
- bruit parasite dans les scanners
- altération mineure du vaisseau

---

# 3. Types de mini-games

## 3.1 Scan circulaire
**Principe :** un radar affiche des signaux éphémères. Le joueur doit cliquer au bon moment pour les "verrouiller".

**Objectifs :**
- précision
- observation

**Récompenses :**
- données rares
- découverte d’un point d’intérêt
- amélioration temporaire des scanners

**Intégration Parallaxe :** un signal impossible à verrouiller peut apparaître.

---

## 3.2 Décryptage de signaux
**Principe :** un message est bruité. Le joueur doit déplacer, aligner ou filtrer certaines parties pour révéler une portion lisible.

**Objectifs :**
- logique simple
- exploration narrative

**Récompenses :**
- fragments de lore
- coordonnées partiellement révélées

**Intégration Parallaxe :** symboles inconnus ou distorsions brèves.

---

## 3.3 Navigation dans un champ d’astéroïdes
**Principe :** l’écran comporte 3 zones verticales. Le joueur déplace le vaisseau pour éviter des obstacles qui descendent.

**Objectifs :**
- réflexe léger
- gestion du risque

**Récompenses :**
- intégrité du vaisseau conservée
- données sur la densité locale

**Risques :** légers dégâts si collision.

---

## 3.4 Analyse d’échantillons
**Principe :** le joueur doit associer des motifs ou structures pour identifier un échantillon inconnu.

**Objectifs :**
- reconnaissance visuelle

**Récompenses :**
- éléments biologiques
- données sur faune/flore
- objets codex

---

## 3.5 Harmonisation d'ondes
**Principe :** plusieurs courbes d’ondes sont affichées. Le joueur doit ajuster les fréquences pour les aligner.

**Objectifs :**
- précision
- sens du rythme

**Récompenses :**
- amplification de scan
- signaux rares révélés

**Intégration Parallaxe :** une onde impossible à stabiliser.

---

# 4. Difficulté et scoring

## 4.1 Difficulté dynamique
La difficulté peut évoluer selon :
- progression du joueur
- modules installés
- événements récents

## 4.2 Calcul du score
Paramètres possibles :
- précision
- vitesse
- nombre d’erreurs
- alignement optimal

## 4.3 Interprétation du score
| Score | Résultat |
|-------|----------|
| 0–25  | Échec léger |
| 25–60 | Réussite minimale |
| 60–85 | Bonne performance |
| 85–100| Réussite exceptionnelle |

---

# 5. Récompenses détaillées

## 5.1 Récompenses fixes
- données scientifiques
- fragments simples

## 5.2 Récompenses variables
- signaux rares
- découvertes d’un objet du Stellarpedia
- coordonnées partiellement révélées

## 5.3 Récompenses exceptionnelles (faibles chances)
- artefact mineur
- entrée codex cachée
- message cryptique lié au Parallaxe

---

# 6. Intégration au cycle quotidien
Les mini-games s’intègrent à la boucle journalière :
1. Événement du jour
2. Mini-jeu
3. Expédition
4. Mise à jour du Codex

Chaque mini-jeu apporte une variation subtile sans rallonger la session.

---

# 7. Intégration technique

## 7.1 Table de stockage des tentatives
```sql
mini_game_attempts
- id
- player_id
- type
- score
- success
- context JSON
- duration_ms
- created_at
```

## 7.2 Points d’intégration possibles
- événements
- expéditions
- récompenses
- progression

---

# 8. Valeur narrative
Les mini-jeux servent également :
- à introduire des fragments de lore
- à renforcer l’ambiance du jeu
- à faire apparaître des indices cryptiques
- à créer des moments d’étrangeté liés au Parallaxe

---

# 9. Objectif long terme
Le système doit :
- rester simple
- encourager des sessions courtes
- maintenir l’engagement quotidien
- garder de la diversité avec une faible maintenance

Les mini-games forment ainsi l’un des piliers de la boucle quotidienne de Stellar.
