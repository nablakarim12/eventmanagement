<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Get a sample registration record to see what data exists
$registration = DB::table('event_registrations')
    ->where('event_id', 2)
    ->first();

if ($registration) {
    echo "Sample registration data:\n";
    echo "ID: " . $registration->id . "\n";
    echo "User ID: " . $registration->user_id . "\n";
    echo "Selected Category: " . ($registration->selected_category ?? 'NULL') . "\n";
    echo "Selected Theme: " . ($registration->selected_theme ?? 'NULL') . "\n";
    echo "\nAll columns:\n";
    foreach ($registration as $key => $value) {
        echo "$key: $value\n";
    }
} else {
    echo "No registration found for event 2\n";
}
