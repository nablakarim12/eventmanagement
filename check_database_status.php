<?php

/**
 * COMPREHENSIVE DATABASE RECOVERY SCRIPT
 * This script provides options to recover different types of data
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n";
echo "================================================\n";
echo "  DATABASE COMPREHENSIVE RECOVERY\n";
echo "================================================\n\n";

try {
    // Test database connection
    echo "Testing database connection...\n";
    DB::connection()->getPdo();
    echo "✓ Database connection successful!\n\n";

    // Check all tables
    echo "Checking database tables...\n";
    echo "================================================\n\n";

    $allTables = DB::select("
        SELECT tablename 
        FROM pg_tables 
        WHERE schemaname = 'public' 
        ORDER BY tablename
    ");

    $tableStats = [];
    
    foreach ($allTables as $table) {
        $tableName = $table->tablename;
        
        // Skip migrations and system tables
        if (in_array($tableName, ['migrations', 'password_reset_tokens', 'personal_access_tokens', 'failed_jobs'])) {
            continue;
        }
        
        try {
            $count = DB::table($tableName)->count();
            $tableStats[$tableName] = $count;
        } catch (\Exception $e) {
            $tableStats[$tableName] = 'ERROR';
        }
    }

    // Display table statistics
    echo "TABLE STATUS:\n";
    echo "----------------------------------------------------\n";
    printf("%-35s | %s\n", "Table Name", "Records");
    echo "----------------------------------------------------\n";
    
    foreach ($tableStats as $table => $count) {
        $status = $count === 0 ? '❌ EMPTY' : ($count === 'ERROR' ? '⚠️  ERROR' : "✓ $count");
        printf("%-35s | %s\n", $table, $status);
    }
    
    echo "----------------------------------------------------\n\n";

    // Identify critical empty tables
    $emptyTables = array_filter($tableStats, function($count) {
        return $count === 0;
    });

    if (!empty($emptyTables)) {
        echo "⚠️  CRITICAL: The following tables are empty:\n";
        foreach (array_keys($emptyTables) as $table) {
            echo "  - $table\n";
        }
        echo "\n";
    }

    // Check for specific critical data
    echo "CRITICAL DATA CHECK:\n";
    echo "----------------------------------------------------\n";
    
    $checks = [
        'Admins' => DB::table('admins')->count(),
        'Event Categories' => isset($tableStats['event_categories']) ? $tableStats['event_categories'] : 0,
        'Event Types' => isset($tableStats['event_types']) ? $tableStats['event_types'] : 0,
        'Users/Organizers' => isset($tableStats['users']) ? $tableStats['users'] : 0,
        'Events' => isset($tableStats['events']) ? $tableStats['events'] : 0,
    ];

    foreach ($checks as $item => $count) {
        $icon = $count > 0 ? '✓' : '❌';
        echo "$icon $item: $count\n";
    }
    
    echo "\n";

    // Recommendations
    echo "RECOMMENDATIONS:\n";
    echo "----------------------------------------------------\n";
    
    if ($tableStats['admins'] == 0) {
        echo "1. ⚠️  Run 'php recover_database.php' to create admin account\n";
    } else {
        echo "1. ✓ Admin account exists\n";
    }
    
    if (isset($tableStats['event_categories']) && $tableStats['event_categories'] == 0) {
        echo "2. ⚠️  Event categories need to be seeded\n";
    } else {
        echo "2. ✓ Event categories exist\n";
    }
    
    if (isset($tableStats['event_types']) && $tableStats['event_types'] == 0) {
        echo "3. ⚠️  Event types need to be seeded\n";
    } else {
        echo "3. ✓ Event types exist\n";
    }

    echo "\n";
    echo "AVAILABLE RECOVERY OPTIONS:\n";
    echo "----------------------------------------------------\n";
    echo "1. Run: php recover_database.php\n";
    echo "   - Recovers admin account\n";
    echo "   - Reseeds event categories and types\n\n";
    
    echo "2. Run: php artisan db:seed\n";
    echo "   - Runs all seeders\n";
    echo "   - Use only if tables are empty\n\n";
    
    echo "3. Run: php artisan db:seed --class=AdminSeeder\n";
    echo "   - Recovers only admin account\n\n";

    // Check if there's data that might have been partially recovered
    if (isset($tableStats['events']) && $tableStats['events'] > 0) {
        echo "\n📊 GOOD NEWS: You have {$tableStats['events']} events in the database\n";
    }
    
    if (isset($tableStats['users']) && $tableStats['users'] > 0) {
        echo "📊 GOOD NEWS: You have {$tableStats['users']} users/organizers in the database\n";
    }
    
    if (isset($tableStats['registrations']) && $tableStats['registrations'] > 0) {
        echo "📊 GOOD NEWS: You have {$tableStats['registrations']} registrations in the database\n";
    }

    echo "\n";
    echo "================================================\n";
    echo "  Analysis Complete\n";
    echo "================================================\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
