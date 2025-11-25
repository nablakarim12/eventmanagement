<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EventRegistration;

// Find a pending registration
$registration = EventRegistration::where('approved_at', null)
    ->where('rejected_at', null)
    ->with('event', 'user')
    ->first();

if (!$registration) {
    echo "No pending registrations found. Looking for any registration...\n";
    $registration = EventRegistration::with('event', 'user')->first();
}

if (!$registration) {
    echo "No registrations found in database!\n";
    exit;
}

echo "Testing QR Code Generation\n";
echo "==========================\n\n";

echo "Registration ID: {$registration->id}\n";
echo "User: {$registration->user->name}\n";
echo "Event: {$registration->event->event_name}\n";
echo "Current Status:\n";
echo "  - Approved: " . ($registration->approved_at ? 'Yes' : 'No') . "\n";
echo "  - QR Code: " . ($registration->qr_code ?: 'Not generated') . "\n";
echo "  - QR Image: " . ($registration->qr_image_path ?: 'Not generated') . "\n\n";

// Approve the registration (this should trigger Observer)
if (!$registration->approved_at) {
    echo "Approving registration (this will trigger QR generation)...\n";
    $registration->approved_at = now();
    $registration->approval_status = 'approved';
    $registration->save();
    
    // Reload the registration
    $registration->refresh();
    
    echo "\nAfter Approval:\n";
    echo "  - Approved: " . ($registration->approved_at ? 'Yes' : 'No') . "\n";
    echo "  - QR Code: " . ($registration->qr_code ?: 'Not generated') . "\n";
    echo "  - QR Image: " . ($registration->qr_image_path ?: 'Not generated') . "\n";
    
    if ($registration->qr_code && $registration->qr_image_path) {
        echo "\n✓ SUCCESS! QR code generated automatically!\n";
        echo "QR Code: {$registration->qr_code}\n";
        echo "Image Path: {$registration->qr_image_path}\n";
        echo "Full Path: " . storage_path('app/public/' . $registration->qr_image_path) . "\n";
        
        // Check if file exists
        if (file_exists(storage_path('app/public/' . $registration->qr_image_path))) {
            echo "\n✓ QR image file exists!\n";
        } else {
            echo "\n✗ QR image file NOT found!\n";
        }
    } else {
        echo "\n✗ FAILED! QR code was not generated.\n";
        echo "Check storage/logs/laravel.log for errors.\n";
    }
} else {
    echo "Registration already approved. QR should already be generated.\n";
}
