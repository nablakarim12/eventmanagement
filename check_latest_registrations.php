<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking latest registrations for eduinnovate event (event_id=2):\n\n";

$latestRegs = \App\Models\EventRegistration::where('event_id', 2)
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get();

foreach ($latestRegs as $reg) {
    echo "=== Registration ID: {$reg->id} ===\n";
    echo "User: {$reg->user->name}\n";
    echo "Role: {$reg->role}\n";
    echo "Created: {$reg->created_at}\n";
    echo "Selected Category: " . ($reg->selected_category ?? 'NULL') . "\n";
    echo "Registration Data: " . json_encode($reg->registration_data, JSON_PRETTY_PRINT) . "\n";
    
    // Check event paper
    $paper = \App\Models\EventPaper::where('event_id', $reg->event_id)
        ->where('user_id', $reg->user_id)
        ->first();
    
    if ($paper) {
        echo "Event Paper Found:\n";
        echo "  - Title: {$paper->title}\n";
        echo "  - Category: {$paper->paper_category}\n";
        echo "  - Abstract: " . substr($paper->abstract ?? '', 0, 50) . "...\n";
    } else {
        echo "Event Paper: Not found\n";
    }
    echo "\n";
}
