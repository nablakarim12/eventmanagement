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
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Registration Information
            $table->string('registration_code')->unique(); // Unique registration code
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'attended'])->default('confirmed');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable(); // stripe, paypal, cash, etc.
            $table->string('payment_transaction_id')->nullable();
            
            // Registration Details
            $table->json('registration_data')->nullable(); // Store any additional form data
            $table->text('special_requirements')->nullable();
            $table->text('dietary_restrictions')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            
            // Timestamps
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('attended_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->unique(['event_id', 'user_id'], 'unique_event_user_registration');
            $table->index(['status', 'payment_status']);
            $table->index('registration_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
