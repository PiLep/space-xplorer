<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include 'generating' status
        DB::statement("ALTER TABLE resources MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'generating') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'generating' from the enum
        // First, update any resources with 'generating' status to 'pending'
        DB::statement("UPDATE resources SET status = 'pending' WHERE status = 'generating'");

        // Then modify the enum back to original values
        DB::statement("ALTER TABLE resources MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
