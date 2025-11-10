# Configuration des Queues - Space Xplorer

Les queues Laravel sont utilisées pour exécuter des tâches asynchrones, notamment la génération d'avatars qui prend ~20-30 secondes.

## Comportement en Local vs Production

### En Local (Développement)

**Par défaut, les queues sont synchrones** si aucun worker n'est lancé. Pour rendre les queues asynchrones :

1. **Lancer le worker de queue** :
   ```bash
   ./vendor/bin/sail artisan queue:work
   ```

2. **Ou utiliser le script dev** (déjà configuré) :
   ```bash
   ./vendor/bin/sail composer run dev
   ```
   Ce script lance automatiquement `queue:listen` avec les autres services.

### En Production

Les queues sont automatiquement asynchrones si un worker de queue est configuré (supervisor, Laravel Horizon, etc.).

## Configuration

### Queue Connection

Par défaut, le projet utilise `database` comme driver de queue (configuré dans `config/queue.php`).

Pour utiliser Redis (plus performant) :

```env
QUEUE_CONNECTION=redis
```

### Vérifier l'état des queues

```bash
# Voir les jobs en attente
./vendor/bin/sail artisan queue:monitor

# Voir les jobs échoués
./vendor/bin/sail artisan queue:failed

# Relancer un job échoué
./vendor/bin/sail artisan queue:retry {job-id}
```

## Listeners en Queue

### GenerateAvatar

Le listener `GenerateAvatar` est maintenant asynchrone :
- ✅ Ne bloque pas l'inscription de l'utilisateur
- ✅ S'exécute en arrière-plan
- ✅ Retry automatique (3 tentatives avec 30s de délai)
- ✅ Gestion des erreurs avec logging

### GenerateHomePlanet

Le listener `GenerateHomePlanet` reste synchrone car :
- ⚡ Génération rapide (pas d'appel API externe)
- ✅ Nécessaire immédiatement pour l'affichage du dashboard

## Commandes Utiles

```bash
# Lancer le worker de queue
./vendor/bin/sail artisan queue:work

# Lancer avec un timeout (utile pour le développement)
./vendor/bin/sail artisan queue:work --timeout=60

# Traiter une seule job (pour tester)
./vendor/bin/sail artisan queue:work --once

# Voir les jobs en attente dans la base de données
./vendor/bin/sail artisan tinker
>>> \Illuminate\Support\Facades\DB::table('jobs')->count()
```

## Dépannage

### Les avatars ne sont pas générés

1. Vérifier que le worker de queue tourne :
   ```bash
   ./vendor/bin/sail artisan queue:work
   ```

2. Vérifier les jobs échoués :
   ```bash
   ./vendor/bin/sail artisan queue:failed
   ```

3. Vérifier les logs :
   ```bash
   ./vendor/bin/sail artisan pail
   ```

### Les queues sont synchrones en local

C'est normal si aucun worker n'est lancé. Pour les rendre asynchrones :
- Lancer `./vendor/bin/sail artisan queue:work`
- Ou utiliser `./vendor/bin/sail composer run dev` qui lance automatiquement le worker

## Production

En production, utilisez un gestionnaire de processus comme Supervisor ou Laravel Horizon pour maintenir le worker de queue actif.

## Tests avec Événements et Listeners en Queue

Pour tester correctement les événements qui déclenchent des listeners en queue, consultez la documentation dédiée :

📖 **[Tester les Événements avec Listeners en Queue](./testing-events.md)**

Cette documentation explique :
- Pourquoi `Queue::fake()` ne fonctionne pas pour tester les listeners `ShouldQueue`
- Comment mock les services externes au lieu de fake la queue
- Les protections mises en place contre les appels externes et les timeouts
- Les bonnes pratiques pour tester avec des événements
