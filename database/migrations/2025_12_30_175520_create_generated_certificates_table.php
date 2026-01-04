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
            $table->foreignId('registration_id')->constrained('event_registrations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('certificate_templates')->onDelete('set null');
            $table->string('certificate_type'); // 'participant' or 'jury'
            $table->string('certificate_url'); // Cloudinary URL
            $table->string('cloudinary_public_id')->nullable();
            $table->string('participant_name');
            $table->string('event_name');
            $table->string('award_name')->nullable(); // For Innovation awards
            $table->date('issued_date');
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
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
