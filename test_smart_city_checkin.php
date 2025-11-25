<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;
use App\Models\EventRegistration;

// Find Smart City Idea Challenge event
$event = Event::where('title', 'LIKE', '%Smart City%')->first();

if (!$event) {
    echo "Smart City event not found!\n";
    echo "Searching for any event with 'Smart' or 'City'...\n";
    $event = Event::where('title', 'LIKE', '%Smart%')
        ->orWhere('title', 'LIKE', '%City%')
        ->first();
}

if (!$event) {
    echo "No Smart City event found. Available events:\n";
    Event::all()->each(function($e) {
        echo "  - {$e->title}\n";
    });
    exit;
}

echo "Found Event: {$event->title}\n";
echo "Event Date: {$event->start_date}\n";
echo "Event ID: {$event->id}\n\n";

// Find registrations for this event
$registrations = EventRegistration::where('event_id', $event->id)
    ->with('user')
    ->get();

echo "Total Registrations: " . $registrations->count() . "\n\n";

if ($registrations->isEmpty()) {
    echo "No registrations found for this event!\n";
    exit;
}

echo "Registration Details:\n";
echo str_repeat("=", 80) . "\n";

foreach ($registrations as $reg) {
    echo "ID: {$reg->id}\n";
    echo "User: " . ($reg->user ? $reg->user->name : 'N/A') . "\n";
    echo "Code: {$reg->registration_code}\n";
    echo "Role: {$reg->role}\n";
    echo "Approved: " . ($reg->approved_at ? 'YES ✓' : 'NO ✗') . "\n";
    echo "QR Code: " . ($reg->qr_code ?: 'Not generated') . "\n";
    
    if ($reg->qr_code) {
        echo "Check-In URL: http://localhost/check-in/{$reg->qr_code}\n";
        echo "QR Image: " . ($reg->qr_image_path ?: 'Not found') . "\n";
        
        if ($reg->qr_image_path) {
            $imagePath = storage_path('app/public/' . $reg->qr_image_path);
            echo "Image Exists: " . (file_exists($imagePath) ? 'YES ✓' : 'NO ✗') . "\n";
        }
    }
    
    echo "Checked In: " . ($reg->checked_in_at ? 'YES ✓ at ' . $reg->checked_in_at : 'NO ✗') . "\n";
    echo str_repeat("-", 80) . "\n";
}

// Find approved registrations with QR codes
$approvedWithQR = $registrations->filter(function($reg) {
    return $reg->approved_at && $reg->qr_code;
});

if ($approvedWithQR->isEmpty()) {
    echo "\n⚠️  No approved registrations with QR codes found.\n";
    echo "Let me approve a registration and generate QR code...\n\n";
    
    // Find a pending registration
    $pending = $registrations->where('approved_at', null)->first();
    
    if ($pending) {
        echo "Approving registration ID: {$pending->id}\n";
        $pending->approved_at = now();
        $pending->approval_status = 'approved';
        $pending->save();
        
        // Reload to see QR code
        $pending->refresh();
        
        echo "✓ Approved!\n";
        echo "QR Code: {$pending->qr_code}\n";
        echo "Check-In URL: http://localhost/check-in/{$pending->qr_code}\n";
    } else {
        echo "No pending registrations to approve.\n";
    }
} else {
    echo "\n✓ Ready for Testing!\n";
    echo "Approved registrations with QR codes: " . $approvedWithQR->count() . "\n\n";
    
    echo "📱 TEST THESE URLS:\n";
    echo str_repeat("=", 80) . "\n";
    
    foreach ($approvedWithQR as $reg) {
        echo "User: " . ($reg->user ? $reg->user->name : 'N/A') . " ({$reg->role})\n";
        echo "URL: http://localhost/check-in/{$reg->qr_code}\n";
        echo "Status: " . ($reg->checked_in_at ? '✓ Already checked in' : '⭕ Ready for check-in') . "\n";
        echo "\n";
    }
}
