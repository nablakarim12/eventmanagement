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
            $table->decimal('min_attendance_hours', 5, 2)->default(1.0)->after('max_participants');
            $table->boolean('auto_generate_certificates')->default(true)->after('min_attendance_hours');
            $table->boolean('requires_attendance')->default(false)->after('auto_generate_certificates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['min_attendance_hours', 'auto_generate_certificates', 'requires_attendance']);
        });
    }
};
