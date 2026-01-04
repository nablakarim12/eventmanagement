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
     * This migration restores all columns that were removed during cleanup
     * to ensure compatibility with the friend's system that queries these columns.
     */
    public function up(): void
    {
        // First, drop the virtual/generated columns we created
        DB::statement("ALTER TABLE events DROP COLUMN IF EXISTS start_date");
        DB::statement("ALTER TABLE events DROP COLUMN IF EXISTS end_date");
        DB::statement("ALTER TABLE events DROP COLUMN IF EXISTS start_time");
        DB::statement("ALTER TABLE events DROP COLUMN IF EXISTS end_time");
        
        Schema::table('events', function (Blueprint $table) {
            // Restore legacy date/time columns as regular columns
            if (!Schema::hasColumn('events', 'start_date')) {
                $table->datetime('start_date')->nullable()->after('status');
            }
            if (!Schema::hasColumn('events', 'end_date')) {
                $table->datetime('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('events', 'start_time')) {
                $table->time('start_time')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('events', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
            
            // Restore conference-specific deadline columns that were removed
            if (!Schema::hasColumn('events', 'reviewer_registration_deadline')) {
                $table->datetime('reviewer_registration_deadline')->nullable();
            }
            if (!Schema::hasColumn('events', 'paper_submission_deadline')) {
                $table->datetime('paper_submission_deadline')->nullable();
            }
            if (!Schema::hasColumn('events', 'review_deadline')) {
                $table->datetime('review_deadline')->nullable();
            }
            if (!Schema::hasColumn('events', 'acceptance_notification_date')) {
                $table->datetime('acceptance_notification_date')->nullable();
            }
            
            // Restore product/abstract deadline columns
            if (!Schema::hasColumn('events', 'f2f_product_deadline')) {
                $table->datetime('f2f_product_deadline')->nullable();
            }
            if (!Schema::hasColumn('events', 'f2f_abstract_deadline')) {
                $table->datetime('f2f_abstract_deadline')->nullable();
            }
            if (!Schema::hasColumn('events', 'online_product_deadline')) {
                $table->datetime('online_product_deadline')->nullable();
            }
            if (!Schema::hasColumn('events', 'online_abstract_deadline')) {
                $table->datetime('online_abstract_deadline')->nullable();
            }
        });
        
        // Populate the restored columns with data from f2f_* columns
        DB::statement("
            UPDATE events 
            SET 
                start_date = COALESCE(f2f_start_date, online_start_date),
                end_date = COALESCE(f2f_end_date, online_end_date),
                start_time = COALESCE(f2f_start_time, online_start_time),
                end_time = COALESCE(f2f_end_time, online_end_time),
                reviewer_registration_deadline = COALESCE(f2f_reviewer_registration_deadline, online_reviewer_registration_deadline),
                paper_submission_deadline = COALESCE(f2f_paper_submission_deadline, online_paper_submission_deadline),
                review_deadline = COALESCE(f2f_review_deadline, online_review_deadline),
                acceptance_notification_date = COALESCE(f2f_acceptance_notification_date, online_acceptance_notification_date)
            WHERE start_date IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'start_date',
                'end_date', 
                'start_time',
                'end_time',
                'reviewer_registration_deadline',
                'paper_submission_deadline',
                'review_deadline',
                'acceptance_notification_date',
                'f2f_product_deadline',
                'f2f_abstract_deadline',
                'online_product_deadline',
                'online_abstract_deadline'
            ]);
        });
    }
};
