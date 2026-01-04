<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking event_papers table for latest submissions:\n\n";

$papers = \App\Models\EventPaper::where('event_id', 2)
    ->orderBy('created_at', 'desc')
    ->take(3)
    ->get();

foreach ($papers as $paper) {
    echo "=== Paper ID: {$paper->id} ===\n";
    echo "All Fields:\n";
    print_r($paper->toArray());
    echo "\n\n";
}

// Also check the database schema
echo "\nChecking table columns:\n";
$columns = \DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'event_papers' AND table_schema = 'postgres'");
foreach ($columns as $col) {
    echo "- {$col->column_name}: {$col->data_type}\n";
}
