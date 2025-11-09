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
        Schema::create('planets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // tellurique, gazeuse, glacée, désertique, océanique
            $table->string('size'); // petite, moyenne, grande
            $table->string('temperature'); // froide, tempérée, chaude
            $table->string('atmosphere'); // respirable, toxique, inexistante
            $table->string('terrain'); // rocheux, océanique, désertique, forestier, etc.
            $table->string('resources'); // abondantes, modérées, rares
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planets');
    }
};
