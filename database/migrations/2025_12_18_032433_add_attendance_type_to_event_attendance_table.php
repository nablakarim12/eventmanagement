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
        Schema::table('event_attendance', function (Blueprint $table) {
            $table->string('attendance_type')->default('general')->after('registration_id'); // general, presentation, reviewer, organizer
            $table->foreignId('checked_in_by')->nullable()->after('check_in_method')->constrained('event_organizers')->onDelete('set null'); // Who checked them in
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_attendance', function (Blueprint $table) {
            $table->dropForeign(['checked_in_by']);
            $table->dropColumn(['attendance_type', 'checked_in_by']);
        });
    }
};
