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
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraints that reference users.id
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->dropForeign(['tokenable_id']);
            });

            // Drop the primary key
            $table->dropPrimary(['id']);

            // Drop the old id column
            $table->dropColumn('id');
        });

        // Add new ULID column as primary key
        Schema::table('users', function (Blueprint $table) {
            $table->ulid('id')->primary()->first();
        });

        // Update sessions table to use ULID for user_id
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->ulid('user_id')->nullable()->index()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Update personal_access_tokens table to use string for tokenable_id (to support ULIDs)
        // Note: We can't use foreign key here because morphs() can reference multiple models
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn('tokenable_id');
            $table->string('tokenable_id')->index()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraints
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->dropForeign(['tokenable_id']);
            });

            // Drop the ULID primary key
            $table->dropPrimary(['id']);

            // Drop the ULID column
            $table->dropColumn('id');
        });

        // Add back the big integer id column
        Schema::table('users', function (Blueprint $table) {
            $table->id()->first();
        });

        // Revert sessions table
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->foreignId('user_id')->nullable()->index()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Revert personal_access_tokens table
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn('tokenable_id');
            $table->unsignedBigInteger('tokenable_id')->index()->after('id');
        });
    }
};
