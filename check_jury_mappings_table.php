<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking jury_mappings table structure:\n\n";

try {
    $columns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'jury_mappings' AND table_schema = 'postgres' ORDER BY ordinal_position");
    
    foreach ($columns as $col) {
        echo "- {$col->column_name} ({$col->data_type})\n";
    }
    
    echo "\n\nChecking if table has data:\n";
    $count = DB::table('jury_mappings')->count();
    echo "Total records: {$count}\n";
    
    if ($count > 0) {
        echo "\nSample record:\n";
        $sample = DB::table('jury_mappings')->first();
        print_r($sample);
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
