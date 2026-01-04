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
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'f2f_payment_deadline')) {
                $table->dateTime('f2f_payment_deadline')->nullable();
            }
            if (!Schema::hasColumn('events', 'online_payment_deadline')) {
                $table->dateTime('online_payment_deadline')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'f2f_payment_deadline')) {
                $table->dropColumn('f2f_payment_deadline');
            }
            if (Schema::hasColumn('events', 'online_payment_deadline')) {
                $table->dropColumn('online_payment_deadline');
            }
        });
    }
};
