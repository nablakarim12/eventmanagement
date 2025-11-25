<?php
// Simple test script to check QR generation
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== QR Generation Test ===\n";

// Check existing organizers
$organizerCount = App\Models\EventOrganizer::count();
echo "Organizers in database: $organizerCount\n";

if ($organizerCount > 0) {
    $organizer = App\Models\EventOrganizer::first();
    echo "Test organizer: " . $organizer->name . "\n";
    
    // Check existing events
    $eventCount = App\Models\Event::where('organizer_id', $organizer->id)->count();
    echo "Events for organizer: $eventCount\n";
    
    if ($eventCount > 0) {
        $event = App\Models\Event::where('organizer_id', $organizer->id)->first();
        echo "Test event: " . $event->title . "\n";
        
        // Check QR codes for this event
        $qrCount = App\Models\EventQrCode::where('event_id', $event->id)->count();
        echo "QR codes for event: $qrCount\n";
        
        if ($qrCount > 0) {
            $qrCodes = App\Models\EventQrCode::where('event_id', $event->id)->get();
            foreach ($qrCodes as $qr) {
                echo "  - QR Code: " . $qr->type . " (ID: " . substr($qr->qr_code, 0, 8) . "...)\n";
                echo "    URL: " . url('/scan/' . $qr->qr_code) . "\n";
            }
        } else {
            echo "No QR codes found. Testing auto-generation...\n";
            // This should trigger auto QR generation
            App\Models\EventQrCode::generateForEvent($event, 'check_in');
            App\Models\EventQrCode::generateForEvent($event, 'check_out');
            echo "QR codes generated!\n";
        }
    } else {
        echo "Creating test event...\n";
        
        // Get first category
        $category = App\Models\EventCategory::first();
        if (!$category) {
            $category = App\Models\EventCategory::create([
                'name' => 'Conference',
                'description' => 'Academic and Innovation Conferences',
                'slug' => 'conference'
            ]);
        }
        
        $event = App\Models\Event::create([
            'organizer_id' => $organizer->id,
            'category_id' => $category->id,
            'title' => 'Test Innovation Conference',
            'description' => 'A test event for QR code functionality',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(7)->addHours(8),
            'location' => 'Test Venue',
            'max_participants' => 100,
            'registration_fee' => 0,
            'status' => 'published'
        ]);
        echo "Test event created: " . $event->title . "\n";
        
        // Check if QR codes were auto-generated
        $qrCount = App\Models\EventQrCode::where('event_id', $event->id)->count();
        echo "Auto-generated QR codes: $qrCount\n";
        
        if ($qrCount > 0) {
            $qrCodes = App\Models\EventQrCode::where('event_id', $event->id)->get();
            foreach ($qrCodes as $qr) {
                echo "  - QR Code: " . $qr->type . " (ID: " . substr($qr->qr_code, 0, 8) . "...)\n";
                echo "    Scan URL: " . url('/scan/' . $qr->qr_code) . "\n";
            }
        }
    }
} else {
    echo "No organizers found. Please create an organizer first.\n";
}

echo "\n=== Test Complete ===\n";
?>