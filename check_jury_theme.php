<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking jury members' theme and category data:\n\n";

$juryRegs = \App\Models\EventRegistration::where('event_id', 2)
    ->where('role', 'jury')
    ->get();

foreach ($juryRegs as $reg) {
    echo "=== Jury: {$reg->user->name} ===\n";
    
    $paper = \App\Models\EventPaper::where('event_id', $reg->event_id)
        ->where('user_id', $reg->user_id)
        ->first();
    
    if ($paper) {
        echo "  Has event_paper record:\n";
        echo "    Theme: " . ($paper->product_theme ?? 'NULL') . "\n";
        echo "    Category: " . ($paper->product_category ?? 'NULL') . "\n";
    } else {
        echo "  No event_paper found\n";
    }
    echo "\n";
}
