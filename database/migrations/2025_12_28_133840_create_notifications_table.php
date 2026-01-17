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
        Schema::create('notifications', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('user_id');
            $table->string('type'); // message_important, ship_assigned, resource_added, etc.
            $table->string('title'); // Titre court de la notification
            $table->text('message'); // Message de la notification (max 200 caractères)
            $table->json('data')->nullable(); // Données additionnelles (ID vaisseau, quantité ressources, lien vers message, etc.)
            $table->boolean('is_read')->default(false); // Statut de lecture
            $table->timestamp('read_at')->nullable(); // Date de lecture
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes for performance
            $table->index('user_id'); // For queries by user
            $table->index('is_read'); // For unread/read filters
            $table->index('type'); // For type filters
            $table->index('created_at'); // For chronological sorting
            $table->index(['user_id', 'is_read']); // Composite index for combined queries
            $table->index(['user_id', 'created_at']); // Composite index for user notifications sorted by date (as per architect review)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
