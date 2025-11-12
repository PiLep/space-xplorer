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
        Schema::table('planets', function (Blueprint $table) {
            // Coordonnées absolues dans la galaxie (calculées depuis le système)
            $table->decimal('x', 15, 2)->nullable()->after('description');
            $table->decimal('y', 15, 2)->nullable();
            $table->decimal('z', 15, 2)->nullable();

            // Relation avec le système stellaire
            $table->foreignUlid('star_system_id')->nullable()->after('z')->constrained('star_systems')->onDelete('cascade');

            // Coordonnées relatives dans le système (position orbitale statique)
            $table->decimal('orbital_distance', 10, 2)->nullable()->after('star_system_id'); // Distance à l'étoile (unités arbitraires)
            $table->decimal('orbital_angle', 8, 4)->nullable()->after('orbital_distance'); // Angle orbital (0-360°)
            $table->decimal('orbital_inclination', 6, 2)->nullable()->after('orbital_angle'); // Inclinaison orbitale (-90 à +90°)

            // Index pour recherches spatiales
            $table->index(['x', 'y', 'z']);
            $table->index('star_system_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            $table->dropForeign(['star_system_id']);
            $table->dropIndex(['x', 'y', 'z']);
            $table->dropIndex(['star_system_id']);
            $table->dropColumn([
                'x', 'y', 'z',
                'star_system_id',
                'orbital_distance',
                'orbital_angle',
                'orbital_inclination',
            ]);
        });
    }
};
