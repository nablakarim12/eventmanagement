<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n========================================\n";
echo "EVENT_REGISTRATIONS TABLE COLUMNS\n";
echo "========================================\n\n";

$columns = DB::select("
    SELECT column_name, data_type 
    FROM information_schema.columns 
    WHERE table_name = 'event_registrations'
    ORDER BY ordinal_position
");

foreach($columns as $col) {
    if (strpos($col->column_name, 'jury') !== false || 
        strpos($col->column_name, 'certificate') !== false || 
        strpos($col->column_name, 'document') !== false ||
        strpos($col->column_name, 'qualification') !== false) {
        echo sprintf("%-40s %s\n", $col->column_name, $col->data_type);
    }
}
