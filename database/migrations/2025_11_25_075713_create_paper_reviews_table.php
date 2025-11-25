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
        Schema::create('paper_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_submission_id')->constrained()->onDelete('cascade');
            $table->foreignId('jury_assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('jury_registration_id')->constrained('event_registrations')->onDelete('cascade');
            
            // Review scores (1-10 scale)
            $table->decimal('originality_score', 4, 2)->nullable();
            $table->decimal('methodology_score', 4, 2)->nullable();
            $table->decimal('clarity_score', 4, 2)->nullable();
            $table->decimal('contribution_score', 4, 2)->nullable();
            $table->decimal('overall_score', 5, 2)->nullable(); // Average of all criteria
            
            // Review content
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('comments')->nullable();
            $table->text('confidential_comments')->nullable(); // Only for organizers
            
            $table->enum('recommendation', ['accept', 'minor_revision', 'major_revision', 'reject'])->nullable();
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            $table->index('paper_submission_id');
            $table->index('jury_registration_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_reviews');
    }
};
