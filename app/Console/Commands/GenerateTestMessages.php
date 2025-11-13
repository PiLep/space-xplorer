<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\Planet;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Console\Command;

class GenerateTestMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:generate 
                            {--email= : Email of the user to generate messages for}
                            {--count=5 : Number of messages to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test messages for a user (welcome, discovery, mission, alert, system)';

    /**
     * Execute the console command.
     */
    public function handle(MessageService $messageService): int
    {
        $email = $this->option('email');
        $count = (int) $this->option('count');

        // Get user
        if ($email) {
            $user = User::where('email', $email)->first();
            if (! $user) {
                $this->error("User with email '{$email}' not found.");

                return Command::FAILURE;
            }
        } else {
            // Get first user or prompt
            $user = User::first();
            if (! $user) {
                $this->error('No users found in the database.');

                return Command::FAILURE;
            }
            $this->info("Using user: {$user->email} ({$user->name})");
        }

        $this->info("Generating {$count} test messages for {$user->name} ({$user->email})...");
        $this->newLine();

        $messagesCreated = [];

        // Message types to generate
        $messageTypes = [
            'welcome',
            'discovery',
            'mission',
            'alert',
            'system',
        ];

        for ($i = 0; $i < $count; $i++) {
            $type = $messageTypes[$i % count($messageTypes)];
            $message = $this->generateMessageByType($messageService, $user, $type, $i + 1);
            $messagesCreated[] = $message;
            $this->line("  ✓ Created {$type} message: {$message->subject}");
        }

        $this->newLine();
        $this->info("Successfully created {$count} messages!");
        $this->table(
            ['Type', 'Subject', 'Read', 'Important'],
            array_map(function ($msg) {
                return [
                    $msg->type,
                    $msg->subject,
                    $msg->is_read ? 'Yes' : 'No',
                    $msg->is_important ? 'Yes' : 'No',
                ];
            }, $messagesCreated)
        );

        return Command::SUCCESS;
    }

    /**
     * Generate a message by type.
     */
    private function generateMessageByType(
        MessageService $messageService,
        User $user,
        string $type,
        int $index
    ): Message {
        return match ($type) {
            'welcome' => $messageService->createWelcomeMessage($user),
            'discovery' => $this->createDiscoveryMessage($messageService, $user, $index),
            'mission' => $this->createMissionMessage($messageService, $user, $index),
            'alert' => $this->createAlertMessage($messageService, $user, $index),
            'system' => $this->createSystemMessage($messageService, $user, $index),
            default => $messageService->createSystemMessage(
                $user,
                'Message système',
                'Ceci est un message système de test.'
            ),
        };
    }

    /**
     * Create a discovery message.
     */
    private function createDiscoveryMessage(MessageService $messageService, User $user, int $index): Message
    {
        // Try to get a planet, or create a fake discovery
        $planet = Planet::inRandomOrder()->first();

        if ($planet) {
            return $messageService->createDiscoveryMessage(
                $user,
                $planet,
                "Découverte de planète #{$index}",
                null
            );
        }

        // Fallback to array-based discovery
        return $messageService->createDiscoveryMessage(
            $user,
            [
                'type' => 'anomalie_spatiale',
                'name' => "Anomalie Alpha-{$index}",
                'description' => 'Une anomalie spatiale intéressante a été détectée.',
            ],
            "Découverte spéciale #{$index}",
            null
        );
    }

    /**
     * Create a mission message.
     */
    private function createMissionMessage(MessageService $messageService, User $user, int $index): Message
    {
        $missions = [
            [
                'subject' => 'Mission d\'exploration Alpha',
                'content' => "Explorateur {$user->name},\n\nStellar vous assigne une mission d'exploration dans le secteur Alpha-{$index}. Votre objectif est de cartographier au moins 3 planètes dans cette zone.\n\nRécompense : 500 crédits stellaires\n\nBonne chance,\nL'équipe Stellar",
                'metadata' => [
                    'mission_id' => "M-{$index}",
                    'reward' => 500,
                    'sector' => "Alpha-{$index}",
                ],
            ],
            [
                'subject' => 'Mission de collecte de ressources',
                'content' => "Explorateur {$user->name},\n\nNous avons besoin de ressources rares pour nos recherches. Collectez au moins 10 unités de ressources rares sur les planètes que vous explorerez.\n\nRécompense : 750 crédits stellaires\n\nBonne chance,\nL'équipe Stellar",
                'metadata' => [
                    'mission_id' => "M-{$index}",
                    'reward' => 750,
                    'resource_type' => 'rare',
                ],
            ],
            [
                'subject' => 'Mission de découverte de système',
                'content' => "Explorateur {$user->name},\n\nUne mission importante vous attend : découvrez un nouveau système stellaire inexploré. Cette découverte pourrait révolutionner notre compréhension de l'univers.\n\nRécompense : 1000 crédits stellaires\n\nBonne chance,\nL'équipe Stellar",
                'metadata' => [
                    'mission_id' => "M-{$index}",
                    'reward' => 1000,
                    'mission_type' => 'system_discovery',
                ],
            ],
        ];

        $mission = $missions[($index - 1) % count($missions)];

        return $messageService->createMissionMessage(
            $user,
            $mission['subject'],
            $mission['content'],
            $mission['metadata']
        );
    }

    /**
     * Create an alert message.
     */
    private function createAlertMessage(MessageService $messageService, User $user, int $index): Message
    {
        $alerts = [
            [
                'subject' => 'Alerte : Tempête solaire imminente',
                'content' => "Explorateur {$user->name},\n\n⚠️ ALERTE SYSTÈME ⚠️\n\nUne tempête solaire majeure est détectée dans votre secteur. Évitez les voyages interstellaires pendant les prochaines 24 heures.\n\nRestez en sécurité,\nL'équipe Stellar",
                'metadata' => [
                    'alert_type' => 'solar_storm',
                    'priority' => 'high',
                    'duration_hours' => 24,
                ],
            ],
            [
                'subject' => 'Alerte : Maintenance système programmée',
                'content' => "Explorateur {$user->name},\n\nUne maintenance système est programmée pour demain de 02:00 à 04:00 UTC. Certains services pourront être temporairement indisponibles.\n\nMerci de votre compréhension,\nL'équipe Stellar",
                'metadata' => [
                    'alert_type' => 'maintenance',
                    'priority' => 'medium',
                    'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
                ],
            ],
            [
                'subject' => 'Alerte : Événement spécial détecté',
                'content' => "Explorateur {$user->name},\n\nUn événement spatial rare a été détecté dans votre région. C'est une opportunité unique d'observation scientifique !\n\nNe manquez pas cette occasion,\nL'équipe Stellar",
                'metadata' => [
                    'alert_type' => 'special_event',
                    'priority' => 'high',
                    'event_type' => 'rare_observation',
                ],
            ],
        ];

        $alert = $alerts[($index - 1) % count($alerts)];

        return $messageService->createAlertMessage(
            $user,
            $alert['subject'],
            $alert['content'],
            $alert['metadata']
        );
    }

    /**
     * Create a system message.
     */
    private function createSystemMessage(MessageService $messageService, User $user, int $index): Message
    {
        $systemMessages = [
            [
                'subject' => 'Mise à jour du système de navigation',
                'content' => "Explorateur {$user->name},\n\nLe système de navigation a été mis à jour avec de nouvelles fonctionnalités. Vous pouvez maintenant planifier des voyages plus efficaces.\n\nBonne exploration,\nL'équipe Stellar",
                'metadata' => [
                    'update_type' => 'navigation',
                    'version' => '2.0',
                ],
            ],
            [
                'subject' => 'Nouvelle fonctionnalité : Archives de découvertes',
                'content' => "Explorateur {$user->name},\n\nUne nouvelle fonctionnalité est disponible : les Archives de découvertes. Consultez toutes vos découvertes passées dans un seul endroit.\n\nBonne exploration,\nL'équipe Stellar",
                'metadata' => [
                    'feature' => 'discovery_archives',
                    'available' => true,
                ],
            ],
            [
                'subject' => 'Rapport mensuel d\'activité',
                'content' => "Explorateur {$user->name},\n\nVoici votre rapport mensuel d'activité :\n- Planètes explorées : {$index}\n- Systèmes découverts : 1\n- Ressources collectées : ".($index * 10)."\n\nContinuez votre excellent travail !\nL'équipe Stellar",
                'metadata' => [
                    'report_type' => 'monthly',
                    'planets_explored' => $index,
                ],
            ],
        ];

        $systemMessage = $systemMessages[($index - 1) % count($systemMessages)];

        return $messageService->createSystemMessage(
            $user,
            $systemMessage['subject'],
            $systemMessage['content'],
            $systemMessage['metadata']
        );
    }
}

