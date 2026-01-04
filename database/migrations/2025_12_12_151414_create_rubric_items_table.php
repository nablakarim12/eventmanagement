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
        Schema::create('rubric_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubric_category_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Abstract", "Problem Statement & Objectives"
            $table->text('description')->nullable(); // Additional context
            $table->integer('max_score')->default(5); // Usually 5 for each item
            $table->integer('order')->default(0); // Display order within category
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('rubric_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubric_items');
    }
};
