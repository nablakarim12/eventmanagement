<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $result = DB::select("
        SELECT column_name, data_type, is_nullable
        FROM information_schema.columns 
        WHERE table_name = 'jury_mappings'
        ORDER BY ordinal_position
    ");
    
    echo "jury_mappings table columns:\n\n";
    foreach ($result as $col) {
        echo "- {$col->column_name} ({$col->data_type}) " . ($col->is_nullable === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
