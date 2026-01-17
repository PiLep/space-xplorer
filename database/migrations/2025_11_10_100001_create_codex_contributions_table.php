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
        Schema::create('codex_contributions', function (Blueprint $table) {
            // Primary key using ULID (as per architectural recommendation)
            $table->ulid('id')->primary();

            // Foreign key to codex_entries table (ULID)
            $table->ulid('codex_entry_id');
            $table->foreign('codex_entry_id')->references('id')->on('codex_entries')->onDelete('cascade');

            // Foreign key to users table (ULID)
            $table->ulid('contributor_user_id');
            $table->foreign('contributor_user_id')->references('id')->on('users')->onDelete('cascade');

            // Contribution content
            $table->string('content_type'); // 'description', 'name_suggestion', 'additional_info', etc.
            $table->text('content');

            // Status: pending, approved, rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();

            // Indexes for performance
            $table->index('codex_entry_id'); // For queries by codex entry
            $table->index('contributor_user_id'); // For queries by contributor
            $table->index('status'); // For filtering by status
            $table->index(['codex_entry_id', 'status']); // Composite index for common queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codex_contributions');
    }
};

