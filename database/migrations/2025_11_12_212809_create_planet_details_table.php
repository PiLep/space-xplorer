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
        Schema::create('planet_properties', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('planet_id')->unique()->constrained('planets')->onDelete('cascade');

            // Caractéristiques en anglais uniquement
            $table->string('type'); // terrestrial, gaseous, icy, desert, oceanic
            $table->string('size'); // small, medium, large
            $table->string('temperature'); // cold, temperate, hot
            $table->string('atmosphere'); // breathable, toxic, nonexistent
            $table->string('terrain'); // rocky, oceanic, desert, forested, urban, mixed, icy
            $table->string('resources'); // abundant, moderate, rare
            $table->text('description')->nullable();

            $table->timestamps();

            // Index pour recherches
            $table->index('planet_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planet_properties');
    }
};
