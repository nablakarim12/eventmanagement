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
        Schema::create('simple_certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('certificate_type'); // 'participant' or 'jury'
            $table->string('template_design')->default('modern'); // Template design style
            $table->string('primary_color')->default('#3B82F6'); // Primary color
            $table->string('secondary_color')->default('#1E40AF'); // Secondary color
            $table->string('text_color')->default('#1F2937'); // Text color
            $table->string('logo_url')->nullable(); // Organization logo
            $table->string('logo_cloudinary_id')->nullable();
            $table->string('signature_url')->nullable(); // Signature image
            $table->string('signature_cloudinary_id')->nullable();
            $table->string('signature_name')->nullable(); // Name below signature
            $table->string('signature_title')->nullable(); // Title below signature
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simple_certificate_templates');
    }
};
