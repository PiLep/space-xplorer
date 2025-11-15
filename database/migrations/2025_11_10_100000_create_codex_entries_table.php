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
        Schema::create('codex_entries', function (Blueprint $table) {
            // Primary key using ULID (as per architectural recommendation)
            $table->ulid('id')->primary();

            // Foreign key to planets table (ULID)
            $table->ulid('planet_id')->unique();
            $table->foreign('planet_id')->references('id')->on('planets')->onDelete('cascade');

            // Name fields
            $table->string('name')->nullable(); // User-provided name
            $table->string('fallback_name'); // Auto-generated technical name

            // Description
            $table->text('description')->nullable(); // AI-generated description

            // Discovery information
            $table->ulid('discovered_by_user_id')->nullable();
            $table->foreign('discovered_by_user_id')->references('id')->on('users')->onDelete('set null');

            // Status flags
            $table->boolean('is_named')->default(false);
            $table->boolean('is_public')->default(true);

            $table->timestamps();

            // Indexes for performance (as per architectural recommendations)
            $table->index('discovered_by_user_id'); // For filtering by discoverer
            $table->index('is_public'); // For public/private filtering
            $table->index('created_at'); // For chronological sorting

            // Indexes for search performance on name and fallback_name
            // Using simple indexes for compatibility (full-text indexes require MySQL 5.7.6+)
            $table->index('name'); // For searching by name
            $table->index('fallback_name'); // For searching by fallback name
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codex_entries');
    }
};

