<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    // Check if columns exist
    $hasCategory = Schema::hasColumn('event_registrations', 'selected_category');
    $hasTheme = Schema::hasColumn('event_registrations', 'selected_theme');
    
    echo "Has selected_category: " . ($hasCategory ? 'YES' : 'NO') . "\n";
    echo "Has selected_theme: " . ($hasTheme ? 'YES' : 'NO') . "\n";
    
    if (!$hasCategory) {
        DB::statement('ALTER TABLE event_registrations ADD COLUMN selected_category VARCHAR(255) NULL');
        echo "✓ Added selected_category column\n";
    } else {
        echo "✓ selected_category column already exists\n";
    }
    
    if (!$hasTheme) {
        DB::statement('ALTER TABLE event_registrations ADD COLUMN selected_theme VARCHAR(255) NULL');
        echo "✓ Added selected_theme column\n";
    } else {
        echo "✓ selected_theme column already exists\n";
    }
    
    echo "\nDone!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
