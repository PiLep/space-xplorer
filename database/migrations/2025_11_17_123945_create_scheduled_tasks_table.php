<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scheduled_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'daily_planet_resources', 'daily_avatar_resources'
            $table->string('command'); // Command class name or signature
            $table->boolean('is_enabled')->default(true);
            $table->string('schedule_time')->nullable(); // e.g., '02:00'
            $table->text('description')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->json('metadata')->nullable(); // For storing additional configuration
            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_tasks');
    }
};
