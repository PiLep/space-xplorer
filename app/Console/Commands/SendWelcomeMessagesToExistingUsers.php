<?php

namespace App\Console\Commands;

use App\Mail\NewMessageNotification;
use App\Models\Message;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWelcomeMessagesToExistingUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:send-welcome-to-existing-users 
                            {--force : Force sending even if user already has a welcome message}
                            {--email : Send email notification when message is created}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send welcome messages to existing users who do not have one yet';

    /**
     * Execute the console command.
     */
    public function handle(MessageService $messageService): int
    {
        $force = $this->option('force');

        $this->info('Checking existing users for welcome messages...');
        $this->newLine();

        $users = User::all();
        $sentCount = 0;
        $skippedCount = 0;

        foreach ($users as $user) {
            // Check if user already has a welcome message
            $hasWelcomeMessage = Message::where('recipient_id', $user->id)
                ->where('type', 'welcome')
                ->exists();

            if ($hasWelcomeMessage && ! $force) {
                $this->line("  ⏭  Skipping {$user->name} ({$user->email}) - already has welcome message");
                $skippedCount++;

                continue;
            }

            try {
                $message = $messageService->createWelcomeMessage($user);
                $this->line("  ✓  Sent welcome message to {$user->name} ({$user->email})");

                // Send email notification if requested
                if ($this->option('email')) {
                    try {
                        Mail::to($user->email)->send(new NewMessageNotification($message, $user));
                        $this->line('     📧 Email notification sent');
                    } catch (\Exception $e) {
                        $this->warn("     ⚠  Failed to send email notification: {$e->getMessage()}");
                    }
                }

                $sentCount++;
            } catch (\Exception $e) {
                $this->error("  ✗  Failed to send message to {$user->name}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info('Summary:');
        $this->line("  - Messages sent: {$sentCount}");
        $this->line("  - Users skipped: {$skippedCount}");
        $this->line("  - Total users: {$users->count()}");

        return Command::SUCCESS;
    }
}

