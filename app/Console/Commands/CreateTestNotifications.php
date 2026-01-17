<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CreateTestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:create-test 
                            {--user= : User ID or email to create notifications for}
                            {--count=10 : Number of notifications to create}
                            {--read : Mark some notifications as read}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test notifications for a user';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $userIdentifier = $this->option('user');
        $count = (int) $this->option('count');
        $markSomeAsRead = $this->option('read');

        // Find user
        $user = null;
        if ($userIdentifier) {
            $user = User::where('id', $userIdentifier)
                ->orWhere('email', $userIdentifier)
                ->first();

            if (! $user) {
                $this->error("User not found: {$userIdentifier}");

                return Command::FAILURE;
            }
        } else {
            $user = User::first();

            if (! $user) {
                $this->error('No users found in database. Please create a user first.');

                return Command::FAILURE;
            }
        }

        $this->info("Creating {$count} test notifications for user: {$user->name} ({$user->email})");

        $notifications = [
            [
                'type' => 'message_important',
                'title' => 'Nouveau message important',
                'message' => 'Vous avez reçu un message important de la compagnie Stellar concernant votre mission actuelle.',
                'data' => [
                    'message_id' => 'test-msg-001',
                    'inbox_url' => '/inbox?message=test-msg-001',
                ],
            ],
            [
                'type' => 'message_important',
                'title' => 'Alerte système',
                'message' => 'Une alerte système nécessite votre attention immédiate. Veuillez consulter votre inbox.',
                'data' => [
                    'message_id' => 'test-msg-002',
                    'inbox_url' => '/inbox?message=test-msg-002',
                ],
            ],
            [
                'type' => 'ship_assigned',
                'title' => 'Vaisseau attribué',
                'message' => 'Un nouveau vaisseau vous a été attribué : Stellar Explorer #1234. Consultez vos vaisseaux pour plus de détails.',
                'data' => [
                    'ship_id' => 'test-ship-001',
                    'ship_name' => 'Stellar Explorer #1234',
                    'ship_url' => '/ships/test-ship-001',
                ],
            ],
            [
                'type' => 'resource_added',
                'title' => 'Nouvelles ressources',
                'message' => '10 unités de minerai de fer ont été ajoutées à votre inventaire après votre dernière exploration.',
                'data' => [
                    'resource_type' => 'iron_ore',
                    'quantity' => 10,
                    'inventory_url' => '/inventory',
                ],
            ],
            [
                'type' => 'resource_added',
                'title' => 'Ressources collectées',
                'message' => '5 unités de cristaux d\'énergie ont été collectées et ajoutées à votre inventaire.',
                'data' => [
                    'resource_type' => 'energy_crystals',
                    'quantity' => 5,
                    'inventory_url' => '/inventory',
                ],
            ],
        ];

        $created = 0;
        $readCount = 0;

        for ($i = 0; $i < $count; $i++) {
            $notificationData = $notifications[$i % count($notifications)];

            // Add variation to messages
            if ($i > 0) {
                $notificationData['title'] .= " #{$i}";
                $notificationData['message'] = str_replace(
                    ['1234', '10', '5'],
                    [rand(1000, 9999), rand(1, 50), rand(1, 20)],
                    $notificationData['message']
                );
            }

            $notification = $notificationService->create(
                user: $user,
                type: $notificationData['type'],
                title: $notificationData['title'],
                message: $notificationData['message'],
                data: $notificationData['data']
            );

            // Mark some as read if requested
            if ($markSomeAsRead && $i % 3 === 0) {
                $notification->markAsRead();
                $readCount++;
            }

            $created++;
        }

        $this->info("✓ Created {$created} notifications");
        if ($markSomeAsRead) {
            $this->info("✓ Marked {$readCount} notifications as read");
        }

        $unreadCount = $notificationService->getUnreadCount($user);
        $this->info("Total unread notifications: {$unreadCount}");

        return Command::SUCCESS;
    }
}
