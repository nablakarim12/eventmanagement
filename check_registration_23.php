<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$reg = App\Models\EventRegistration::with(['user', 'event'])->find(23);

if ($reg) {
    echo "Registration ID: 23\n";
    echo "User: " . ($reg->user ? $reg->user->name : 'N/A') . "\n";
    echo "Receipt Path: " . ($reg->payment_receipt_path ?? 'NULL') . "\n";
    echo "Event ID: " . $reg->event_id . "\n";
    echo "Organizer ID: " . ($reg->event ? $reg->event->organizer_id : 'N/A') . "\n";
    
    // Try to fetch the file
    if ($reg->payment_receipt_path) {
        echo "\nTrying to fetch file...\n";
        $content = @file_get_contents($reg->payment_receipt_path);
        if ($content !== false) {
            echo "File fetched successfully! Size: " . strlen($content) . " bytes\n";
        } else {
            echo "ERROR: Could not fetch file from Cloudinary\n";
            echo "Error: " . error_get_last()['message'] . "\n";
        }
    } else {
        echo "\nNo receipt path found\n";
    }
} else {
    echo "Registration 23 not found in database\n";
}
