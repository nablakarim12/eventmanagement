<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('event_organizers', 'google_id')) {
    Schema::table('event_organizers', function (Blueprint $table) {
        $table->string('google_id')->nullable()->unique()->after('org_email');
    });
    echo "✓ Added google_id column to event_organizers table\n";
} else {
    echo "✓ google_id column already exists\n";
}

// Make password nullable
\DB::statement("ALTER TABLE event_organizers ALTER COLUMN password DROP NOT NULL");
echo "✓ Made password column nullable\n";

echo "\nDone! Google OAuth setup ready.\n";
