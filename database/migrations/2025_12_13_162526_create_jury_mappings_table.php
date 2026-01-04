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
        Schema::create('jury_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('reviewer_registration_id')->constrained('event_registrations')->onDelete('cascade');
            $table->foreignId('participant_registration_id')->constrained('event_registrations')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('event_organizers')->onDelete('cascade');
            $table->enum('status', ['pending', 'completed', 'skipped'])->default('pending');
            $table->text('review_notes')->nullable();
            $table->integer('score')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['event_id', 'reviewer_registration_id']);
            $table->index(['event_id', 'participant_registration_id']);
            $table->unique(['reviewer_registration_id', 'participant_registration_id'], 'unique_reviewer_participant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jury_mappings');
    }
};
