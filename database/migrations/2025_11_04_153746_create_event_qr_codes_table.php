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
        Schema::create('event_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('qr_code')->unique(); // The QR code string/hash
            $table->enum('type', ['check_in', 'check_out', 'general'])->default('check_in');
            $table->string('qr_image_path'); // Path to QR code image
            $table->text('data')->nullable(); // JSON data encoded in QR
            $table->datetime('valid_from')->nullable();
            $table->datetime('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('scan_count')->default(0);
            $table->datetime('last_scanned_at')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['event_id', 'type', 'is_active']);
            $table->index(['qr_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_qr_codes');
    }
};
