<?php

namespace Database\Seeders;

use App\Events\UserRegistered;
use App\Models\Planet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌌 Seeding Stellar database...');
        $this->command->newLine();

        // Seed universe time configuration first
        $this->call(UniverseTimeConfigSeeder::class);
        $this->command->newLine();

        // Password par défaut pour tous les utilisateurs de test
        $defaultPassword = 'password';

        // Créer des utilisateurs de test avec leurs planètes
        $users = [
            [
                'name' => 'Alex Explorer',
                'email' => 'alex@stellar-game.test',
            ],
            [
                'name' => 'Sam Navigator',
                'email' => 'sam@stellar-game.test',
            ],
            [
                'name' => 'Morgan Pilot',
                'email' => 'morgan@stellar-game.test',
            ],
            [
                'name' => 'Jordan Commander',
                'email' => 'jordan@stellar-game.test',
            ],
            [
                'name' => 'Riley Explorer',
                'email' => 'riley@stellar-game.test',
            ],
        ];

        $createdUsers = [];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($defaultPassword),
                'email_verified_at' => now(),
            ]);

            // Générer la planète d'origine via l'événement
            event(new UserRegistered($user));

            // Rafraîchir pour obtenir la planète assignée
            $user->refresh();

            $createdUsers[] = [
                'user' => $user,
                'planet' => $user->homePlanet,
            ];
        }

        // Créer quelques planètes supplémentaires (non assignées)
        $extraPlanets = Planet::factory()->count(5)->create();

        // Afficher les informations de connexion
        $this->command->info('✅ Users created successfully!');
        $this->command->newLine();
        $this->command->info('📋 Login Credentials:');
        $this->command->newLine();

        foreach ($createdUsers as $data) {
            $user = $data['user'];
            $planet = $data['planet'];

            $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->command->line("👤 <fg=cyan>{$user->name}</>");
            $this->command->line("   📧 Email: <fg=yellow>{$user->email}</>");
            $this->command->line("   🔑 Password: <fg=yellow>{$defaultPassword}</>");
            $this->command->line("   🆔 User ID: <fg=gray>{$user->id}</>");

            if ($planet) {
                $this->command->line("   🪐 Home Planet: <fg=green>{$planet->name}</>");
                $this->command->line("      Type: {$planet->type} | Size: {$planet->size} | Temp: {$planet->temperature}");
                $this->command->line("      🆔 Planet ID: <fg=gray>{$planet->id}</>");
            } else {
                $this->command->line('   ⚠️  <fg=red>No home planet assigned</>');
            }
            $this->command->newLine();
        }

        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('📊 Summary:');
        $this->command->line('   • Users created: <fg=cyan>'.count($createdUsers).'</>');
        $this->command->line('   • Planets created: <fg=cyan>'.(count($createdUsers) + $extraPlanets->count()).'</>');
        $this->command->line("   • Default password for all users: <fg=yellow>{$defaultPassword}</>");
        $this->command->newLine();
        $this->command->info('✨ Database seeded successfully!');
    }
}
