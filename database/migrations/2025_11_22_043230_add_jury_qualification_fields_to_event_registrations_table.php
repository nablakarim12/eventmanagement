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
            // Add jury qualification fields
            $table->text('jury_qualification_summary')->nullable()->after('role');
            $table->string('jury_qualification_documents')->nullable()->after('jury_qualification_summary'); // JSON array of document paths
            $table->text('jury_experience')->nullable()->after('jury_qualification_documents');
            $table->text('jury_expertise_areas')->nullable()->after('jury_experience');
            $table->string('jury_institution')->nullable()->after('jury_expertise_areas');
            $table->string('jury_position')->nullable()->after('jury_institution');
            $table->integer('jury_years_experience')->nullable()->after('jury_position');
            
            // Jury approval specific fields
            $table->text('jury_approval_notes')->nullable()->after('rejected_reason');
            $table->timestamp('jury_reviewed_at')->nullable()->after('jury_approval_notes');
            $table->foreignId('jury_reviewed_by')->nullable()->after('jury_reviewed_at');
            
            // Add index for jury-related queries
            $table->index(['role', 'approval_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropIndex(['role', 'approval_status']);
            $table->dropColumn([
                'jury_qualification_summary',
                'jury_qualification_documents',
                'jury_experience',
                'jury_expertise_areas',
                'jury_institution',
                'jury_position',
                'jury_years_experience',
                'jury_approval_notes',
                'jury_reviewed_at',
                'jury_reviewed_by'
            ]);
        });
    }
};
