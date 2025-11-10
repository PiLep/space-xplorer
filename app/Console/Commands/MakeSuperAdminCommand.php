<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:make {email : The email of the user to make super admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a user a super admin by setting the is_super_admin flag';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found.");

            return Command::FAILURE;
        }

        if ($user->is_super_admin) {
            $this->warn("User '{$email}' is already a super admin.");

            return Command::SUCCESS;
        }

        $user->update(['is_super_admin' => true]);

        $this->info("User '{$email}' has been made a super admin.");
        $this->line("Don't forget to add this email to ADMIN_EMAIL_WHITELIST in your .env file!");

        return Command::SUCCESS;
    }
}
