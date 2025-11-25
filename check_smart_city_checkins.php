<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Event;
use App\Models\EventRegistration;

echo "=== Smart City Idea Challenge Check-In Status ===\n\n";

// Find Smart City event
$event = Event::where('title', 'LIKE', '%Smart City%')->first();

if (!$event) {
    echo "❌ Smart City event not found\n";
    exit;
}

echo "Event: {$event->title}\n";
echo "Event ID: {$event->id}\n";
echo "Start Date: {$event->start_date}\n\n";

// Get all registrations
$registrations = EventRegistration::where('event_id', $event->id)
    ->with('user')
    ->get();

echo "📊 Summary:\n";
echo "==========\n";
echo "Total Registrations: " . $registrations->count() . "\n";
echo "✅ Checked In: " . $registrations->whereNotNull('checked_in_at')->count() . "\n";
echo "❌ Not Checked In: " . $registrations->whereNull('checked_in_at')->count() . "\n\n";

echo "📋 Detailed Registration List:\n";
echo "============================\n\n";

foreach ($registrations as $reg) {
    echo "👤 User: {$reg->user->name}\n";
    echo "   Email: {$reg->user->email}\n";
    echo "   Registration Code: {$reg->registration_code}\n";
    $role = $reg->registration_type ? $reg->registration_type : ($reg->role ? $reg->role : 'N/A');
    echo "   Role: {$role}\n";
    echo "   Approval Status: {$reg->approval_status}\n";
    
    if ($reg->checked_in_at) {
        $checkinTime = is_string($reg->checked_in_at) ? $reg->checked_in_at : $reg->checked_in_at->format('Y-m-d H:i:s');
        echo "   ✅ Checked In: {$checkinTime}\n";
        echo "   ✅ Check-in method: " . ($reg->qr_code ? 'QR Code or Manual' : 'Manual') . "\n";
    } else {
        echo "   ❌ Check-In Status: Not checked in yet\n";
    }
    
    echo "   " . str_repeat("-", 50) . "\n\n";
}

// Show who can be assigned as jury now
echo "\n🎯 Available for Jury Assignment:\n";
echo "================================\n";
$availableJury = $registrations->filter(function($reg) {
    $role = $reg->registration_type ? $reg->registration_type : ($reg->role ? $reg->role : '');
    return in_array($role, ['jury', 'both']) 
           && $reg->approval_status === 'approved' 
           && $reg->checked_in_at !== null;
});

if ($availableJury->count() > 0) {
    foreach ($availableJury as $jury) {
        $juryRole = $jury->registration_type ? $jury->registration_type : ($jury->role ? $jury->role : 'N/A');
        echo "✓ {$jury->user->name} ({$juryRole})\n";
    }
} else {
    echo "⚠️ No jury members available yet (need to check in first)\n";
}
