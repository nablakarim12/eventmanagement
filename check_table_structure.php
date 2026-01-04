<?php

/**
 * CHECK ALL TABLES AND THEIR COLUMNS
 * This shows exactly what exists in your database
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n";
echo "========================================\n";
echo "  DATABASE STRUCTURE CHECK\n";
echo "========================================\n\n";

try {
    // Get all tables
    $tables = DB::select("
        SELECT tablename 
        FROM pg_tables 
        WHERE schemaname = 'public' 
        AND tablename NOT LIKE 'pg_%'
        ORDER BY tablename
    ");

    echo "Total tables found: " . count($tables) . "\n\n";
    
    foreach ($tables as $table) {
        $tableName = $table->tablename;
        
        // Skip system tables
        if (in_array($tableName, ['migrations', 'password_reset_tokens', 'personal_access_tokens', 'failed_jobs'])) {
            continue;
        }
        
        // Get column count
        $columns = DB::select("
            SELECT column_name, data_type 
            FROM information_schema.columns 
            WHERE table_name = ? 
            ORDER BY ordinal_position
        ", [$tableName]);
        
        // Get record count
        $count = DB::table($tableName)->count();
        
        $status = $count > 0 ? "✓ $count records" : "○ EMPTY";
        
        echo sprintf("%-40s | %-12s | %d columns\n", 
            $tableName, 
            $status,
            count($columns)
        );
    }
    
    echo "\n========================================\n";
    echo "  SUMMARY\n";
    echo "========================================\n\n";
    
    $essentialTables = [
        'admins' => 'Admin accounts',
        'users' => 'User/Organizer accounts',
        'events' => 'Events',
        'event_registrations' => 'Event registrations',
        'event_categories' => 'Event categories',
        'event_types' => 'Event types',
        'event_papers' => 'Paper submissions',
        'paper_reviews' => 'Paper reviews',
    ];
    
    echo "Essential Tables Status:\n";
    echo "------------------------\n";
    
    foreach ($essentialTables as $table => $description) {
        $exists = Schema::hasTable($table);
        if ($exists) {
            $count = DB::table($table)->count();
            $icon = $count > 0 ? '✓' : '○';
            echo "$icon $description: " . ($exists ? "EXISTS ($count records)" : "MISSING") . "\n";
        } else {
            echo "✗ $description: MISSING\n";
        }
    }
    
    echo "\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}
