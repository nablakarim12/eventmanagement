<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'events' ORDER BY ordinal_position");

echo "Events Table Columns:\n";
echo "====================\n\n";

foreach($columns as $col) {
    echo $col->column_name . ' (' . $col->data_type . ')' . PHP_EOL;
}
