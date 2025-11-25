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
        Schema::create('event_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('attendance_id')->constrained('event_attendance')->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->string('participant_name');
            $table->string('event_title');
            $table->date('event_date');
            $table->integer('attendance_hours')->nullable();
            $table->string('template_used')->default('default');
            $table->string('certificate_path'); // PDF file path
            $table->datetime('generated_at');
            $table->datetime('emailed_at')->nullable();
            $table->integer('download_count')->default(0);
            $table->datetime('last_downloaded_at')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->string('verification_code')->unique();
            $table->json('certificate_data')->nullable(); // Store additional certificate info
            $table->timestamps();
            
            $table->index(['event_id', 'user_id']);
            $table->index(['certificate_number']);
            $table->index(['verification_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_certificates');
    }
};
