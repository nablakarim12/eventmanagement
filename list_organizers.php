<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Event Organizers in Database ===\n\n";

$organizers = DB::table('event_organizers')
    ->select('id', 'org_email', 'org_name', 'phone', 'created_at')
    ->get();

if ($organizers->isEmpty()) {
    echo "No organizers found in the database.\n";
} else {
    foreach ($organizers as $org) {
        echo "ID: {$org->id}\n";
        echo "Organization: {$org->org_name}\n";
        echo "Email: {$org->org_email}\n";
        echo "Phone: {$org->phone}\n";
        echo "Created: {$org->created_at}\n";
        echo "-------------------\n";
    }
}

echo "\nTotal organizers: " . $organizers->count() . "\n";
