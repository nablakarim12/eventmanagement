<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\EventQrCode;
use App\Models\Event;

echo "=== Check-in vs Check-out QR Codes Explanation ===\n\n";

// Get an event to show the QR codes
$event = Event::with('qrCodes')->first();

if ($event) {
    echo "📅 Event: " . $event->title . "\n";
    echo "📍 Location: " . $event->venue_name . "\n";
    echo "⏰ Date: " . $event->start_date->format('M d, Y h:i A') . "\n\n";
    
    echo "🎫 QR CODES GENERATED:\n";
    echo "═══════════════════════════════════════\n\n";
    
    foreach ($event->qrCodes as $qrCode) {
        echo "🔸 TYPE: " . strtoupper($qrCode->type) . "\n";
        echo "   Purpose: " . ($qrCode->type === 'check_in' ? 'Record ARRIVAL at event' : 'Record DEPARTURE from event') . "\n";
        echo "   Location: " . ($qrCode->type === 'check_in' ? 'Place at ENTRANCE/Registration' : 'Place at EXIT/Completion') . "\n";
        echo "   QR Code: " . substr($qrCode->qr_code, 0, 8) . "...\n";
        echo "   Scan URL: http://localhost:8000/scan/" . $qrCode->qr_code . "\n";
        echo "   Valid: " . $qrCode->valid_from->format('M d h:i A') . " - " . $qrCode->valid_until->format('M d h:i A') . "\n\n";
    }
    
    echo "📋 ATTENDANCE TRACKING:\n";
    echo "═══════════════════════════════════════\n";
    echo "When participant scans CHECK-IN QR:\n";
    echo "✅ Creates attendance record with arrival time\n";
    echo "✅ Status: 'present' \n";
    echo "✅ check_in_time: Current timestamp\n";
    echo "✅ check_out_time: NULL (not left yet)\n\n";
    
    echo "When participant scans CHECK-OUT QR:\n"; 
    echo "✅ Updates existing attendance record\n";
    echo "✅ Status: 'completed'\n";
    echo "✅ check_out_time: Current timestamp\n";
    echo "✅ Duration calculated automatically\n\n";
    
    echo "📊 ATTENDANCE STATES:\n";
    echo "═══════════════════════════════════════\n";
    echo "🟡 REGISTERED: User registered but not attended yet\n";
    echo "🟢 PRESENT: User checked in (scanned check-in QR)\n";
    echo "🔵 COMPLETED: User checked in AND out (scanned both QRs)\n";
    echo "🔴 ABSENT: User registered but never checked in\n\n";
    
} else {
    echo "❌ No events found. Create an event first to see QR codes.\n";
}

echo "=== Explanation Complete ===\n";