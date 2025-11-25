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
        Schema::create('event_organizer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_organizer_id')->constrained()->onDelete('cascade');
            $table->string('document_type'); // e.g., 'business_license', 'company_profile', 'previous_event_proof'
            $table->string('file_name');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_organizer_documents');
    }
};
