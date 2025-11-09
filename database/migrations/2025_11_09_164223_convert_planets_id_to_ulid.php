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

        // Use raw SQL to modify the column (MySQL requires removing AUTO_INCREMENT first)
        DB::statement('ALTER TABLE planets MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE planets DROP PRIMARY KEY');
        DB::statement('ALTER TABLE planets DROP COLUMN id');

        // Add new ULID column as primary key
        Schema::table('planets', function (Blueprint $table) {
            $table->ulid('id')->primary()->first();
        });

        // Note: home_planet_id will be converted to ULID in a separate migration
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
