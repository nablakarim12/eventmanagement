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
        Schema::table('event_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_registrations', 'selected_category')) {
                $table->string('selected_category')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('event_registrations', 'selected_theme')) {
                $table->string('selected_theme')->nullable()->after('selected_category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn(['selected_category', 'selected_theme']);
        });
    }
};
