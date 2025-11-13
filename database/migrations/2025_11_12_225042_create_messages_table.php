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
        Schema::create('messages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('sender_id')->nullable();
            $table->ulid('recipient_id');
            $table->string('type'); // system, discovery, mission, alert, welcome
            $table->string('subject');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_important')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes for performance (as per architectural recommendations)
            $table->index('recipient_id'); // For queries by recipient
            $table->index('is_read'); // For unread/read filters
            $table->index('type'); // For type filters
            $table->index(['recipient_id', 'is_read']); // Composite index for combined queries
            $table->index('created_at'); // For chronological sorting
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
