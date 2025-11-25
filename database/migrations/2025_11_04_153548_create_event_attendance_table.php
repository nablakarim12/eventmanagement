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
        Schema::create('event_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('registration_id')->constrained('event_registrations')->onDelete('cascade');
            $table->datetime('check_in_time')->nullable();
            $table->datetime('check_out_time')->nullable();
            $table->string('check_in_method')->default('qr'); // qr, manual, auto
            $table->string('check_out_method')->default('qr'); // qr, manual, auto
            $table->text('check_in_location')->nullable(); // GPS coordinates or location name
            $table->text('check_out_location')->nullable();
            $table->integer('total_duration_minutes')->nullable(); // auto calculated
            $table->enum('status', ['present', 'absent', 'partial'])->default('absent');
            $table->text('notes')->nullable();
            $table->boolean('certificate_generated')->default(false);
            $table->datetime('certificate_generated_at')->nullable();
            $table->timestamps();
            
            $table->unique(['event_id', 'user_id']); // One attendance record per user per event
            $table->index(['event_id', 'status']);
            $table->index(['registration_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attendance');
    }
};
