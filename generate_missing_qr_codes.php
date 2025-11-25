<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EventRegistration;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

echo "Generating QR codes for approved registrations without QR codes...\n\n";

// Find approved registrations without QR codes
$registrations = EventRegistration::whereNotNull('approved_at')
    ->whereNull('qr_code')
    ->with('user', 'event')
    ->get();

echo "Found {$registrations->count()} approved registrations without QR codes.\n\n";

foreach ($registrations as $registration) {
    echo "Processing Registration ID: {$registration->id}\n";
    echo "User: " . ($registration->user ? $registration->user->name : 'N/A') . "\n";
    echo "Event: " . ($registration->event ? $registration->event->title : 'N/A') . "\n";
    
    // Generate unique QR code identifier
    $qrCode = 'REG-' . strtoupper(Str::random(12));
    
    // Create the QR code URL
    $checkInUrl = route('qr.scan.registration', ['qrCode' => $qrCode]);
    
    echo "QR Code: {$qrCode}\n";
    echo "URL: {$checkInUrl}\n";
    
    // Generate QR code image
    $qrCodeObj = new QrCode($checkInUrl);
    $writer = new PngWriter();
    $result = $writer->write($qrCodeObj);
    
    // Save QR code image
    $filename = 'qr-codes/' . $qrCode . '.png';
    Storage::disk('public')->put($filename, $result->getString());
    
    // Update registration
    $registration->qr_code = $qrCode;
    $registration->qr_image_path = $filename;
    $registration->save();
    
    echo "✓ QR code generated and saved!\n";
    echo "Image: storage/app/public/{$filename}\n";
    echo str_repeat("-", 80) . "\n";
}

echo "\n✅ Done! All approved registrations now have QR codes.\n";
