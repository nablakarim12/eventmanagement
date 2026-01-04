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
            // Add missing core fields
            if (!Schema::hasColumn('events', 'delivery_mode')) {
                $table->string('delivery_mode')->nullable()->after('category_id');
            }
            
            if (!Schema::hasColumn('events', 'innovation_categories')) {
                $table->json('innovation_categories')->nullable()->after('delivery_mode');
            }
            
            // F2F date/time fields (basic)
            if (!Schema::hasColumn('events', 'f2f_start_date')) {
                $table->date('f2f_start_date')->nullable()->after('f2f_extended_notification_date');
            }
            
            if (!Schema::hasColumn('events', 'f2f_end_date')) {
                $table->date('f2f_end_date')->nullable()->after('f2f_start_date');
            }
            
            if (!Schema::hasColumn('events', 'f2f_start_time')) {
                $table->time('f2f_start_time')->nullable()->after('f2f_end_date');
            }
            
            if (!Schema::hasColumn('events', 'f2f_end_time')) {
                $table->time('f2f_end_time')->nullable()->after('f2f_start_time');
            }
            
            // F2F jury registration deadline
            if (!Schema::hasColumn('events', 'f2f_jury_registration_deadline')) {
                $table->datetime('f2f_jury_registration_deadline')->nullable()->after('f2f_registration_deadline');
            }
            
            // F2F payment deadline
            if (!Schema::hasColumn('events', 'f2f_payment_deadline_new')) {
                $table->datetime('f2f_payment_deadline_new')->nullable()->after('f2f_extended_notification_date');
            }
            
            // Online date/time fields (basic)
            if (!Schema::hasColumn('events', 'online_start_date')) {
                $table->date('online_start_date')->nullable()->after('online_extended_notification_date');
            }
            
            if (!Schema::hasColumn('events', 'online_end_date')) {
                $table->date('online_end_date')->nullable()->after('online_start_date');
            }
            
            if (!Schema::hasColumn('events', 'online_start_time')) {
                $table->time('online_start_time')->nullable()->after('online_end_date');
            }
            
            if (!Schema::hasColumn('events', 'online_end_time')) {
                $table->time('online_end_time')->nullable()->after('online_start_time');
            }
            
            // Online jury registration deadline
            if (!Schema::hasColumn('events', 'online_jury_registration_deadline_new')) {
                $table->datetime('online_jury_registration_deadline_new')->nullable()->after('online_registration_deadline');
            }
            
            // Online acceptance notification (new field name)
            if (!Schema::hasColumn('events', 'online_acceptance_notification_date_new')) {
                $table->datetime('online_acceptance_notification_date_new')->nullable()->after('online_submission_deadline');
            }
            
            // Online payment deadline
            if (!Schema::hasColumn('events', 'online_payment_deadline_new')) {
                $table->datetime('online_payment_deadline_new')->nullable()->after('online_extended_notification_date');
            }
            
            // Online platform URL
            if (!Schema::hasColumn('events', 'online_platform_url')) {
                $table->string('online_platform_url')->nullable()->after('online_payment_deadline_new');
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
                'delivery_mode',
                'innovation_categories',
                'f2f_start_date',
                'f2f_end_date',
                'f2f_start_time',
                'f2f_end_time',
                'f2f_jury_registration_deadline',
                'f2f_payment_deadline_new',
                'online_start_date',
                'online_end_date',
                'online_start_time',
                'online_end_time',
                'online_jury_registration_deadline_new',
                'online_acceptance_notification_date_new',
                'online_payment_deadline_new',
                'online_platform_url',
            ]);
        });
    }
};
