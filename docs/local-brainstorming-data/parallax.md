# Le Parallaxe  
Mystère fil rouge persistant pour Stellar

## Présentation
Le Parallaxe est un phénomène anormal qui poursuit silencieusement chaque explorateur.  
Il se manifeste rarement, sous des formes subtiles que seul le joueur perçoit.  
Le Parallaxe n’a pas de nature confirmée : il peut être un signal, une entité, une résonance, un écho temporel ou une projection psychique.

Le but de cette mécanique est de créer un fil rouge narratif à long terme.  
Le joueur reçoit des fragments d’indices de manière irrégulière, parfois espacés de plusieurs jours de jeu, afin de nourrir une curiosité durable.

---

## Objectifs du système
- Introduire un mystère personnel et persistant.  
- Encourager le retour quotidien sans imposer de grind.  
- Alimenter le lore de l’univers de manière contrôlée.  
- Donner l’impression que le joueur occupe une place singulière.  
- Créer de la discussion entre joueurs sans révéler la vérité.  
- Laisser toutes les interprétations possibles ouvertes.

---

## Manifestations possibles du Parallaxe

### 1. Signaux altérés
Certains mini-jeux (scan, détection, décryptage) peuvent générer :
- distorsions visuelles  
- pics d’intensité impossibles à analyser  
- fragments de phrases ou coordonnées tronquées  

Ces anomalies disparaissent avant que le joueur ne puisse les revoir.

---

### 2. Découvertes impossibles
Certaines découvertes du joueur peuvent présenter :
- des caractéristiques qui ne devraient pas exister  
- des duplicatas subtils dans d’autres secteurs  
- des traces d’analyse datant d’un “futur” impossible  
- des structures géométriques parfaites dans la faune ou la flore

---

### 3. Entrées secrètes du Codex
Le joueur débloque parfois une entrée masquée, visible uniquement par lui.

Format :
- très courte  
- ambiguë  
- non référencée dans le codex public  

Exemples :  
- “Ce secteur connaît ton passage.”  
- “L’autre version de toi a hésité.”

---

### 4. Logs fantômes
Le journal du joueur peut afficher des lignes :
- horodatées d’un moment où il n’était pas connecté  
- décrivant des actions qu’il n’a jamais effectuées  
- mentionnant des systèmes inconnus

Ces logs ne réapparaissent jamais deux fois.

---

### 5. Messages cryptés
Le Parallaxe envoie parfois un message illisible :
- bruit blanc  
- spectrogrammes incohérents  
- symboles non reconnus  
- texte partiellement interprété

Ils ne sont pas conçus pour être résolus immédiatement.

---

## Fréquence des indices
Les indices du Parallaxe sont rares pour conserver l’impact narratif.

### Distribution recommandée :
- Premier indice durant l’onboarding.  
- Ensuite : un indice toutes les 10 à 20 sessions.  
- Possibilité d’indice en cas d’événement exceptionnel (grand score, secteur critique, échec majeur).

---

## Déclencheurs possibles
- Succès élevé ou échec critique dans un mini-jeu.  
- Découverte d’un système ou d’un artefact rare.  
- Première rencontre avec une espèce intelligente.  
- Période d’inactivité prolongée.  
- Exploration d’un secteur “porteur” du Parallaxe.  
- Publication dans le Codex.

---

## Implémentation technique

### Table dédiée aux indices
```sql
mystery_clues
- id
- player_id
- clue_code
- delivered_at
- context (JSON)
