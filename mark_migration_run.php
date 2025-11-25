<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Insert the rejected_reason migration as already run
DB::connection('pgsql')
    ->table('migrations')
    ->insert([
        'migration' => '2025_11_20_164327_add_rejected_reason_to_event_registrations_table',
        'batch' => 12
    ]);

echo "Migration marked as run successfully!\n";
