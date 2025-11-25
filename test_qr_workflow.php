<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Event;
use App\Models\EventQrCode;

echo "=== Testing QR Scanning Workflow ===\n";

// Get a QR code that exists
$qrCode = EventQrCode::with('event')->first();

if (!$qrCode) {
    echo "No QR codes found. Generating one...\n";
    $event = Event::first();
    if ($event) {
        $qrCode = EventQrCode::generateForEvent($event);
        echo "Generated new QR code: " . $qrCode->qr_code . "\n";
    } else {
        echo "No events found to generate QR code.\n";
        exit;
    }
}

echo "Testing QR Code: " . $qrCode->qr_code . "\n";
echo "Event: " . $qrCode->event->title . "\n";
echo "Scan URL: http://localhost:8000/scan/" . $qrCode->qr_code . "\n";

// Test QR validation
echo "\n=== Testing QR Validation ===\n";
$isValid = $qrCode->isValid();
echo "QR Code is valid: " . ($isValid ? 'YES' : 'NO') . "\n";

if ($isValid) {
    echo "✅ QR Code is ready for scanning!\n";
    echo "✅ Event: " . $qrCode->event->title . "\n";
    echo "✅ Type: " . $qrCode->type . "\n";
    echo "✅ Valid until: " . $qrCode->valid_until . "\n";
} else {
    echo "❌ QR Code is not valid (check dates or active status)\n";
}

echo "\n=== Test Complete ===\n";