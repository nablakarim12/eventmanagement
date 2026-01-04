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
     * This migration cleans up and organizes the events table:
     * 1. Removes unused legacy columns (empty data)
     * 2. Adds missing columns found during analysis
     * 3. Organizes columns logically by event type
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // ============================================
            // STEP 1: Add missing columns that were found
            // ============================================
            
            // Currency field (shared by all event types)
            if (!Schema::hasColumn('events', 'currency')) {
                $table->string('currency', 3)->default('MYR')->after('registration_fee');
            }
            
            // Innovation-specific jury deadlines and extensions
            if (!Schema::hasColumn('events', 'f2f_jury_registration_deadline')) {
                $table->datetime('f2f_jury_registration_deadline')->nullable()->after('f2f_acceptance_date');
            }
            if (!Schema::hasColumn('events', 'f2f_extended_paper_deadlines')) {
                $table->json('f2f_extended_paper_deadlines')->nullable()->after('f2f_extended_acceptance_dates');
            }
            if (!Schema::hasColumn('events', 'f2f_jury_deadline')) {
                $table->datetime('f2f_jury_deadline')->nullable()->after('f2f_acceptance_date');
            }
            
            if (!Schema::hasColumn('events', 'online_jury_registration_deadline')) {
                $table->datetime('online_jury_registration_deadline')->nullable()->after('online_acceptance_date');
            }
            if (!Schema::hasColumn('events', 'online_extended_paper_deadlines')) {
                $table->json('online_extended_paper_deadlines')->nullable()->after('online_extended_acceptance_dates');
            }
            if (!Schema::hasColumn('events', 'online_jury_deadline')) {
                $table->datetime('online_jury_deadline')->nullable()->after('online_acceptance_date');
            }
        });
        
        // ============================================
        // STEP 2: Drop unused legacy columns
        // ============================================
        
        Schema::table('events', function (Blueprint $table) {
            // Drop empty legacy conference columns (replaced by f2f_* and online_* versions)
            $columnsToCheck = [
                'reviewer_registration_deadline',
                'paper_submission_deadline',
                'review_deadline',
                'acceptance_notification_date'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('events', $column)) {
                    $hasData = DB::table('events')->whereNotNull($column)->exists();
                    if (!$hasData) {
                        $table->dropColumn($column);
                    }
                }
            }
        });
        
        // ============================================
        // STEP 3: Handle legacy start/end date columns
        // Note: These have data, so we migrate to new structure
        // ============================================
        
        // Migrate legacy start_date/end_date to f2f_* for non-Innovation/Conference events
        DB::statement("
            UPDATE events 
            SET 
                f2f_start_date = start_date,
                f2f_end_date = end_date,
                f2f_start_time = start_time,
                f2f_end_time = end_time
            WHERE delivery_mode IS NULL 
            AND f2f_start_date IS NULL
            AND start_date IS NOT NULL
        ");
        
        // Now we can drop the legacy columns after migration
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'start_date')) {
                $table->dropColumn(['start_date', 'end_date', 'start_time', 'end_time']);
            }
            
            // Migrate registration_deadline for standard events
            if (Schema::hasColumn('events', 'registration_deadline')) {
                // Note: Registration deadline is only for standard events, not Innovation/Conference
                // Keep this column for now as it's used by standard events
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Restore legacy columns
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->datetime('reviewer_registration_deadline')->nullable();
            $table->datetime('paper_submission_deadline')->nullable();
            $table->datetime('review_deadline')->nullable();
            $table->datetime('acceptance_notification_date')->nullable();
            
            // Drop added columns
            if (Schema::hasColumn('events', 'currency')) {
                $table->dropColumn('currency');
            }
            if (Schema::hasColumn('events', 'f2f_jury_registration_deadline')) {
                $table->dropColumn('f2f_jury_registration_deadline');
            }
            if (Schema::hasColumn('events', 'f2f_extended_paper_deadlines')) {
                $table->dropColumn('f2f_extended_paper_deadlines');
            }
            if (Schema::hasColumn('events', 'f2f_jury_deadline')) {
                $table->dropColumn('f2f_jury_deadline');
            }
            if (Schema::hasColumn('events', 'online_jury_registration_deadline')) {
                $table->dropColumn('online_jury_registration_deadline');
            }
            if (Schema::hasColumn('events', 'online_extended_paper_deadlines')) {
                $table->dropColumn('online_extended_paper_deadlines');
            }
            if (Schema::hasColumn('events', 'online_jury_deadline')) {
                $table->dropColumn('online_jury_deadline');
            }
        });
    }
};
