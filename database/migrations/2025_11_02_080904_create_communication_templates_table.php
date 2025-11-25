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
        Schema::create('communication_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('event_organizers')->onDelete('cascade');
            $table->string('name');
            $table->string('subject');
            $table->text('message');
            $table->enum('type', ['welcome', 'reminder', 'update', 'follow-up', 'custom'])->default('custom');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_templates');
    }
};
