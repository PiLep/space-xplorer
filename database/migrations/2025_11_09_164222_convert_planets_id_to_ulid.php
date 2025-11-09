<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the foreign key constraint from users.home_planet_id first
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['home_planet_id']);
        });

        Schema::table('planets', function (Blueprint $table) {
            // Drop the primary key
            $table->dropPrimary(['id']);

            // Drop the old id column
            $table->dropColumn('id');
        });

        // Add new ULID column as primary key
        Schema::table('planets', function (Blueprint $table) {
            $table->ulid('id')->primary()->first();
        });

        // Note: home_planet_id will be converted to ULID in a separate migration
        // For now, we'll temporarily allow the foreign key to be nullable
        // The convert_home_planet_id_to_ulid migration will handle the conversion
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint from users.home_planet_id first
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['home_planet_id']);
        });

        Schema::table('planets', function (Blueprint $table) {
            // Drop the ULID primary key
            $table->dropPrimary(['id']);

            // Drop the ULID column
            $table->dropColumn('id');
        });

        // Add back the big integer id column
        Schema::table('planets', function (Blueprint $table) {
            $table->id()->first();
        });

        // Recreate the foreign key constraint (assuming home_planet_id is back to bigInteger)
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('home_planet_id')->references('id')->on('planets')->onDelete('set null');
        });
    }
};
