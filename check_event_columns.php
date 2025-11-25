<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get event table columns
$columns = DB::select("
    SELECT column_name 
    FROM information_schema.columns 
    WHERE table_name = 'events'
    ORDER BY ordinal_position
");

echo "Events Table Columns:\n";
foreach ($columns as $col) {
    echo "  - {$col->column_name}\n";
}

// Get first event to see data
echo "\nFirst Event:\n";
$event = DB::table('events')->first();
if ($event) {
    foreach ($event as $key => $value) {
        echo "  {$key}: " . (is_string($value) && strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) . "\n";
    }
}
