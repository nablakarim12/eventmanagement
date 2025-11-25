<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Event;
use App\Models\EventQrCode;

echo "=== QR Code Debug Information ===\n";

// Get a QR code that exists
$qrCode = EventQrCode::with('event')->first();

if (!$qrCode) {
    echo "No QR codes found.\n";
    exit;
}

echo "QR Code: " . $qrCode->qr_code . "\n";
echo "Event: " . $qrCode->event->title . "\n";
echo "Type: " . $qrCode->type . "\n";
echo "Is Active: " . ($qrCode->is_active ? 'YES' : 'NO') . "\n";
echo "Valid From: " . ($qrCode->valid_from ?: 'NULL') . "\n";
echo "Valid Until: " . ($qrCode->valid_until ?: 'NULL') . "\n";
echo "Current Time: " . now() . "\n";

// Check each validation condition
echo "\n=== Validation Details ===\n";
echo "is_active: " . ($qrCode->is_active ? 'PASS' : 'FAIL') . "\n";

if ($qrCode->valid_from) {
    $fromValid = $qrCode->valid_from <= now();
    echo "valid_from check: " . ($fromValid ? 'PASS' : 'FAIL') . " (valid_from: {$qrCode->valid_from})\n";
} else {
    echo "valid_from check: PASS (no restriction)\n";
}

if ($qrCode->valid_until) {
    $untilValid = $qrCode->valid_until >= now();
    echo "valid_until check: " . ($untilValid ? 'PASS' : 'FAIL') . " (valid_until: {$qrCode->valid_until})\n";
} else {
    echo "valid_until check: PASS (no restriction)\n";
}

echo "\n=== Event Details ===\n";
echo "Event Start: " . $qrCode->event->start_date . "\n";
echo "Event End: " . ($qrCode->event->end_date ?: 'NULL') . "\n";

echo "\n=== Debug Complete ===\n";