<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== All Rubric Tables ===\n\n";

$tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name LIKE '%rubric%'");

foreach ($tables as $table) {
    echo "Table: " . $table->table_name . "\n";
    
    // Count rows
    $count = DB::select("SELECT COUNT(*) as count FROM " . $table->table_name)[0]->count;
    echo "  Rows: " . $count . "\n";
    
    // Show columns
    $columns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = '{$table->table_name}'");
    echo "  Columns:\n";
    foreach ($columns as $col) {
        echo "    - {$col->column_name} ({$col->data_type})\n";
    }
    echo "\n";
}

echo "\n=== Checking for score-related tables ===\n\n";

$scoreTables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name LIKE '%score%'");

foreach ($scoreTables as $table) {
    echo "Table: " . $table->table_name . "\n";
    $count = DB::select("SELECT COUNT(*) as count FROM " . $table->table_name)[0]->count;
    echo "  Rows: " . $count . "\n\n";
}
