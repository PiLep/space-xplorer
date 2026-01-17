<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📬 Seeding notifications...');
        $this->command->newLine();

        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️  No users found. Please run DatabaseSeeder first.');

            return;
        }

        $notificationService = app(NotificationService::class);

        $notificationTemplates = [
            [
                'type' => 'message_important',
                'title' => 'Nouveau message important',
                'message' => 'Vous avez reçu un message important de la compagnie Stellar concernant votre mission actuelle.',
                'data' => [
                    'message_id' => 'msg-001',
                    'inbox_url' => '/inbox?message=msg-001',
                ],
            ],
            [
                'type' => 'message_important',
                'title' => 'Alerte système',
                'message' => 'Une alerte système nécessite votre attention immédiate. Veuillez consulter votre inbox.',
                'data' => [
                    'message_id' => 'msg-002',
                    'inbox_url' => '/inbox?message=msg-002',
                ],
            ],
            [
                'type' => 'ship_assigned',
                'title' => 'Vaisseau attribué',
                'message' => 'Un nouveau vaisseau vous a été attribué : Stellar Explorer #1234. Consultez vos vaisseaux pour plus de détails.',
                'data' => [
                    'ship_id' => 'ship-001',
                    'ship_name' => 'Stellar Explorer #1234',
                    'ship_url' => '/ships/ship-001',
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

        $totalCreated = 0;
        $totalRead = 0;

        foreach ($users as $user) {
            // Create 8-12 notifications per user (mix of read and unread)
            $notificationCount = rand(8, 12);
            $readCount = 0;

            for ($i = 0; $i < $notificationCount; $i++) {
                $template = $notificationTemplates[$i % count($notificationTemplates)];

                // Add variation to messages
                $title = $template['title'];
                $message = $template['message'];

                if ($i > 0) {
                    // Add variation to IDs and quantities
                    $message = str_replace(
                        ['1234', '10', '5', 'msg-001', 'msg-002', 'ship-001'],
                        [
                            rand(1000, 9999),
                            rand(1, 50),
                            rand(1, 20),
                            'msg-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                            'msg-'.str_pad((string) ($i + 2), 3, '0', STR_PAD_LEFT),
                            'ship-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                        ],
                        $message
                    );
                }

                // Create notification
                $notification = $notificationService->create(
                    user: $user,
                    type: $template['type'],
                    title: $title,
                    message: $message,
                    data: $template['data']
                );

                // Mark approximately 30-40% as read (randomly)
                if (rand(1, 100) <= 35) {
                    $notification->markAsRead();
                    $readCount++;
                    $totalRead++;
                }

                // Set created_at to random time in the past (last 7 days)
                $randomDaysAgo = rand(0, 7);
                $randomHoursAgo = rand(0, 23);
                $randomMinutesAgo = rand(0, 59);
                $createdAt = now()->subDays($randomDaysAgo)
                    ->subHours($randomHoursAgo)
                    ->subMinutes($randomMinutesAgo);

                $notification->update([
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                if ($notification->is_read) {
                    $notification->update([
                        'read_at' => $createdAt->addMinutes(rand(5, 60)),
                    ]);
                }

                $totalCreated++;
            }

            $this->command->line("   • {$user->name}: {$notificationCount} notifications ({$readCount} lues)");
        }

        $this->command->newLine();
        $this->command->info('✅ Notifications seeded successfully!');
        $this->command->line("   • Total notifications created: <fg=cyan>{$totalCreated}</>");
        $this->command->line("   • Read notifications: <fg=cyan>{$totalRead}</>");
        $this->command->line('   • Unread notifications: <fg=cyan>'.($totalCreated - $totalRead).'</>');
        $this->command->newLine();
    }
}
