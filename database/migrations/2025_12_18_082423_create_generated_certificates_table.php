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
        Schema::create('generated_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('registration_id')->constrained('event_registrations')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('certificate_templates')->onDelete('cascade');
            
            $table->string('certificate_url'); // Cloudinary URL of generated certificate
            $table->string('cloudinary_public_id')->nullable();
            $table->string('certificate_type'); // 'participant' or 'reviewer'
            
            $table->string('participant_name');
            $table->string('participant_role');
            $table->string('event_name');
            $table->string('event_date');
            
            $table->timestamp('generated_at');
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamp('emailed_at')->nullable();
            $table->timestamps();
            
            // Ensure one certificate per user per event per type
            $table->unique(['event_id', 'user_id', 'certificate_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_certificates');
    }
};
