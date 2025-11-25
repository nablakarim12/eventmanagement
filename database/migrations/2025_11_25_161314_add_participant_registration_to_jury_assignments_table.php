<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jury_assignments', function (Blueprint $table) {
            // Make paper_submission_id nullable for Innovation Competitions
            $table->foreignId('paper_submission_id')->nullable()->change();
            
            // Add participant_registration_id for direct participant-to-jury mapping
            $table->foreignId('participant_registration_id')->nullable()->after('paper_submission_id')
                  ->constrained('event_registrations')->onDelete('cascade');
            
            // Add index for participant assignments
            $table->index('participant_registration_id');
            
            // Drop old unique constraint and create new one
            $table->dropUnique(['paper_submission_id', 'jury_registration_id']);
        });
        
        // Add flexible unique constraints
        DB::statement('CREATE UNIQUE INDEX jury_assignments_paper_unique ON jury_assignments (paper_submission_id, jury_registration_id) WHERE paper_submission_id IS NOT NULL');
        DB::statement('CREATE UNIQUE INDEX jury_assignments_participant_unique ON jury_assignments (participant_registration_id, jury_registration_id) WHERE participant_registration_id IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jury_assignments', function (Blueprint $table) {
            DB::statement('DROP INDEX IF EXISTS jury_assignments_paper_unique');
            DB::statement('DROP INDEX IF EXISTS jury_assignments_participant_unique');
            
            $table->dropColumn('participant_registration_id');
            $table->foreignId('paper_submission_id')->nullable(false)->change();
            
            $table->unique(['paper_submission_id', 'jury_registration_id']);
        });
    }
};
