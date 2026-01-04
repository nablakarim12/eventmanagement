<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds virtual/computed columns for backward compatibility
     * with the friend's system that expects start_date, end_date, start_time, end_time.
     * 
     * These columns will automatically return:
     * - For Standard events: Use existing values (none removed)
     * - For Innovation/Conference events: Use f2f_start_date, f2f_end_date, etc.
     * 
     * This way both systems can work without breaking changes.
     */
    public function up(): void
    {
        // PostgreSQL supports GENERATED columns, but Laravel's Schema doesn't support them directly
        // We'll create them using raw SQL
        
        DB::statement("
            ALTER TABLE events 
            ADD COLUMN start_date TIMESTAMP 
            GENERATED ALWAYS AS (
                CASE 
                    WHEN f2f_start_date IS NOT NULL THEN f2f_start_date
                    WHEN online_start_date IS NOT NULL THEN online_start_date
                    ELSE NULL
                END
            ) STORED
        ");
        
        DB::statement("
            ALTER TABLE events 
            ADD COLUMN end_date TIMESTAMP 
            GENERATED ALWAYS AS (
                CASE 
                    WHEN f2f_end_date IS NOT NULL THEN f2f_end_date
                    WHEN online_end_date IS NOT NULL THEN online_end_date
                    ELSE NULL
                END
            ) STORED
        ");
        
        DB::statement("
            ALTER TABLE events 
            ADD COLUMN start_time TIME 
            GENERATED ALWAYS AS (
                CASE 
                    WHEN f2f_start_time IS NOT NULL THEN f2f_start_time
                    WHEN online_start_time IS NOT NULL THEN online_start_time
                    ELSE NULL
                END
            ) STORED
        ");
        
        DB::statement("
            ALTER TABLE events 
            ADD COLUMN end_time TIME 
            GENERATED ALWAYS AS (
                CASE 
                    WHEN f2f_end_time IS NOT NULL THEN f2f_end_time
                    WHEN online_end_time IS NOT NULL THEN online_end_time
                    ELSE NULL
                END
            ) STORED
        ");
        
        // Create index on the virtual start_date column for performance
        DB::statement("CREATE INDEX events_start_date_index ON events (start_date)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'start_time', 'end_time']);
        });
    }
};
