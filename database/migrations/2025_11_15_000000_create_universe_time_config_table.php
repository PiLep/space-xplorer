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
        Schema::create('universe_time_config', function (Blueprint $table) {
            $table->id();
            $table->dateTime('reference_date')->comment('Date de référence terrestre (début de l\'univers) - 2436-01-01');
            $table->integer('real_days_per_game_week')->default(7)->comment('Nombre de jours réels = 1 semaine de jeu');
            $table->integer('base_year')->default(2436)->comment('Année terrestre de départ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('universe_time_config');
    }
};

