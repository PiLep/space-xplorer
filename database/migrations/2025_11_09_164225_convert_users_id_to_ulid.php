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
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop foreign key constraints that reference users.id (if they exist)
        // Note: sessions table doesn't have an explicit foreign key in the original migration
        try {
            DB::statement('ALTER TABLE sessions DROP FOREIGN KEY IF EXISTS sessions_user_id_foreign');
        } catch (\Exception $e) {
            // Foreign key might not exist, continue
        }

        // Use raw SQL to modify the column (MySQL requires removing AUTO_INCREMENT first)
        DB::statement('ALTER TABLE users MODIFY id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE users DROP PRIMARY KEY');
        DB::statement('ALTER TABLE users DROP COLUMN id');

        // Add new ULID column as primary key
        Schema::table('users', function (Blueprint $table) {
            $table->ulid('id')->primary()->first();
        });

        // Update sessions table to use ULID for user_id
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->ulid('user_id')->nullable()->index()->after('id');
            // Note: No foreign key constraint as sessions table didn't have one originally
        });

        // Update personal_access_tokens table to use string for tokenable_id (to support ULIDs)
        // Note: We can't use foreign key here because morphs() can reference multiple models
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn('tokenable_id');
            $table->string('tokenable_id')->index()->after('id');
        });

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Use raw SQL to modify the column
        DB::statement('ALTER TABLE users DROP PRIMARY KEY');
        DB::statement('ALTER TABLE users DROP COLUMN id');

        // Add back the big integer id column
        Schema::table('users', function (Blueprint $table) {
            $table->id()->first();
        });

        // Revert sessions table
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->foreignId('user_id')->nullable()->index()->after('id');
        });

        // Revert personal_access_tokens table
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn('tokenable_id');
            $table->unsignedBigInteger('tokenable_id')->index()->after('id');
        });

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
