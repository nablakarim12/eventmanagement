<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Event Papers columns:\n";
$columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'event_papers' ORDER BY ordinal_position");
foreach ($columns as $col) {
    echo "- " . $col->column_name . "\n";
}

echo "\n\nSample event_papers data:\n";
$paper = DB::table('event_papers')->where('event_id', 2)->first();
if ($paper) {
    foreach ($paper as $key => $value) {
        echo "$key: $value\n";
    }
}
