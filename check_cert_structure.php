<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking certificate-related columns...\n\n";

// Check event_registrations table
echo "event_registrations table:\n";
$columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'event_registrations' AND column_name LIKE '%cert%'");
foreach ($columns as $col) {
    echo "- " . $col->column_name . "\n";
}

echo "\n";

// Check if there's a certificates table
$tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_name LIKE '%cert%'");
echo "Tables with 'cert' in name:\n";
foreach ($tables as $table) {
    echo "- " . $table->table_name . "\n";
}

echo "\nDone!\n";
