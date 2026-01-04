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
            // Note: registration_deadline already exists in events table
            
            // New deadline fields for innovation events
            $table->datetime('jury_registration_deadline')->nullable()->after('registration_deadline');
            $table->datetime('submission_deadline')->nullable()->after('jury_registration_deadline');
            $table->datetime('acceptance_notification_date')->nullable()->after('submission_deadline');
            
            // Extended deadlines (optional)
            $table->datetime('extended_registration_deadline')->nullable()->after('acceptance_notification_date');
            $table->datetime('extended_jury_deadline')->nullable()->after('extended_registration_deadline');
            $table->datetime('extended_submission_deadline')->nullable()->after('extended_jury_deadline');
            $table->datetime('extended_notification_date')->nullable()->after('extended_submission_deadline');
            
            // Face-to-face specific deadlines (for hybrid mode)
            $table->datetime('f2f_registration_deadline')->nullable()->after('extended_notification_date');
            $table->datetime('f2f_submission_deadline')->nullable()->after('f2f_registration_deadline');
            $table->datetime('f2f_acceptance_notification_date')->nullable()->after('f2f_submission_deadline');
            $table->datetime('f2f_extended_registration_deadline')->nullable()->after('f2f_acceptance_notification_date');
            $table->datetime('f2f_extended_jury_deadline')->nullable()->after('f2f_extended_registration_deadline');
            $table->datetime('f2f_extended_submission_deadline')->nullable()->after('f2f_extended_jury_deadline');
            $table->datetime('f2f_extended_notification_date')->nullable()->after('f2f_extended_submission_deadline');
            
            // Online specific deadlines (for hybrid mode)
            $table->datetime('online_registration_deadline')->nullable()->after('f2f_extended_notification_date');
            $table->datetime('online_submission_deadline')->nullable()->after('online_registration_deadline');
            $table->datetime('online_acceptance_notification_date')->nullable()->after('online_submission_deadline');
            $table->datetime('online_extended_registration_deadline')->nullable()->after('online_acceptance_notification_date');
            $table->datetime('online_extended_jury_deadline')->nullable()->after('online_extended_registration_deadline');
            $table->datetime('online_extended_submission_deadline')->nullable()->after('online_extended_jury_deadline');
            $table->datetime('online_extended_notification_date')->nullable()->after('online_extended_submission_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'jury_registration_deadline',
                'submission_deadline',
                'acceptance_notification_date',
                'extended_registration_deadline',
                'extended_jury_deadline',
                'extended_submission_deadline',
                'extended_notification_date',
                'f2f_registration_deadline',
                'f2f_submission_deadline',
                'f2f_acceptance_notification_date',
                'f2f_extended_registration_deadline',
                'f2f_extended_jury_deadline',
                'f2f_extended_submission_deadline',
                'f2f_extended_notification_date',
                'online_registration_deadline',
                'online_submission_deadline',
                'online_acceptance_notification_date',
                'online_extended_registration_deadline',
                'online_extended_jury_deadline',
                'online_extended_submission_deadline',
                'online_extended_notification_date',
            ]);
        });
    }
};
