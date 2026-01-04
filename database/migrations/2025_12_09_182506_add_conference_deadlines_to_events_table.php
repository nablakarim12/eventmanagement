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
            // Additional conference deadlines
            $table->datetime('reviewer_registration_deadline')->nullable()->after('registration_deadline');
            $table->datetime('acceptance_notification_date')->nullable()->after('review_deadline');
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
                'acceptance_notification_date'
            ]);
        });
    }
};
