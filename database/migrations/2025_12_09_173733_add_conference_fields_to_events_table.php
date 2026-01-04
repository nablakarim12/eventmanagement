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
            // Conference-specific deadlines
            $table->datetime('reviewer_registration_deadline')->nullable()->after('registration_deadline');
            $table->datetime('paper_submission_deadline')->nullable()->after('reviewer_registration_deadline');
            $table->datetime('review_deadline')->nullable()->after('paper_submission_deadline');
            $table->datetime('acceptance_notification_date')->nullable()->after('review_deadline');
            
            // Conference-specific paper submission settings
            $table->integer('min_abstract_words')->default(200)->after('acceptance_notification_date');
            $table->integer('min_keywords')->default(3)->after('min_abstract_words');
            $table->integer('max_paper_size_mb')->default(10)->after('min_keywords');
            $table->text('paper_format_guidelines')->nullable()->after('max_paper_size_mb');
            $table->boolean('allow_multiple_submissions')->default(false)->after('paper_format_guidelines');
            
            // Reviewer settings
            $table->integer('min_reviewers_per_paper')->default(2)->after('allow_multiple_submissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'reviewer_registration_deadline',
                'paper_submission_deadline',
                'review_deadline',
                'acceptance_notification_date',
                'min_abstract_words',
                'min_keywords',
                'max_paper_size_mb',
                'paper_format_guidelines',
                'allow_multiple_submissions',
                'min_reviewers_per_paper'
            ]);
        });
    }
};
