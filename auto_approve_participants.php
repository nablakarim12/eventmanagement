<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EventRegistration;

echo "=== Auto-Approve Checked-In Participants ===\n\n";

// Get all checked-in participants with pending approval
$pendingParticipants = EventRegistration::where('event_id', 24)
    ->where('role', 'participant')
    ->where('approval_status', 'pending')
    ->whereNotNull('checked_in_at')
    ->with('user')
    ->get();

echo "Found " . $pendingParticipants->count() . " checked-in participants with pending approval\n\n";

if ($pendingParticipants->count() === 0) {
    echo "✅ No participants need approval!\n";
    exit;
}

echo "Participants to be approved:\n";
foreach ($pendingParticipants as $p) {
    echo "  - " . $p->user->name . " (" . $p->registration_code . ")\n";
}

echo "\nProceed with auto-approval? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if ($line !== 'yes') {
    echo "Cancelled.\n";
    exit;
}

echo "\nApproving...\n";
$count = 0;

foreach ($pendingParticipants as $p) {
    $p->approval_status = 'approved';
    $p->save();
    echo "  ✅ Approved: " . $p->user->name . "\n";
    $count++;
}

echo "\n✅ Successfully approved " . $count . " participants!\n";
echo "\n📊 Updated Status:\n";

$approved = EventRegistration::where('event_id', 24)
    ->where('role', 'participant')
    ->where('approval_status', 'approved')
    ->whereNotNull('checked_in_at')
    ->count();

$jury = EventRegistration::where('event_id', 24)
    ->whereIn('role', ['jury', 'both'])
    ->where('approval_status', 'approved')
    ->whereNotNull('checked_in_at')
    ->count();

echo "  ✅ " . $approved . " approved participants (checked in)\n";
echo "  ✅ " . $jury . " approved jury members (checked in)\n";
echo "\n🎯 Ready for jury assignment!\n";
