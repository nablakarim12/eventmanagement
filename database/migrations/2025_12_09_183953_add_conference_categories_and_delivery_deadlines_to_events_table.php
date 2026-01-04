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
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'conference_categories')) {
                $table->json('conference_categories')->nullable();
            }
            
            // F2F conference deadlines
            if (!Schema::hasColumn('events', 'f2f_reviewer_registration_deadline')) {
                $table->dateTime('f2f_reviewer_registration_deadline')->nullable();
            }
            if (!Schema::hasColumn('events', 'f2f_paper_submission_deadline')) {
                $table->dateTime('f2f_paper_submission_deadline')->nullable();
            }
            if (!Schema::hasColumn('events', 'f2f_review_deadline')) {
                $table->dateTime('f2f_review_deadline')->nullable();
            }
            if (!Schema::hasColumn('events', 'f2f_acceptance_notification_date')) {
                $table->dateTime('f2f_acceptance_notification_date')->nullable();
            }
            
            // Online conference deadlines
            if (!Schema::hasColumn('events', 'online_reviewer_registration_deadline')) {
                $table->dateTime('online_reviewer_registration_deadline')->nullable();
            }
            if (!Schema::hasColumn('events', 'online_paper_submission_deadline')) {
                $table->dateTime('online_paper_submission_deadline')->nullable();
            }
            if (!Schema::hasColumn('events', 'online_review_deadline')) {
                $table->dateTime('online_review_deadline')->nullable();
            }
            if (!Schema::hasColumn('events', 'online_acceptance_notification_date')) {
                $table->dateTime('online_acceptance_notification_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'conference_categories',
                'f2f_reviewer_registration_deadline',
                'f2f_paper_submission_deadline',
                'f2f_review_deadline',
                'f2f_acceptance_notification_date',
                'online_reviewer_registration_deadline',
                'online_paper_submission_deadline',
                'online_review_deadline',
                'online_acceptance_notification_date'
            ]);
        });
    }
};
