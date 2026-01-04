<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Remove unused columns that are not used in Innovation/Conference forms
     * and are always NULL
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Remove Innovation-specific columns that are not used in forms
            // (f2f_paper_deadline is used, but product/abstract are not)
            $table->dropColumn([
                'f2f_product_deadline',
                'f2f_abstract_deadline',
                'online_product_deadline',
                'online_abstract_deadline',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Restore the columns if migration is rolled back
            $table->date('f2f_product_deadline')->nullable()->after('f2f_paper_deadline');
            $table->date('f2f_abstract_deadline')->nullable()->after('f2f_product_deadline');
            $table->date('online_product_deadline')->nullable()->after('online_paper_deadline');
            $table->date('online_abstract_deadline')->nullable()->after('online_product_deadline');
        });
    }
};
