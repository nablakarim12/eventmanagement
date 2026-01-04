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
        Schema::table('event_registrations', function (Blueprint $table) {
            // Presentation selection and scheduling
            $table->string('presentation_status')->nullable()->after('selected_category'); // selected, rejected, pending_review
            $table->integer('presentation_queue')->nullable()->after('presentation_status'); // Queue number for on-site presentation
            $table->dateTime('presentation_time')->nullable()->after('presentation_queue'); // Scheduled presentation time
            $table->text('presentation_link')->nullable()->after('presentation_time'); // Online meeting link (for online/hybrid)
            $table->text('presentation_location')->nullable()->after('presentation_link'); // Physical location (for face_to_face/hybrid)
            $table->decimal('average_score', 5, 2)->nullable()->after('presentation_location'); // Average review score
            $table->dateTime('presentation_notified_at')->nullable()->after('average_score'); // When notification was sent
            $table->text('rejection_reason')->nullable()->after('presentation_notified_at'); // Reason for rejection
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'presentation_status',
                'presentation_queue',
                'presentation_time',
                'presentation_link',
                'presentation_location',
                'average_score',
                'presentation_notified_at',
                'rejection_reason',
            ]);
        });
    }
};
