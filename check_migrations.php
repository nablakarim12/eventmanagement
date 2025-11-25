<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$migrations = DB::connection('pgsql')
    ->table('migrations')
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();

echo "Recent Migrations:\n";
foreach ($migrations as $migration) {
    echo "ID: {$migration->id} | Batch: {$migration->batch} | {$migration->migration}\n";
}
