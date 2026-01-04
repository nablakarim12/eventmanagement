<?php

/**
 * SYNC MIGRATIONS TABLE
 * This script marks all existing migrations as run in the migrations table
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

echo "\n";
echo "========================================\n";
echo "  SYNCING MIGRATIONS TABLE\n";
echo "========================================\n\n";

try {
    // Get all migration files
    $migrationPath = __DIR__ . '/database/migrations';
    $migrationFiles = File::files($migrationPath);
    
    echo "Found " . count($migrationFiles) . " migration files\n\n";
    
    // Get current migrations in database
    $ranMigrations = DB::table('migrations')->pluck('migration')->toArray();
    
    echo "Currently ran migrations: " . count($ranMigrations) . "\n\n";
    
    // Get the highest batch number
    $lastBatch = DB::table('migrations')->max('batch') ?? 0;
    $newBatch = $lastBatch + 1;
    
    $synced = 0;
    $skipped = 0;
    
    foreach ($migrationFiles as $file) {
        $migrationName = str_replace('.php', '', $file->getFilename());
        
        if (!in_array($migrationName, $ranMigrations)) {
            // Check if table exists by trying to describe it
            $tableName = $this->getTableNameFromMigration($migrationName);
            
            if ($tableName && $this->tableExists($tableName)) {
                DB::table('migrations')->insert([
                    'migration' => $migrationName,
                    'batch' => $newBatch
                ]);
                echo "✓ Synced: $migrationName\n";
                $synced++;
            } else {
                echo "⊘ Skipped: $migrationName (table doesn't exist)\n";
                $skipped++;
            }
        }
    }
    
    echo "\n";
    echo "========================================\n";
    echo "✓ Sync complete!\n";
    echo "  Synced: $synced migrations\n";
    echo "  Skipped: $skipped migrations\n";
    echo "========================================\n\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

function getTableNameFromMigration($migrationName) {
    if (preg_match('/create_(.+)_table/', $migrationName, $matches)) {
        return $matches[1];
    }
    return null;
}

function tableExists($tableName) {
    try {
        return DB::getSchemaBuilder()->hasTable($tableName);
    } catch (\Exception $e) {
        return false;
    }
}
