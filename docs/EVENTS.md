# Documentation des Événements - Audit Trail

Cette documentation décrit tous les événements disponibles dans l'application Space Xplorer pour le suivi complet des activités utilisateur (audit trail).

## Vue d'ensemble

Tous les événements suivent le pattern Laravel standard et utilisent les traits `Dispatchable` et `SerializesModels`. Ils sont dispatchés via `event()` ou `Event::dispatch()` et peuvent être écoutés via des Listeners.

## Événements d'Authentification

### UserRegistered

**Quand** : Lors de la création d'un nouveau compte utilisateur.

**Où** : `App\Services\AuthService::register()` et `registerFromArray()`

**Données** :
- `User $user` - L'utilisateur créé

**Exemple** :
```php
event(new UserRegistered($user));
```

**Listeners associés** :
- `GenerateHomePlanet` - Génère la planète d'accueil
- `GenerateAvatar` - Génère l'avatar de l'utilisateur
- `SendWelcomeMessage` - Envoie le message de bienvenue

---

### EmailVerified

**Quand** : Lorsque l'utilisateur vérifie son email avec succès.

**Où** : `App\Services\EmailVerificationService::verifyCode()`

**Données** :
- `User $user` - L'utilisateur dont l'email a été vérifié

**Exemple** :
```php
event(new EmailVerified($user));
```

---

### FirstLogin

**Quand** : Lors de la première connexion d'un utilisateur après la vérification de son email (sans sessions précédentes).

**Où** : `App\Services\AuthService::login()` et `loginFromCredentials()`

**Données** :
- `User $user` - L'utilisateur qui se connecte pour la première fois

**Conditions** :
- L'utilisateur doit avoir vérifié son email (`email_verified_at` non null)
- L'utilisateur ne doit pas avoir de sessions précédentes dans la table `sessions`

**Exemple** :
```php
event(new FirstLogin($user));
```

---

### UserLoggedIn

**Quand** : À chaque connexion réussie d'un utilisateur.

**Où** : `App\Services\AuthService::login()`, `loginFromCredentials()`, `register()`, `registerFromArray()`

**Données** :
- `User $user` - L'utilisateur qui se connecte

**Exemple** :
```php
event(new UserLoggedIn($user));
```

---

### UserLoggedOut

**Quand** : Lors de la déconnexion d'un utilisateur.

**Où** : `App\Services\AuthService::logout()`

**Données** :
- `User $user` - L'utilisateur qui se déconnecte

**Note** : L'événement n'est dispatché que si un utilisateur était authentifié.

**Exemple** :
```php
event(new UserLoggedOut($user));
```

---

### FailedLoginAttempt

**Quand** : Lors d'une tentative de connexion échouée (mauvais email ou mot de passe).

**Où** : `App\Services\AuthService::login()` et `loginFromCredentials()`

**Données** :
- `string $email` - L'email utilisé dans la tentative
- `?string $ipAddress` - L'adresse IP de la tentative (peut être null)
- `?string $userAgent` - Le user agent de la tentative (peut être null)

**Exemple** :
```php
event(new FailedLoginAttempt($email, $ipAddress, $userAgent));
```

**Utilisation** : Utile pour détecter les tentatives d'intrusion ou les attaques par force brute.

---

## Événements de Profil Utilisateur

### ProfileAccessed

**Quand** : Lorsqu'un utilisateur accède à sa page de profil.

**Où** : `App\Livewire\Profile::mount()`

**Données** :
- `User $user` - L'utilisateur qui accède à son profil

**Exemple** :
```php
event(new ProfileAccessed($user));
```

---

### DashboardAccessed

**Quand** : Lorsqu'un utilisateur accède à son dashboard.

**Où** : `App\Livewire\Dashboard::mount()`

**Données** :
- `User $user` - L'utilisateur qui accède au dashboard

**Exemple** :
```php
event(new DashboardAccessed($user));
```

---

### InboxAccessed

**Quand** : Lorsqu'un utilisateur accède à sa boîte de réception.

**Où** : `App\Livewire\Inbox::mount()`

**Données** :
- `User $user` - L'utilisateur qui accède à sa boîte de réception

**Exemple** :
```php
event(new InboxAccessed($user));
```

---

### UserProfileUpdated

**Quand** : Lorsqu'un utilisateur met à jour son profil (nom, email, ou avatar).

**Où** : `App\Http\Controllers\Api\UserController::update()` et `updateAvatar()`

**Données** :
- `User $user` - L'utilisateur dont le profil a été mis à jour
- `array $changedAttributes` - Tableau des attributs modifiés avec leurs valeurs anciennes et nouvelles

**Format de `$changedAttributes`** :
```php
[
    'name' => ['old' => 'Ancien nom', 'new' => 'Nouveau nom'],
    'email' => ['old' => 'ancien@email.com', 'new' => 'nouveau@email.com'],
    'avatar_url' => ['old' => 'ancien/path.jpg', 'new' => 'nouveau/path.jpg'],
]
```

**Exemple** :
```php
event(new UserProfileUpdated($user, [
    'name' => ['old' => 'John', 'new' => 'Jane'],
]));
```

---

### EmailChanged

**Quand** : Lorsqu'un utilisateur change son adresse email.

**Où** : `App\Http\Controllers\Api\UserController::update()`

**Données** :
- `User $user` - L'utilisateur dont l'email a été changé
- `string $oldEmail` - L'ancienne adresse email
- `string $newEmail` - La nouvelle adresse email

**Exemple** :
```php
event(new EmailChanged($user, 'ancien@email.com', 'nouveau@email.com'));
```

**Note** : Cet événement est dispatché en plus de `UserProfileUpdated`.

---

### AvatarChanged

**Quand** : Lorsqu'un utilisateur change son avatar.

**Où** : `App\Http\Controllers\Api\UserController::updateAvatar()`

**Données** :
- `User $user` - L'utilisateur dont l'avatar a été changé
- `?string $oldAvatarPath` - L'ancien chemin de l'avatar (peut être null)
- `string $newAvatarPath` - Le nouveau chemin de l'avatar

**Exemple** :
```php
event(new AvatarChanged($user, 'old/avatar.jpg', 'new/avatar.jpg'));
```

**Note** : Cet événement est dispatché en plus de `UserProfileUpdated`.

---

### AvatarGenerated

**Quand** : Lorsqu'un avatar est généré pour un utilisateur (lors de l'inscription).

**Où** : `App\Listeners\GenerateAvatar::handle()`

**Données** :
- `User $user` - L'utilisateur pour lequel l'avatar a été généré
- `string $avatarPath` - Le chemin de l'avatar généré
- `string $avatarUrl` - L'URL complète de l'avatar

**Exemple** :
```php
event(new AvatarGenerated($user, 'avatars/avatar123.jpg', 'https://example.com/avatars/avatar123.jpg'));
```

---

## Événements de Sécurité

### PasswordChanged

**Quand** : Lorsqu'un utilisateur change son mot de passe (via réinitialisation).

**Où** : `App\Services\PasswordResetService::reset()`

**Données** :
- `User $user` - L'utilisateur dont le mot de passe a été changé

**Exemple** :
```php
event(new PasswordChanged($user));
```

**Note** : Cet événement est dispatché en plus de `PasswordResetCompleted`.

---

### PasswordResetRequested

**Quand** : Lorsqu'un utilisateur demande une réinitialisation de mot de passe.

**Où** : `App\Services\PasswordResetService::sendResetLink()`

**Données** :
- `string $email` - L'email pour lequel la réinitialisation a été demandée

**Exemple** :
```php
event(new PasswordResetRequested($email));
```

---

### PasswordResetCompleted

**Quand** : Lorsqu'un utilisateur complète avec succès la réinitialisation de son mot de passe.

**Où** : `App\Services\PasswordResetService::reset()`

**Données** :
- `User $user` - L'utilisateur qui a réinitialisé son mot de passe

**Exemple** :
```php
event(new PasswordResetCompleted($user));
```

**Note** : Cet événement est dispatché en plus de `PasswordChanged`.

---

### SessionExpired

**Quand** : Lorsqu'une session utilisateur expire.

**Où** : Non intégré automatiquement (disponible pour intégration future via middleware)

**Données** :
- `User $user` - L'utilisateur dont la session a expiré

**Note** : Cet événement est créé mais non dispatché automatiquement. Pour l'intégrer, créer un middleware personnalisé qui détecte les sessions expirées.

**Exemple** :
```php
event(new SessionExpired($user));
```

---

## Événements de Jeu

### PlanetCreated

**Quand** : Lorsqu'une planète est créée.

**Où** : `App\Services\PlanetGeneratorService::generatePlanet()`

**Données** :
- `Planet $planet` - La planète créée

**Exemple** :
```php
event(new PlanetCreated($planet));
```

**Listeners associés** :
- `GeneratePlanetImage` - Génère l'image de la planète
- `GeneratePlanetVideo` - Génère la vidéo de la planète

---

### PlanetExplored

**Quand** : Lorsqu'un utilisateur explore une planète.

**Où** : `App\Http\Controllers\Api\PlanetController::explore()`

**Données** :
- `User $user` - L'utilisateur qui explore
- `Planet $planet` - La planète explorée

**Exemple** :
```php
event(new PlanetExplored($user, $planet));
```

---

### DiscoveryMade

**Quand** : Lorsqu'un utilisateur fait une découverte (planète spéciale, etc.).

**Où** : Divers endroits selon le type de découverte

**Données** :
- `User $user` - L'utilisateur qui fait la découverte
- `string $discoveryType` - Le type de découverte (ex: 'planet', 'special')
- `array $discoveryData` - Données supplémentaires sur la découverte

**Exemple** :
```php
event(new DiscoveryMade($user, 'planet', ['planet_id' => $planet->id]));
```

---

### PlanetImageGenerated

**Quand** : Lorsqu'une image de planète est générée.

**Où** : `App\Listeners\GeneratePlanetImage::handle()`

**Données** :
- `Planet $planet` - La planète pour laquelle l'image a été générée
- `string $imagePath` - Le chemin de l'image générée
- `string $imageUrl` - L'URL complète de l'image

**Exemple** :
```php
event(new PlanetImageGenerated($planet, 'planets/image123.jpg', 'https://example.com/planets/image123.jpg'));
```

---

### PlanetVideoGenerated

**Quand** : Lorsqu'une vidéo de planète est générée.

**Où** : `App\Listeners\GeneratePlanetVideo::handle()`

**Données** :
- `Planet $planet` - La planète pour laquelle la vidéo a été générée
- `string $videoPath` - Le chemin de la vidéo générée
- `string $videoUrl` - L'URL complète de la vidéo

**Exemple** :
```php
event(new PlanetVideoGenerated($planet, 'planets/video123.mp4', 'https://example.com/planets/video123.mp4'));
```

---

## Événements de Ressources

### ResourceGenerated

**Quand** : Lorsqu'une ressource (avatar, image de planète, etc.) est générée.

**Où** : `App\Services\ResourceGenerationService::generate()`

**Données** :
- `Resource $resource` - La ressource générée

**Exemple** :
```php
event(new ResourceGenerated($resource));
```

---

### ResourceApproved

**Quand** : Lorsqu'une ressource est approuvée par un administrateur.

**Où** : `App\Http\Controllers\Admin\ResourceController::approve()` et `App\Livewire\Admin\ResourceReview::approve()`

**Données** :
- `Resource $resource` - La ressource approuvée
- `User $admin` - L'administrateur qui a approuvé

**Exemple** :
```php
event(new ResourceApproved($resource, $admin));
```

---

### ResourceRejected

**Quand** : Lorsqu'une ressource est rejetée par un administrateur.

**Où** : `App\Http\Controllers\Admin\ResourceController::reject()` et `App\Livewire\Admin\ResourceReview::reject()`

**Données** :
- `Resource $resource` - La ressource rejetée
- `User $admin` - L'administrateur qui a rejeté
- `string $reason` - La raison du rejet

**Exemple** :
```php
event(new ResourceRejected($resource, $admin, 'Contenu inapproprié'));
```

---

## Événements de Suppression

### UserDeleting

**Quand** : Avant qu'un utilisateur soit supprimé (soft delete).

**Où** : `App\Observers\UserObserver::deleting()`

**Données** :
- `User $user` - L'utilisateur qui va être supprimé

**Exemple** :
```php
event(new UserDeleting($user));
```

---

### UserDeleted

**Quand** : Après qu'un utilisateur soit supprimé (soft delete).

**Où** : `App\Observers\UserObserver::deleted()`

**Données** :
- `User $user` - L'utilisateur qui a été supprimé

**Exemple** :
```php
event(new UserDeleted($user));
```

**Listeners associés** :
- `CleanupUserData` - Nettoie les données associées à l'utilisateur

---

## Utilisation des Événements

### Écouter un événement

Pour écouter un événement, créer un Listener dans `app/Listeners/` et l'enregistrer dans `app/Providers/EventServiceProvider.php` :

```php
// app/Listeners/LogUserActivity.php
namespace App\Listeners;

use App\Events\UserLoggedIn;
use Illuminate\Support\Facades\Log;

class LogUserActivity
{
    public function handle(UserLoggedIn $event): void
    {
        Log::info('User logged in', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'timestamp' => now(),
        ]);
    }
}
```

```php
// app/Providers/EventServiceProvider.php
use App\Events\UserLoggedIn;
use App\Listeners\LogUserActivity;

protected $listen = [
    UserLoggedIn::class => [
        LogUserActivity::class,
    ],
];
```

### Tester les événements

Utiliser `Event::fake()` dans les tests :

```php
use App\Events\UserLoggedIn;
use Illuminate\Support\Facades\Event;

it('dispatches UserLoggedIn event', function () {
    Event::fake([UserLoggedIn::class]);

    // ... code qui dispatch l'événement ...

    Event::assertDispatched(UserLoggedIn::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});
```

---

## Audit Trail Complet

Pour un audit trail complet, tous les événements suivants doivent être écoutés et enregistrés :

### Cycle de vie utilisateur
1. `UserRegistered` - Création du compte
2. `EmailVerified` - Vérification de l'email
3. `FirstLogin` - Première connexion
4. `UserLoggedIn` - Connexions suivantes
5. `UserLoggedOut` - Déconnexions
6. `FailedLoginAttempt` - Tentatives échouées
7. `UserDeleted` - Suppression du compte

### Activités utilisateur
8. `ProfileAccessed` - Accès au profil
9. `DashboardAccessed` - Accès au dashboard
10. `InboxAccessed` - Accès à la boîte de réception
11. `UserProfileUpdated` - Modifications du profil
12. `EmailChanged` - Changement d'email
13. `AvatarChanged` - Changement d'avatar
14. `PasswordChanged` - Changement de mot de passe

### Activités de jeu
15. `PlanetCreated` - Création de planète
16. `PlanetExplored` - Exploration de planète
17. `DiscoveryMade` - Découvertes

---

## Notes importantes

- Tous les événements ont `$shouldBroadcast = false` par défaut (pas de broadcasting)
- Les événements utilisent `SerializesModels` pour éviter les problèmes de sérialisation
- Les événements peuvent être dispatchés de manière synchrone ou asynchrone selon la configuration
- Pour un audit trail complet, créer un listener global qui enregistre tous les événements dans une table de logs

---

## Exemple de Listener d'Audit Trail

```php
// app/Listeners/LogAuditTrail.php
namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogAuditTrail
{
    public function handle($event): void
    {
        // Enregistrer dans la base de données
        DB::table('audit_logs')->insert([
            'event' => get_class($event),
            'user_id' => $event->user->id ?? null,
            'data' => json_encode($event),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        // Ou simplement logger
        Log::info('Audit trail', [
            'event' => get_class($event),
            'user_id' => $event->user->id ?? null,
            'data' => $event,
        ]);
    }
}
```

