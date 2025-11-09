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
        // Drop foreign key if it exists (it was already dropped in convert_planets_id_to_ulid, but check anyway)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        Schema::table('users', function (Blueprint $table) {
            // Drop the old column (foreign key was already dropped in previous migration)
            $table->dropColumn('home_planet_id');
        });

        // Add new ULID column
        Schema::table('users', function (Blueprint $table) {
            $table->ulid('home_planet_id')->nullable()->after('email_verified_at');
            $table->foreign('home_planet_id')->references('id')->on('planets')->onDelete('set null');
        });
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['home_planet_id']);

            // Drop the ULID column
            $table->dropColumn('home_planet_id');
        });

        // Add back the big integer column
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('home_planet_id')->nullable()->after('email_verified_at');
            $table->foreign('home_planet_id')->references('id')->on('planets')->onDelete('set null');
        });
    }
};
