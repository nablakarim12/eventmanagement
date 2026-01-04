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
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('template_type'); // 'participant' or 'reviewer'
            $table->string('template_url'); // Cloudinary URL
            $table->string('cloudinary_public_id')->nullable();
            
            // Text positioning coordinates (in pixels from top-left)
            $table->integer('name_x')->default(0);
            $table->integer('name_y')->default(0);
            $table->integer('name_font_size')->default(36);
            $table->string('name_color')->default('#000000');
            
            $table->integer('role_x')->default(0);
            $table->integer('role_y')->default(0);
            $table->integer('role_font_size')->default(24);
            $table->string('role_color')->default('#000000');
            
            $table->integer('event_name_x')->default(0);
            $table->integer('event_name_y')->default(0);
            $table->integer('event_name_font_size')->default(28);
            $table->string('event_name_color')->default('#000000');
            
            $table->integer('date_x')->default(0);
            $table->integer('date_y')->default(0);
            $table->integer('date_font_size')->default(20);
            $table->string('date_color')->default('#000000');
            
            $table->string('font_path')->nullable(); // Path to custom font
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_templates');
    }
};
