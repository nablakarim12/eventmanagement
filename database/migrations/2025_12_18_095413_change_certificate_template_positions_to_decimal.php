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
        Schema::table('certificate_templates', function (Blueprint $table) {
            // Change position columns from integer to decimal(8,2) to support millimeters
            $table->decimal('name_x', 8, 2)->default(0)->change();
            $table->decimal('name_y', 8, 2)->default(0)->change();
            $table->decimal('role_x', 8, 2)->default(0)->change();
            $table->decimal('role_y', 8, 2)->default(0)->change();
            $table->decimal('event_name_x', 8, 2)->default(0)->change();
            $table->decimal('event_name_y', 8, 2)->default(0)->change();
            $table->decimal('date_x', 8, 2)->default(0)->change();
            $table->decimal('date_y', 8, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
            // Revert back to integer
            $table->integer('name_x')->default(0)->change();
            $table->integer('name_y')->default(0)->change();
            $table->integer('role_x')->default(0)->change();
            $table->integer('role_y')->default(0)->change();
            $table->integer('event_name_x')->default(0)->change();
            $table->integer('event_name_y')->default(0)->change();
            $table->integer('date_x')->default(0)->change();
            $table->integer('date_y')->default(0)->change();
        });
    }
};
