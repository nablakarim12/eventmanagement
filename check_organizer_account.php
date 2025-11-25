<?php

require_once 'vendor/autoload.php';

use App\Models\EventOrganizer;

// Laravel Bootstrap
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== SEARCHING FOR EXISTING ORGANIZER ACCOUNT ===\n\n";

// Search for the specific organizer account
$organizer = EventOrganizer::where('org_email', 'd20221101811@siswa.upsi.edu.my')->first();

if ($organizer) {
    echo "✅ ACCOUNT FOUND!\n";
    echo "📧 Email: {$organizer->org_email}\n";
    echo "🏢 Organization: {$organizer->org_name}\n";
    echo "📞 Phone: " . ($organizer->phone ?? 'Not set') . "\n";
    echo "🌐 Website: " . ($organizer->website ?? 'Not set') . "\n";
    echo "📍 Address: " . ($organizer->address ?? 'Not set') . "\n";
    echo "🏙️ City: " . ($organizer->city ?? 'Not set') . "\n";
    echo "📋 Status: {$organizer->status}\n";
    echo "📅 Created: {$organizer->created_at}\n";
    echo "🆔 ID: {$organizer->id}\n\n";
    
    // Check if this organizer has any existing events
    $existingEvents = \App\Models\Event::where('organizer_id', $organizer->id)->count();
    echo "📊 Existing Events: {$existingEvents}\n\n";
    
    echo "✅ This account can be used for sample events!\n";
} else {
    echo "❌ ACCOUNT NOT FOUND!\n";
    echo "The email 'd20221101811@siswa.upsi.edu.my' was not found in the event_organizers table.\n\n";
    
    echo "Let me show you all existing organizer accounts:\n";
    echo "═══════════════════════════════════════════════\n";
    
    $allOrganizers = EventOrganizer::all();
    if ($allOrganizers->count() > 0) {
        foreach ($allOrganizers as $org) {
            echo "📧 {$org->org_email}\n";
            echo "🏢 {$org->org_name}\n";
            echo "📋 Status: {$org->status}\n";
            echo "🆔 ID: {$org->id}\n";
            echo "---\n";
        }
    } else {
        echo "No organizer accounts found in the database.\n";
    }
}

echo "\n=== SEARCH COMPLETE ===\n";