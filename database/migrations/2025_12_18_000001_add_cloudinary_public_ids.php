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
        // Add public_id columns to store Cloudinary public IDs
        
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'featured_image_public_id')) {
                $table->string('featured_image_public_id')->nullable()->after('featured_image');
            }
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_registrations', 'qr_code_public_id')) {
                $table->string('qr_code_public_id')->nullable()->after('qr_code');
            }
        });

        Schema::table('paper_submissions', function (Blueprint $table) {
            if (Schema::hasTable('paper_submissions')) {
                if (!Schema::hasColumn('paper_submissions', 'file_public_id')) {
                    $table->string('file_public_id')->nullable()->after('file_path');
                }
            }
        });

        Schema::table('event_materials', function (Blueprint $table) {
            if (Schema::hasTable('event_materials')) {
                if (!Schema::hasColumn('event_materials', 'file_public_id')) {
                    $table->string('file_public_id')->nullable()->after('file_path');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'featured_image_public_id')) {
                $table->dropColumn('featured_image_public_id');
            }
        });

        Schema::table('event_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('event_registrations', 'qr_code_public_id')) {
                $table->dropColumn('qr_code_public_id');
            }
        });

        if (Schema::hasTable('paper_submissions')) {
            Schema::table('paper_submissions', function (Blueprint $table) {
                if (Schema::hasColumn('paper_submissions', 'file_public_id')) {
                    $table->dropColumn('file_public_id');
                }
            });
        }

        if (Schema::hasTable('event_materials')) {
            Schema::table('event_materials', function (Blueprint $table) {
                if (Schema::hasColumn('event_materials', 'file_public_id')) {
                    $table->dropColumn('file_public_id');
                }
            });
        }
    }
};
