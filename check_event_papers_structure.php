<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== event_papers Table Structure ===\n\n";

$columns = DB::select("
    SELECT column_name, data_type, is_nullable
    FROM information_schema.columns
    WHERE table_name = 'event_papers'
    ORDER BY ordinal_position
");

foreach ($columns as $col) {
    echo "{$col->column_name} ({$col->data_type}) - Nullable: {$col->is_nullable}\n";
}

echo "\n=== Sample Data ===\n";
$papers = DB::table('event_papers')
    ->where('event_id', 2)
    ->limit(3)
    ->get();

foreach ($papers as $paper) {
    echo "\nPaper ID: {$paper->id}\n";
    echo "  User ID: {$paper->user_id}\n";
    echo "  Event ID: {$paper->event_id}\n";
    if (property_exists($paper, 'registration_id')) {
        echo "  Registration ID: {$paper->registration_id}\n";
    } else {
        echo "  Registration ID: NOT FOUND\n";
    }
}
