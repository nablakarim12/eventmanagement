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
        Schema::table('simple_certificate_templates', function (Blueprint $table) {
            $table->boolean('use_ai_generation')->default(false)->after('use_custom_template');
            $table->string('template_file_url')->nullable()->after('use_ai_generation'); // Image/PDF/Word template
            $table->string('template_file_type')->nullable()->after('template_file_url'); // image, pdf, word
            $table->string('template_cloudinary_id')->nullable()->after('template_file_type');
            $table->text('generation_prompt')->nullable()->after('template_cloudinary_id'); // AI prompt for generation
            $table->string('participant_cert_type')->default('participation')->after('certificate_type'); // participation, award
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simple_certificate_templates', function (Blueprint $table) {
            $table->dropColumn(['use_ai_generation', 'template_file_url', 'template_file_type', 'template_cloudinary_id', 'generation_prompt', 'participant_cert_type']);
        });
    }
};
