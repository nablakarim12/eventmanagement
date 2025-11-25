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
        Schema::table('event_registrations', function (Blueprint $table) {
            // Only add approval_status if it doesn't exist
            if (!Schema::hasColumn('event_registrations', 'approval_status')) {
                $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            }
        });
        
        // Update existing registrations to have default role if null
        DB::statement("UPDATE event_registrations SET role = 'participant' WHERE role IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('event_registrations', 'approval_status')) {
                $table->dropColumn('approval_status');
            }
        });
        
        // Reset role column to nullable varchar
        DB::statement("ALTER TABLE event_registrations ALTER COLUMN role DROP NOT NULL");
        DB::statement("ALTER TABLE event_registrations ALTER COLUMN role DROP DEFAULT");
    }
};
