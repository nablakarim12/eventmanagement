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
        Schema::create('event_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type'); // pdf, docx, pptx, etc.
            $table->bigInteger('file_size'); // in bytes
            $table->enum('access_type', ['public', 'registered_only', 'checked_in_only'])->default('registered_only');
            $table->boolean('is_downloadable')->default(true);
            $table->datetime('available_from')->nullable();
            $table->datetime('available_until')->nullable();
            $table->integer('download_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['event_id', 'is_active']);
            $table->index(['access_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_materials');
    }
};
