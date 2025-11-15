<?php

namespace App\Services;

use App\Events\MessageReceived;
use App\Models\Message;
use App\Models\Planet;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service for managing system messages.
 *
 * This service handles the creation and management of system messages
 * sent by Stellar to users. Messages are automatically generated during
 * important game events (registration, discoveries, etc.).
 */
class MessageService
{
    /**
     * Create a generic system message.
     *
     * @param  User  $recipient  The user receiving the message
     * @param  string  $subject  The message subject
     * @param  string  $content  The message content
     * @param  array  $metadata  Optional metadata (links to planets, systems, etc.)
     * @return Message The created message
     */
    public function createSystemMessage(
        User $recipient,
        string $subject,
        string $content,
        array $metadata = []
    ): Message {
        $message = Message::create([
            'sender_id' => null, // System messages have no sender
            'recipient_id' => $recipient->id,
            'type' => 'system',
            'subject' => $subject,
            'content' => $content,
            'metadata' => $metadata,
        ]);

        event(new MessageReceived($message, $recipient));

        return $message;
    }

    /**
     * Create a welcome message from Stellar.
     *
     * @param  User  $recipient  The newly registered user
     * @return Message The created welcome message
     */
    public function createWelcomeMessage(User $recipient): Message
    {
        $template = $this->getTemplate('welcome', [
            'name' => $recipient->name,
            'matricule' => $recipient->matricule,
        ]);

        $message = Message::create([
            'sender_id' => null,
            'recipient_id' => $recipient->id,
            'type' => 'welcome',
            'subject' => 'Bienvenue dans l\'univers Stellar',
            'content' => $template,
            'metadata' => [
                'type' => 'welcome',
                'user_id' => $recipient->id,
            ],
        ]);

        event(new MessageReceived($message, $recipient));

        return $message;
    }

    /**
     * Create a discovery message for a planet or special discovery.
     *
     * @param  User  $recipient  The user who made the discovery
     * @param  Planet|array  $discovery  The planet discovered or discovery data array
     * @param  string|null  $subject  Optional custom subject
     * @param  string|null  $content  Optional custom content
     * @return Message The created discovery message
     */
    public function createDiscoveryMessage(
        User $recipient,
        Planet|array $discovery,
        ?string $subject = null,
        ?string $content = null
    ): Message {
        if ($discovery instanceof Planet) {
            $planet = $discovery;
            $discoveryData = [
                'planet_id' => $planet->id,
                'planet_name' => $planet->name,
                'planet_type' => $planet->type,
                'planet_size' => $planet->size,
                'planet_temperature' => $planet->temperature,
                'planet_atmosphere' => $planet->atmosphere,
                'planet_terrain' => $planet->terrain,
                'planet_resources' => $planet->resources,
            ];

            $subject = $subject ?? 'Nouvelle planète découverte';
            $content = $content ?? $this->getTemplate('discovery_planet', [
                'name' => $recipient->name,
                'planet_name' => $planet->name,
                'planet_type' => $planet->type,
                'planet_size' => $planet->size,
                'planet_temperature' => $planet->temperature,
                'planet_atmosphere' => $planet->atmosphere,
                'planet_terrain' => $planet->terrain,
                'planet_resources' => $planet->resources,
                'planet_description' => $planet->description,
            ]);
        } else {
            // Array-based discovery (for special discoveries)
            $discoveryData = $discovery;

            // Extract 'type' from discovery data if it exists and rename it to 'discovery_type'
            // to avoid overwriting the message type in metadata
            if (isset($discoveryData['type'])) {
                $discoveryData['discovery_type'] = $discoveryData['type'];
                unset($discoveryData['type']);
            }

            $subject = $subject ?? 'Découverte spéciale';
            $content = $content ?? $this->getTemplate('discovery_special', [
                'name' => $recipient->name,
                'discovery_type' => $discoveryData['discovery_type'] ?? 'inconnue',
                'discovery_data' => $discoveryData,
            ]);
        }

        $message = Message::create([
            'sender_id' => null,
            'recipient_id' => $recipient->id,
            'type' => 'discovery',
            'subject' => $subject,
            'content' => $content,
            'metadata' => array_merge([
                'type' => 'discovery',
            ], $discoveryData),
        ]);

        event(new MessageReceived($message, $recipient));

        return $message;
    }

    /**
     * Create a mission message from Stellar.
     *
     * @param  User  $recipient  The user receiving the mission
     * @param  string  $subject  The mission subject
     * @param  string  $content  The mission content
     * @param  array  $metadata  Optional metadata (mission details, rewards, etc.)
     * @return Message The created mission message
     */
    public function createMissionMessage(
        User $recipient,
        string $subject,
        string $content,
        array $metadata = []
    ): Message {
        $message = Message::create([
            'sender_id' => null,
            'recipient_id' => $recipient->id,
            'type' => 'mission',
            'subject' => $subject,
            'content' => $content,
            'metadata' => array_merge([
                'type' => 'mission',
            ], $metadata),
        ]);

        event(new MessageReceived($message, $recipient));

        return $message;
    }

    /**
     * Create an alert message from Stellar.
     *
     * @param  User  $recipient  The user receiving the alert
     * @param  string  $subject  The alert subject
     * @param  string  $content  The alert content
     * @param  array  $metadata  Optional metadata (alert type, priority, etc.)
     * @return Message The created alert message
     */
    public function createAlertMessage(
        User $recipient,
        string $subject,
        string $content,
        array $metadata = []
    ): Message {
        $message = Message::create([
            'sender_id' => null,
            'recipient_id' => $recipient->id,
            'type' => 'alert',
            'subject' => $subject,
            'content' => $content,
            'is_important' => true, // Alerts are always important
            'metadata' => array_merge([
                'type' => 'alert',
            ], $metadata),
        ]);

        event(new MessageReceived($message, $recipient));

        return $message;
    }

    /**
     * Get messages for a user with optional filters and pagination.
     *
     * @param  User  $user  The user to get messages for
     * @param  string|null  $filter  Filter by status: 'all', 'unread', 'read', 'trash'
     * @param  string|null  $type  Filter by message type
     * @param  int  $perPage  Number of messages per page (default: 20)
     * @return LengthAwarePaginator|Collection Paginated or collection of messages
     */
    public function getMessagesForUser(
        User $user,
        ?string $filter = 'all',
        ?string $type = null,
        int $perPage = 20
    ): LengthAwarePaginator|Collection {
        $query = Message::forUser($user)
            ->orderBy('created_at', 'desc');

        // Apply status filter
        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->read();
        } elseif ($filter === 'trash') {
            // Show only trashed messages
            $query->trashed();
        } else {
            // For 'all' filter, exclude trashed messages by default
            // (SoftDeletes automatically excludes them, but we ensure it)
        }

        // Apply type filter
        if ($type !== null) {
            $query->byType($type);
        }

        // Return paginated results
        return $query->paginate($perPage);
    }

    /**
     * Get message template by type with variable substitution.
     *
     * @param  string  $type  Template type (welcome, discovery_planet, discovery_special, etc.)
     * @param  array  $variables  Variables to substitute in the template
     * @return string The rendered template content
     */
    private function getTemplate(string $type, array $variables = []): string
    {
        $templates = [
            'welcome' => <<<'TEMPLATE'
Explorateur {name} ({matricule}),

Votre intégration dans le programme d'exploration Stellar a été validée. Vous êtes désormais assigné à la Division Exploration sous le matricule {matricule}.

Votre affectation initiale vous a été assignée. Consultez votre terminal pour accéder aux données de votre planète d'origine et aux protocoles opérationnels.

Votre productivité sera surveillée et évaluée selon les critères standards de performance Stellar. Toute déviation des protocoles sera signalée automatiquement.

Objectifs assignés :
- Exploration et cartographie des systèmes stellaires
- Collecte de données planétaires
- Conformité aux protocoles de sécurité Stellar

Accédez à votre terminal pour commencer votre mission.

STELLAR CORPORATION
Division Exploration
Système automatisé de communication

---
Note importante : Conformément à l'article 47-B du contrat d'intégration Stellar, toute période probatoire non validée entraîne l'activation automatique de la clause de remboursement des frais de formation et d'équipement. Votre performance sera évaluée selon les critères standards. Assurez-vous de respecter les objectifs assignés.
TEMPLATE,

            'discovery_planet' => <<<'TEMPLATE'
Explorateur {name},

Félicitations ! Vous avez découvert une nouvelle planète : **{planet_name}**

**Caractéristiques :**
- Type : {planet_type}
- Taille : {planet_size}
- Température : {planet_temperature}
- Atmosphère : {planet_atmosphere}
- Terrain : {planet_terrain}
- Ressources : {planet_resources}

{planet_description}

Cette découverte a été enregistrée dans vos archives. Continuez votre exploration pour découvrir d'autres mondes fascinants.

Bonne exploration,
L'équipe Stellar
TEMPLATE,

            'discovery_special' => <<<'TEMPLATE'
Explorateur {name},

Félicitations ! Vous avez fait une découverte spéciale de type : **{discovery_type}**

Cette découverte exceptionnelle a été enregistrée dans vos archives. Continuez votre exploration pour découvrir d'autres merveilles de l'univers.

Bonne exploration,
L'équipe Stellar
TEMPLATE,
        ];

        $template = $templates[$type] ?? 'Message système de Stellar.';

        // Replace variables in template
        foreach ($variables as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            $template = str_replace('{'.$key.'}', $value ?? '', $template);
        }

        return $template;
    }
}
