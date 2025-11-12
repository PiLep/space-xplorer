<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds performance indexes to optimize database queries:
     * - Index on planets.name for uniqueness checks
     * - Index on created_at columns for sorting
     * - Composite indexes for common query patterns
     * - JSON index on resources.tags for tag searches (MySQL 8.0+)
     */
    public function up(): void
    {
        // Index on planets.name for uniqueness checks during planet generation
        Schema::table('planets', function (Blueprint $table) {
            $table->index('name', 'planets_name_index');
        });

        // Index on created_at for sorting (latest() queries)
        Schema::table('planets', function (Blueprint $table) {
            $table->index('created_at', 'planets_created_at_index');
        });

        // Composite index for resources: type + status + created_at (common admin queries)
        Schema::table('resources', function (Blueprint $table) {
            $table->index(['type', 'status', 'created_at'], 'resources_type_status_created_index');
        });

        // Composite index for users: is_super_admin + created_at (admin user lists)
        Schema::table('users', function (Blueprint $table) {
            $table->index(['is_super_admin', 'created_at'], 'users_admin_created_index');
        });

        // Index on resources.created_at (if not already covered by composite)
        // Note: MySQL can use the composite index for ORDER BY created_at, but
        // a dedicated index can be more efficient for simple sorting queries
        Schema::table('resources', function (Blueprint $table) {
            $table->index('created_at', 'resources_created_at_index');
        });

        // JSON index on resources.tags for tag searches (MySQL 8.0+)
        // This uses a functional index on the JSON array for efficient tag matching
        // Note: This requires MySQL 8.0.17+ for functional indexes
        try {
            // Try MySQL 8.0.17+ functional index syntax
            DB::statement('
                CREATE INDEX resources_tags_index ON resources (
                    (CAST(JSON_EXTRACT(tags, "$[*]") AS CHAR(255) ARRAY))
                )
            ');
        } catch (\Exception $e) {
            // If functional index is not supported, fall back to a generated column approach
            // For MySQL < 8.0.17, create a virtual column that extracts tags as a searchable string
            DB::statement('
                ALTER TABLE resources 
                ADD COLUMN tags_search VARCHAR(500) GENERATED ALWAYS AS (
                    REPLACE(REPLACE(REPLACE(JSON_EXTRACT(tags, "$"), "[", ""), "]", ""), "\\"", "")
                ) VIRTUAL AFTER tags
            ');

            Schema::table('resources', function (Blueprint $table) {
                $table->index('tags_search', 'resources_tags_search_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop JSON index (try functional index first, then generated column)
        try {
            DB::statement('DROP INDEX resources_tags_index ON resources');
        } catch (\Exception $e) {
            // Try dropping the generated column approach
            try {
                Schema::table('resources', function (Blueprint $table) {
                    $table->dropIndex('resources_tags_search_index');
                });
                DB::statement('ALTER TABLE resources DROP COLUMN tags_search');
            } catch (\Exception $e2) {
                // Column might not exist, continue
            }
        }

        // Drop composite indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_admin_created_index');
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->dropIndex('resources_type_status_created_index');
            $table->dropIndex('resources_created_at_index');
        });

        // Drop simple indexes
        Schema::table('planets', function (Blueprint $table) {
            $table->dropIndex('planets_name_index');
            $table->dropIndex('planets_created_at_index');
        });
    }
};
