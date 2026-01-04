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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_registration_id')->constrained('event_registrations')->onDelete('cascade');
            $table->tinyInteger('overall_rating')->nullable();
            $table->tinyInteger('content_rating')->nullable();
            $table->tinyInteger('organization_rating')->nullable();
            $table->tinyInteger('platform_rating')->nullable();
            $table->tinyInteger('venue_rating')->nullable();
            $table->text('comments')->nullable();
            $table->text('suggestions')->nullable();
            $table->text('system_feedback')->nullable();
            $table->boolean('would_recommend')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
