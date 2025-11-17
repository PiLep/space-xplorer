<?php

namespace App\Listeners;

use App\Events\AvatarChanged;
use App\Events\AvatarGenerated;
use App\Events\DashboardAccessed;
use App\Events\DiscoveryMade;
use App\Events\EmailChanged;
use App\Events\EmailVerified;
use App\Events\FailedLoginAttempt;
use App\Events\FirstLogin;
use App\Events\InboxAccessed;
use App\Events\MessageDeleted;
use App\Events\MessagePermanentlyDeleted;
use App\Events\MessageRead;
use App\Events\MessageReceived;
use App\Events\MessageRestored;
use App\Events\PasswordChanged;
use App\Events\PasswordResetCompleted;
use App\Events\PasswordResetRequested;
use App\Events\PlanetCreated;
use App\Events\PlanetExplored;
use App\Events\PlanetImageGenerated;
use App\Events\PlanetVideoGenerated;
use App\Events\ProfileAccessed;
use App\Events\ResourceApproved;
use App\Events\ResourceGenerated;
use App\Events\ResourceRejected;
use App\Events\SessionExpired;
use App\Events\UserDeleted;
use App\Events\UserDeleting;
use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Events\UserProfileUpdated;
use App\Events\UserRegistered;
use App\Jobs\LogEventToDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionProperty;

class LogEvent
{
    /**
     * Handle UserLoggedIn event.
     */
    public function handleUserLoggedIn(UserLoggedIn $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle UserRegistered event.
     */
    public function handleUserRegistered(UserRegistered $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle DashboardAccessed event.
     */
    public function handleDashboardAccessed(DashboardAccessed $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle ProfileAccessed event.
     */
    public function handleProfileAccessed(ProfileAccessed $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle InboxAccessed event.
     */
    public function handleInboxAccessed(InboxAccessed $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle UserProfileUpdated event.
     */
    public function handleUserProfileUpdated(UserProfileUpdated $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle UserLoggedOut event.
     */
    public function handleUserLoggedOut(UserLoggedOut $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle FirstLogin event.
     */
    public function handleFirstLogin(FirstLogin $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle EmailVerified event.
     */
    public function handleEmailVerified(EmailVerified $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle EmailChanged event.
     */
    public function handleEmailChanged(EmailChanged $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle AvatarChanged event.
     */
    public function handleAvatarChanged(AvatarChanged $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle AvatarGenerated event.
     */
    public function handleAvatarGenerated(AvatarGenerated $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle PasswordChanged event.
     */
    public function handlePasswordChanged(PasswordChanged $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle PasswordResetRequested event.
     */
    public function handlePasswordResetRequested(PasswordResetRequested $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle PasswordResetCompleted event.
     */
    public function handlePasswordResetCompleted(PasswordResetCompleted $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle FailedLoginAttempt event.
     */
    public function handleFailedLoginAttempt(FailedLoginAttempt $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle SessionExpired event.
     */
    public function handleSessionExpired(SessionExpired $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle PlanetCreated event.
     */
    public function handlePlanetCreated(PlanetCreated $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle PlanetExplored event.
     */
    public function handlePlanetExplored(PlanetExplored $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle DiscoveryMade event.
     */
    public function handleDiscoveryMade(DiscoveryMade $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle PlanetImageGenerated event.
     */
    public function handlePlanetImageGenerated(PlanetImageGenerated $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle PlanetVideoGenerated event.
     */
    public function handlePlanetVideoGenerated(PlanetVideoGenerated $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle ResourceGenerated event.
     */
    public function handleResourceGenerated(ResourceGenerated $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle ResourceApproved event.
     */
    public function handleResourceApproved(ResourceApproved $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle ResourceRejected event.
     */
    public function handleResourceRejected(ResourceRejected $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle UserDeleted event.
     */
    public function handleUserDeleted(UserDeleted $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle UserDeleting event.
     */
    public function handleUserDeleting(UserDeleting $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle MessageReceived event.
     */
    public function handleMessageReceived(MessageReceived $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle MessageRead event.
     */
    public function handleMessageRead(MessageRead $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle MessageDeleted event.
     */
    public function handleMessageDeleted(MessageDeleted $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle MessageRestored event.
     */
    public function handleMessageRestored(MessageRestored $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Handle MessagePermanentlyDeleted event.
     */
    public function handleMessagePermanentlyDeleted(MessagePermanentlyDeleted $event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Generic handle method called by Laravel for all events.
     *
     * This method is called when the listener is registered in EventServiceProvider.
     * It delegates to handleEvent() which processes all event types.
     */
    public function handle($event): void
    {
        $this->handleEvent($event);
    }

    /**
     * Generic event handler that extracts data and dispatches job.
     *
     * Extrait les données de l'événement de manière synchrone (rapide)
     * puis dispatche un job pour l'enregistrement en base de données (asynchrone).
     * Cette approche évite les problèmes de sérialisation en queue.
     *
     * @param  mixed  $event  L'événement à traiter (peut être n'importe quel type d'événement)
     */
    protected function handleEvent(mixed $event): void
    {
        try {
            // Ignorer si ce n'est pas un objet
            if (! is_object($event)) {
                return;
            }

            $eventType = get_class($event);

            // Ignorer les événements internes de Laravel (framework events)
            // Ne traiter que les événements de notre application (namespace App\Events)
            if (! str_starts_with($eventType, 'App\\Events\\')) {
                return;
            }

            Log::info('LogEvent: Processing App event', ['event_type' => $eventType]);

            // Extraire les informations de l'événement de manière synchrone (rapide)
            $userId = $this->extractUserId($event);
            $eventData = $this->extractEventData($event);

            Log::info('LogEvent: Extracted data', [
                'event_type' => $eventType,
                'user_id' => $userId,
                'data_keys' => array_keys($eventData),
            ]);

            // Dispatcher un job avec les données extraites (asynchrone)
            // Cela évite les problèmes de sérialisation car on passe des données simples
            LogEventToDatabase::dispatch(
                $eventType,
                $userId,
                $eventData,
                $this->getIpAddress(),
                $this->getUserAgent(),
                $this->getSessionId()
            );

            Log::info('LogEvent: Job dispatched', [
                'event_type' => $eventType,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            // Log l'erreur mais ne bloque pas l'application
            // Les événements ne doivent jamais faire échouer l'application
            $eventType = is_object($event) ? get_class($event) : 'unknown';
            Log::error('Failed to process event for logging', [
                'event_type' => $eventType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Check if event should be ignored.
     *
     * Ignore les événements internes de Laravel qui ne sont pas pertinents pour l'audit trail.
     */
    protected function shouldIgnoreEvent(string $eventType): bool
    {
        // Ignorer les événements du framework Laravel
        $ignoredNamespaces = [
            'Illuminate\\',
            'Laravel\\',
            'Symfony\\',
        ];

        foreach ($ignoredNamespaces as $namespace) {
            if (str_starts_with($eventType, $namespace)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get IP address safely (works in CLI context).
     */
    protected function getIpAddress(): ?string
    {
        try {
            return request()->ip();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get user agent safely (works in CLI context).
     */
    protected function getUserAgent(): ?string
    {
        try {
            return request()->userAgent();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get session ID safely (works in CLI context).
     */
    protected function getSessionId(): ?string
    {
        try {
            return session()->getId();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Extract user ID from event.
     *
     * Tente d'extraire l'ID utilisateur de différentes façons :
     * 1. Propriété publique $user
     * 2. Propriété publique $recipient (pour MessageReceived)
     * 3. Méthode getUser()
     * 4. Propriété publique user_id
     * 5. Utilisateur authentifié actuel
     *
     * Retourne toujours une string (ULID) ou null.
     */
    public function extractUserId($event): ?string
    {
        $userId = null;

        // Essayer d'accéder à la propriété $user directement
        if (isset($event->user) && is_object($event->user)) {
            $userId = $event->user->id ?? null;
        }

        // Essayer d'accéder à la propriété $recipient (pour MessageReceived)
        if (! $userId && isset($event->recipient) && is_object($event->recipient)) {
            $userId = $event->recipient->id ?? null;
        }

        // Essayer d'accéder à la propriété user_id
        if (! $userId && isset($event->user_id)) {
            $userId = $event->user_id;
        }

        // Essayer d'appeler une méthode getUser()
        if (! $userId && method_exists($event, 'getUser')) {
            $user = $event->getUser();
            if ($user && is_object($user)) {
                $userId = $user->id ?? null;
            }
        }

        // Utiliser la réflexion pour trouver une propriété user ou recipient
        if (! $userId) {
            try {
                $reflection = new ReflectionClass($event);
                $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

                foreach ($properties as $property) {
                    $propertyName = $property->getName();
                    $propertyNameLower = strtolower($propertyName);
                    if ($propertyNameLower === 'user' || $propertyNameLower === 'recipient') {
                        $user = $property->getValue($event);
                        if ($user && is_object($user) && isset($user->id)) {
                            $userId = $user->id;
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Ignorer les erreurs de réflexion
            }
        }

        // Fallback: utiliser l'utilisateur authentifié actuel
        if (! $userId) {
            $userId = Auth::id();
        }

        // Convertir en string si nécessaire (pour ULIDs) et normaliser les chaînes vides en null
        if ($userId === null || $userId === '') {
            return null;
        }

        return (string) $userId;
    }

    /**
     * Extract event data for storage.
     *
     * Sérialise les données de l'événement de manière sécurisée,
     * en évitant les références circulaires et les objets non sérialisables.
     */
    public function extractEventData($event): array
    {
        $data = [];

        try {
            $reflection = new ReflectionClass($event);
            $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

            foreach ($properties as $property) {
                $propertyName = $property->getName();

                // Ignorer les propriétés qui ne doivent pas être sérialisées
                if (in_array($propertyName, ['shouldBroadcast', 'connection', 'queue', 'chainConnection', 'chainQueue', 'chainCatchCallbacks', 'chained'])) {
                    continue;
                }

                // Vérifier si la propriété est initialisée (pour les propriétés typées PHP 7.4+)
                if ($property->isInitialized($event)) {
                    $value = $property->getValue($event);
                    // Sérialiser les valeurs de manière sécurisée
                    $data[$propertyName] = $this->serializeValue($value);
                }
            }
        } catch (\Exception $e) {
            // En cas d'erreur, retourner au moins le type d'événement
            $data['_error'] = 'Failed to serialize event data: '.$e->getMessage();
        }

        return $data;
    }

    /**
     * Serialize a value safely.
     *
     * Convertit une valeur en format sérialisable (array, string, int, etc.)
     * en évitant les objets complexes et les références circulaires.
     */
    protected function serializeValue($value): mixed
    {
        // Valeurs primitives
        if (is_null($value) || is_scalar($value)) {
            return $value;
        }

        // Tableaux
        if (is_array($value)) {
            return array_map([$this, 'serializeValue'], $value);
        }

        // Objets Eloquent - extraire seulement l'ID et quelques attributs clés
        if (is_object($value)) {
            // Si c'est un modèle Eloquent, extraire l'ID et quelques attributs
            if (method_exists($value, 'getKey')) {
                return [
                    '_type' => get_class($value),
                    'id' => $value->getKey(),
                    'class' => get_class($value),
                ];
            }

            // Pour les autres objets, essayer de convertir en array
            if (method_exists($value, 'toArray')) {
                return $value->toArray();
            }

            // Sinon, retourner juste le type
            return [
                '_type' => get_class($value),
                '_string' => (string) $value,
            ];
        }

        // Par défaut, convertir en string
        return (string) $value;
    }
}
