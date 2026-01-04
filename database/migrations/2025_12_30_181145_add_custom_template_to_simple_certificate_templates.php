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
            $table->boolean('use_custom_template')->default(false)->after('certificate_type');
            $table->string('custom_template_url')->nullable()->after('use_custom_template');
            $table->string('custom_template_cloudinary_id')->nullable()->after('custom_template_url');
            $table->json('text_positions')->nullable()->after('custom_template_cloudinary_id'); // For positioning text on custom templates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simple_certificate_templates', function (Blueprint $table) {
            $table->dropColumn(['use_custom_template', 'custom_template_url', 'custom_template_cloudinary_id', 'text_positions']);
        });
    }
};
