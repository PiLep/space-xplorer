# Configuration des Heartbeats Laravel Forge - Space Xplorer

Les heartbeats Laravel Forge permettent de surveiller les tâches planifiées pour s'assurer qu'elles s'exécutent avec succès et à temps. Cette fonctionnalité vous alerte si une tâche ne s'exécute pas ou échoue.

## Vue d'ensemble

Le projet Space Xplorer utilise deux tâches planifiées quotidiennes :

1. **Génération de ressources planètes** - Tous les jours à 2h00
2. **Génération de ressources avatars** - Tous les jours à 2h30

Ces tâches sont configurées avec des heartbeats Laravel Forge pour un monitoring proactif.

## Configuration dans Laravel Forge

### Étape 1 : Créer les tâches planifiées dans Forge

1. Connectez-vous à votre tableau de bord Laravel Forge
2. Naviguez vers **Site / Processes / Scheduler**
3. Créez deux tâches planifiées :

#### Tâche 1 : Génération de ressources planètes
- **Commande** : `php artisan schedule:run`
- **Utilisateur** : `forge` (ou l'utilisateur approprié)
- **Fréquence** : `* * * * *` (toutes les minutes, comme requis par Laravel)
- **Monitor with heartbeats** : ✅ Activé
- **Notify me after** : `60` minutes (ou selon vos besoins)

#### Tâche 2 : Génération de ressources avatars
- **Commande** : `php artisan schedule:run`
- **Utilisateur** : `forge` (ou l'utilisateur approprié)
- **Fréquence** : `* * * * *` (toutes les minutes, comme requis par Laravel)
- **Monitor with heartbeats** : ✅ Activé
- **Notify me after** : `60` minutes (ou selon vos besoins)

> **Note** : Les deux tâches utilisent la même commande `php artisan schedule:run` car Laravel gère l'exécution des différentes commandes planifiées selon leur horaire. Les heartbeats sont configurés au niveau de chaque commande individuelle dans le code Laravel.

### Étape 2 : Récupérer les URLs de heartbeat

Après avoir créé chaque tâche planifiée avec les heartbeats activés, Laravel Forge génère une URL unique pour chaque heartbeat. Cette URL ressemble à :

```
https://forge.laravel.com/servers/{server-id}/sites/{site-id}/heartbeats/{heartbeat-id}
```

Copiez ces URLs pour chaque tâche.

### Étape 3 : Configurer les variables d'environnement

Ajoutez les URLs de heartbeat dans votre fichier `.env` :

```env
# Laravel Forge Heartbeats
FORGE_HEARTBEAT_PLANET_RESOURCES=https://forge.laravel.com/servers/{server-id}/sites/{site-id}/heartbeats/{heartbeat-id-planets}
FORGE_HEARTBEAT_AVATAR_RESOURCES=https://forge.laravel.com/servers/{server-id}/sites/{site-id}/heartbeats/{heartbeat-id-avatars}
```

### Étape 4 : Vérifier la configuration

Les heartbeats sont automatiquement envoyés après l'exécution réussie de chaque tâche grâce à la méthode `thenPing()` dans `routes/console.php`.

## Comment ça fonctionne

### Dans le code Laravel

Les tâches planifiées sont configurées dans `routes/console.php` avec la méthode `thenPing()` :

```php
Schedule::command(GenerateDailyPlanetResources::class)
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->thenPing(env('FORGE_HEARTBEAT_PLANET_RESOURCES'));

Schedule::command(GenerateDailyAvatarResources::class)
    ->dailyAt('02:30')
    ->withoutOverlapping()
    ->thenPing(env('FORGE_HEARTBEAT_AVATAR_RESOURCES'));
```

### Flux d'exécution

1. Laravel Forge exécute `php artisan schedule:run` toutes les minutes
2. Laravel vérifie quelles tâches doivent être exécutées selon leur horaire
3. La tâche s'exécute (par exemple, `GenerateDailyPlanetResources` à 2h00)
4. Si la tâche réussit, Laravel envoie automatiquement un ping HTTP à l'URL du heartbeat
5. Laravel Forge reçoit le heartbeat et réinitialise le timer de monitoring
6. Si aucun heartbeat n'est reçu dans le délai spécifié, Forge vous envoie une notification

## Avantages

- ✅ **Monitoring proactif** : Vous êtes alerté immédiatement si une tâche échoue ou ne s'exécute pas
- ✅ **Pas de code supplémentaire** : Laravel gère automatiquement l'envoi des heartbeats
- ✅ **Flexible** : Les URLs sont configurées via des variables d'environnement, facilement modifiables
- ✅ **Sécurisé** : Les heartbeats ne sont envoyés qu'en cas de succès de la tâche

## Dépannage

### Les heartbeats ne sont pas envoyés

1. **Vérifier les variables d'environnement** :
   ```bash
   php artisan tinker
   >>> env('FORGE_HEARTBEAT_PLANET_RESOURCES')
   >>> env('FORGE_HEARTBEAT_AVATAR_RESOURCES')
   ```

2. **Vérifier que les URLs sont correctes** : Les URLs doivent être complètes et accessibles depuis le serveur

3. **Vérifier les logs** : Les erreurs de connexion HTTP sont généralement loggées dans les logs Laravel

4. **Tester manuellement** : Vous pouvez tester l'envoi d'un heartbeat avec curl :
   ```bash
   curl -X GET "https://forge.laravel.com/servers/{server-id}/sites/{site-id}/heartbeats/{heartbeat-id}"
   ```

### Les notifications ne sont pas reçues

1. Vérifiez les paramètres de notification dans Laravel Forge
2. Vérifiez que le délai "Notify me after" est approprié (les tâches peuvent prendre plusieurs minutes)
3. Vérifiez que les tâches s'exécutent réellement en consultant les logs Laravel

## Configuration en développement local

En développement local, les heartbeats ne sont pas nécessaires. Les variables d'environnement peuvent être laissées vides ou non définies. Laravel ignorera simplement les appels `thenPing()` si l'URL est vide ou null.

```env
# En développement local, ces variables peuvent être vides
FORGE_HEARTBEAT_PLANET_RESOURCES=
FORGE_HEARTBEAT_AVATAR_RESOURCES=
```

## Références

- [Documentation Laravel Forge - Scheduler](https://forge.laravel.com/docs/scheduler)
- [Documentation Laravel - Task Scheduling](https://laravel.com/docs/scheduling)
- [Documentation Laravel - Heartbeats](https://laravel.com/docs/scheduling#heartbeats)

