<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n========================================\n";
echo "ALL EVENT_REGISTRATIONS COLUMNS\n";
echo "========================================\n\n";

$columns = DB::select("
    SELECT column_name, data_type, is_nullable
    FROM information_schema.columns 
    WHERE table_name = 'event_registrations'
    ORDER BY ordinal_position
");

foreach($columns as $col) {
    echo sprintf("%-40s %-20s %s\n", $col->column_name, $col->data_type, $col->is_nullable);
}
