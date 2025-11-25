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
        Schema::create('paper_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('registration_id')->nullable()->constrained('event_registrations')->onDelete('set null');
            
            $table->string('submission_code')->unique(); // PAPER-XXXXXXXXXXXX
            $table->string('title');
            $table->text('abstract');
            $table->text('keywords')->nullable();
            $table->string('paper_file_path'); // PDF file path
            $table->string('paper_file_name'); // Original filename
            $table->bigInteger('file_size')->nullable(); // File size in bytes
            
            $table->enum('status', ['draft', 'submitted', 'under_review', 'reviewed', 'accepted', 'rejected'])->default('submitted');
            $table->text('rejection_reason')->nullable();
            
            $table->decimal('average_score', 5, 2)->nullable(); // Average review score
            $table->integer('review_count')->default(0);
            
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->index(['event_id', 'user_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_submissions');
    }
};
