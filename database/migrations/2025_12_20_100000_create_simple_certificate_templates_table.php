<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simple_certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('certificate_type'); // 'participant' or 'reviewer'
            
            // Template design selection
            $table->string('template_design')->default('classic'); // classic, modern, elegant, minimal
            
            // Customization options
            $table->string('primary_color')->default('#2563eb'); // Main accent color
            $table->string('secondary_color')->default('#1e40af'); // Secondary color
            $table->string('text_color')->default('#1f2937'); // Text color
            
            // Optional logo
            $table->string('logo_url')->nullable();
            $table->string('logo_cloudinary_id')->nullable();
            
            // Optional signature
            $table->string('signature_url')->nullable();
            $table->string('signature_cloudinary_id')->nullable();
            $table->string('signature_name')->nullable(); // Name under signature
            $table->string('signature_title')->nullable(); // Title under signature
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simple_certificate_templates');
    }
};
