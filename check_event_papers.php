<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking event_papers table for event ID 2:\n\n";

$papers = \App\Models\EventPaper::where('event_id', 2)->get();

if ($papers->isEmpty()) {
    echo "No papers found in event_papers table.\n";
} else {
    foreach ($papers as $paper) {
        echo "--- Paper ID: {$paper->id} ---\n";
        echo "User: {$paper->user->name}\n";
        echo "Title: {$paper->title}\n";
        echo "Abstract: " . substr($paper->abstract ?? '', 0, 100) . "\n";
        echo "Status: {$paper->status}\n";
        
        // Get all columns
        echo "All data: " . json_encode($paper->toArray(), JSON_PRETTY_PRINT) . "\n\n";
    }
}
