<?php

namespace Database\Seeders;

use App\Models\ScheduledTask;
use Illuminate\Database\Seeder;

class ScheduledTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            [
                'name' => 'daily_planet_resources',
                'command' => 'resources:generate-daily-planets',
                'is_enabled' => true,
                'schedule_time' => '02:00',
                'description' => 'Generate daily batch of planet image resources for admin approval',
            ],
            [
                'name' => 'daily_avatar_resources',
                'command' => 'resources:generate-daily-avatars',
                'is_enabled' => true,
                'schedule_time' => '02:30',
                'description' => 'Generate daily batch of avatar image resources for admin approval',
            ],
        ];

        foreach ($tasks as $taskData) {
            ScheduledTask::updateOrCreate(
                ['name' => $taskData['name']],
                $taskData
            );
        }
    }
}
