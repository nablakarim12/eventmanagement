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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizer_id')->constrained('event_organizers')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('event_categories')->onDelete('cascade');
            
            // Basic Event Information
            $table->string('title');
            $table->text('description');
            $table->text('short_description')->nullable();
            
            // Event Timing
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->time('start_time');
            $table->time('end_time');
            
            // Location Information
            $table->string('venue_name');
            $table->text('venue_address');
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('country');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Registration Information
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->boolean('is_free')->default(true);
            $table->datetime('registration_deadline')->nullable();
            
            // Event Status and Settings
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            $table->boolean('requires_approval')->default(false);
            $table->boolean('is_public')->default(true);
            $table->boolean('allow_waitlist')->default(false);
            
            // Additional Information
            $table->json('requirements')->nullable(); // Array of requirements
            $table->json('tags')->nullable(); // Array of tags
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('website_url')->nullable();
            
            // SEO and Media
            $table->string('slug')->unique();
            $table->string('featured_image')->nullable();
            $table->json('gallery_images')->nullable(); // Array of image paths
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'start_date']);
            $table->index(['category_id', 'status']);
            $table->index(['organizer_id', 'status']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
