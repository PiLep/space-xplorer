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
        Schema::create('event_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('event_type'); // Nom de la classe d'événement (ex: App\Events\UserLoggedIn)
            $table->ulid('user_id')->nullable(); // ID de l'utilisateur concerné (nullable car certains événements n'ont pas d'utilisateur)
            $table->json('event_data')->nullable(); // Données sérialisées de l'événement
            $table->string('ip_address')->nullable(); // Adresse IP de la requête
            $table->text('user_agent')->nullable(); // User agent de la requête
            $table->string('session_id')->nullable(); // ID de session pour traçabilité
            $table->timestamps();

            // Foreign key vers users (nullable)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Créer les index séparément pour éviter les problèmes potentiels
        Schema::table('event_logs', function (Blueprint $table) {
            // Indexes pour performance
            $table->index('event_type'); // Pour filtrer par type d'événement
            $table->index('user_id'); // Pour filtrer par utilisateur
            $table->index('created_at'); // Pour trier chronologiquement
            $table->index(['user_id', 'created_at']); // Composite pour requêtes utilisateur + date
            $table->index(['event_type', 'created_at']); // Composite pour requêtes type + date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_logs');
    }
};
