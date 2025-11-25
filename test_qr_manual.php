<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing QR Auto-Generation ===\n";

// Get first event
$event = App\Models\Event::first();
if ($event) {
    echo "Testing with event: " . $event->title . "\n";
    
    // Check existing QR codes
    $existingQRs = App\Models\EventQrCode::where('event_id', $event->id)->count();
    echo "Existing QR codes: $existingQRs\n";
    
    if ($existingQRs == 0) {
        echo "Generating QR codes manually...\n";
        
        // Manual generation using the model method
        $checkInQR = App\Models\EventQrCode::generateForEvent($event, 'check_in');
        $checkOutQR = App\Models\EventQrCode::generateForEvent($event, 'check_out');
        
        echo "Generated QR codes:\n";
        echo "  Check-in: " . substr($checkInQR->qr_code, 0, 8) . "...\n";
        echo "  Check-out: " . substr($checkOutQR->qr_code, 0, 8) . "...\n";
        
        // Test URLs
        echo "\nScan URLs:\n";
        echo "  Check-in: " . url('/scan/' . $checkInQR->qr_code) . "\n";
        echo "  Check-out: " . url('/scan/' . $checkOutQR->qr_code) . "\n";
        
        // Test if QR code generation works
        echo "\nTesting QR image generation...\n";
        try {
            $controller = new App\Http\Controllers\Organizer\QrCodeController();
            $reflection = new ReflectionClass($controller);
            $method = $reflection->getMethod('generateQrCodeImage');
            $method->setAccessible(true);
            
            $result = $method->invokeArgs($controller, [$checkInQR]);
            echo "QR image generated at: $result\n";
        } catch (Exception $e) {
            echo "QR image generation failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "QR codes already exist for this event.\n";
        $qrs = App\Models\EventQrCode::where('event_id', $event->id)->get();
        foreach ($qrs as $qr) {
            echo "  " . $qr->type . ": " . url('/scan/' . $qr->qr_code) . "\n";
        }
    }
} else {
    echo "No events found!\n";
}

echo "\n=== Test Complete ===\n";
?>