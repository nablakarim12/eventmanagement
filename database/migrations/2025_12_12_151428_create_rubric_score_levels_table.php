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
        Schema::create('rubric_score_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubric_item_id')->constrained()->onDelete('cascade');
            $table->integer('level'); // 1, 2, 3, 4, 5
            $table->text('description'); // Description for this score level
            $table->timestamps();
            
            $table->index('rubric_item_id');
            $table->unique(['rubric_item_id', 'level']); // Each item can only have one description per level
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubric_score_levels');
    }
};
