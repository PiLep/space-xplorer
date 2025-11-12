<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds file_url_cached column to store pre-computed URLs for resources.
     * This avoids expensive S3 calls on every access to file_url accessor.
     */
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->string('file_url_cached')->nullable()->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn('file_url_cached');
        });
    }
};
