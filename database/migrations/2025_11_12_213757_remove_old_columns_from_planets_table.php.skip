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
            // Remove old columns that are now in planet_properties table
            $table->dropColumn([
                'type',
                'size',
                'temperature',
                'atmosphere',
                'terrain',
                'resources',
                'description',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planets', function (Blueprint $table) {
            // Restore old columns (for rollback purposes)
            $table->string('type')->after('name');
            $table->string('size')->after('type');
            $table->string('temperature')->after('size');
            $table->string('atmosphere')->after('temperature');
            $table->string('terrain')->after('atmosphere');
            $table->string('resources')->after('terrain');
            $table->text('description')->nullable()->after('resources');
        });
    }
};
