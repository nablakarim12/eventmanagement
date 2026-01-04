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
        Schema::create('event_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Gold Medal", "Silver Medal", "Best Innovation"
            $table->string('display_name')->nullable(); // Optional custom display name
            $table->integer('rank')->default(0); // 1 = 1st place, 2 = 2nd place, etc.
            $table->string('award_type')->default('standard'); // standard, custom
            $table->boolean('is_mandatory')->default(false); // Gold/Silver/Bronze are mandatory
            $table->text('description')->nullable();
            $table->string('color')->nullable(); // Badge/ribbon color
            $table->integer('order')->default(0); // Display order
            $table->timestamps();
            
            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_awards');
    }
};
