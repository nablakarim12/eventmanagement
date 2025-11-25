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
        Schema::create('jury_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_submission_id')->constrained()->onDelete('cascade');
            $table->foreignId('jury_registration_id')->constrained('event_registrations')->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('event_organizers')->onDelete('cascade');
            
            $table->enum('status', ['pending', 'accepted', 'declined', 'completed'])->default('pending');
            $table->timestamp('assigned_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('decline_reason')->nullable();
            $table->timestamps();
            
            // Prevent duplicate assignments
            $table->unique(['paper_submission_id', 'jury_registration_id']);
            $table->index('jury_registration_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jury_assignments');
    }
};
