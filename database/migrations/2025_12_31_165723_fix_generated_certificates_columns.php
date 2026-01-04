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
            // Drop old foreign key constraint if exists
            $table->dropForeign(['template_id']);
            
            // Drop and recreate template_id to reference simple_certificate_templates
            $table->dropColumn('template_id');
        });
        
        Schema::table('generated_certificates', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->after('user_id')->constrained('simple_certificate_templates')->onDelete('set null');
            $table->timestamp('generated_at')->nullable()->after('event_date');
            $table->timestamp('downloaded_at')->nullable()->after('generated_at');
            $table->timestamp('emailed_at')->nullable()->after('downloaded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('generated_certificates', function (Blueprint $table) {
            $table->dropColumn(['generated_at', 'downloaded_at', 'emailed_at']);
        });
    }
};
