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
            // Delivery mode for innovation competitions
            $table->enum('delivery_mode', ['face_to_face', 'online', 'hybrid'])->nullable()->after('category_id');
            
            // Face-to-face event details
            $table->datetime('f2f_start_date')->nullable()->after('delivery_mode');
            $table->datetime('f2f_end_date')->nullable()->after('f2f_start_date');
            $table->time('f2f_start_time')->nullable()->after('f2f_end_date');
            $table->time('f2f_end_time')->nullable()->after('f2f_start_time');
            $table->date('f2f_paper_deadline')->nullable()->after('f2f_end_time');
            $table->date('f2f_product_deadline')->nullable()->after('f2f_paper_deadline');
            $table->date('f2f_abstract_deadline')->nullable()->after('f2f_product_deadline');
            $table->date('f2f_acceptance_date')->nullable()->after('f2f_abstract_deadline');
            
            // Online event details
            $table->datetime('online_start_date')->nullable()->after('f2f_acceptance_date');
            $table->datetime('online_end_date')->nullable()->after('online_start_date');
            $table->time('online_start_time')->nullable()->after('online_end_date');
            $table->time('online_end_time')->nullable()->after('online_start_time');
            $table->string('online_platform_url')->nullable()->after('online_end_time');
            $table->date('online_paper_deadline')->nullable()->after('online_platform_url');
            $table->date('online_product_deadline')->nullable()->after('online_paper_deadline');
            $table->date('online_abstract_deadline')->nullable()->after('online_product_deadline');
            $table->date('online_acceptance_date')->nullable()->after('online_abstract_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_mode',
                'f2f_start_date',
                'f2f_end_date',
                'f2f_start_time',
                'f2f_end_time',
                'f2f_paper_deadline',
                'f2f_product_deadline',
                'f2f_abstract_deadline',
                'f2f_acceptance_date',
                'online_start_date',
                'online_end_date',
                'online_start_time',
                'online_end_time',
                'online_platform_url',
                'online_paper_deadline',
                'online_product_deadline',
                'online_abstract_deadline',
                'online_acceptance_date',
            ]);
        });
    }
};
