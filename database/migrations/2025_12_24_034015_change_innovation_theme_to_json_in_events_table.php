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
        // Use raw SQL for PostgreSQL compatibility
        DB::statement('ALTER TABLE events ALTER COLUMN innovation_theme TYPE JSON USING innovation_theme::json');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE events ALTER COLUMN innovation_theme TYPE VARCHAR(255)');
    }
};
