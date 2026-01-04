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
        Schema::table('generated_certificates', function (Blueprint $table) {
            $table->string('participant_role')->nullable()->after('participant_name');
            $table->string('event_date')->nullable()->after('event_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generated_certificates', function (Blueprint $table) {
            $table->dropColumn(['participant_role', 'event_date']);
        });
    }
};
