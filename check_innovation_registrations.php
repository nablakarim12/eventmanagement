<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EventRegistration;
use App\Models\JuryMapping;

$eventId = 2; // Innovation event

echo "=== Event ID $eventId - Registration Data ===\n\n";

$registrations = EventRegistration::where('event_id', $eventId)
    ->with(['user', 'juryMappingsAsReviewer'])
    ->get();

foreach ($registrations as $reg) {
    echo "User: {$reg->user->name} (ID: {$reg->user_id})\n";
    echo "  - Checked In: " . ($reg->checked_in_at ? "YES ({$reg->checked_in_at})" : "NO") . "\n";
    echo "  - Checked Out: " . ($reg->checked_out_at ? "YES ({$reg->checked_out_at})" : "NO") . "\n";
    echo "  - Presentation Status: " . ($reg->presentation_status ?? 'NULL') . "\n";
    echo "  - Is Jury: " . ($reg->juryMappingsAsReviewer->count() > 0 ? "YES ({$reg->juryMappingsAsReviewer->count()} mappings)" : "NO") . "\n";
    echo "\n";
}

echo "\n=== Summary ===\n";
echo "Total Registrations: " . $registrations->count() . "\n";
echo "Checked In: " . $registrations->whereNotNull('checked_in_at')->count() . "\n";
echo "Jury Members: " . $registrations->filter(fn($r) => $r->juryMappingsAsReviewer->count() > 0)->count() . "\n";
echo "Participants (non-jury): " . $registrations->filter(fn($r) => $r->juryMappingsAsReviewer->count() === 0)->count() . "\n";
