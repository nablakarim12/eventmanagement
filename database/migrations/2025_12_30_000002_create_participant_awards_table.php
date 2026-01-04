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
        Schema::create('participant_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_award_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('registration_id')->constrained('event_registrations')->onDelete('cascade');
            $table->decimal('final_score', 5, 2)->nullable(); // Final evaluation score
            $table->string('category')->nullable(); // Category participant competed in
            $table->string('theme')->nullable(); // Theme participant competed in
            $table->string('ranking_scope'); // 'category_theme', 'category', 'theme', 'overall'
            $table->integer('rank')->default(0); // Position in their ranking scope
            $table->boolean('is_published')->default(false); // Whether result is published to participant
            $table->text('organizer_notes')->nullable(); // Internal notes
            $table->timestamps();
            
            $table->index(['event_id', 'ranking_scope']);
            $table->index(['user_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_awards');
    }
};
