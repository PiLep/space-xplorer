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
        Schema::create('star_systems', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->decimal('x', 15, 2); // Position dans la galaxie
            $table->decimal('y', 15, 2);
            $table->decimal('z', 15, 2);
            $table->string('star_type')->nullable(); // yellow_dwarf, red_giant, red_dwarf, etc.
            $table->integer('planet_count')->default(0); // Nombre de planètes dans le système
            $table->boolean('discovered')->default(false); // Système découvert ou non
            $table->timestamps();

            // Index pour recherches spatiales
            $table->index(['x', 'y', 'z']);
            $table->index('discovered');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('star_systems');
    }
};
