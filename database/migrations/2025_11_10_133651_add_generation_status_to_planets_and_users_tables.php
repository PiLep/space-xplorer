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
            $table->boolean('image_generating')->default(false)->after('image_url');
            $table->boolean('video_generating')->default(false)->after('video_url');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('avatar_generating')->default(false)->after('avatar_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            $table->dropColumn(['image_generating', 'video_generating']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_generating');
        });
    }
};
