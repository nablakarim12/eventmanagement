<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EventRegistration;

echo "=== Complete Registration Analysis for Event 24 ===\n\n";

// All participant registrations (approved, regardless of check-in)
$allApprovedParticipants = EventRegistration::where('event_id', 24)
    ->where('role', 'participant')
    ->where('approval_status', 'approved')
    ->with('user')
    ->get();

echo "📊 ALL APPROVED PARTICIPANTS: " . $allApprovedParticipants->count() . "\n";
echo str_repeat("=", 60) . "\n";

foreach($allApprovedParticipants as $p) {
    $checkedIn = $p->checked_in_at ? '✅ Checked In' : '❌ Not Checked In';
    echo "  - " . $p->user->name . " (" . $p->registration_code . ")\n";
    echo "    " . $checkedIn;
    if ($p->checked_in_at) {
        echo " at " . $p->checked_in_at;
    }
    echo "\n    --------------------------------------------------\n";
}

echo "\n📋 BREAKDOWN:\n";
$checkedInCount = $allApprovedParticipants->whereNotNull('checked_in_at')->count();
$notCheckedInCount = $allApprovedParticipants->whereNull('checked_in_at')->count();

echo "  ✅ Checked In: " . $checkedInCount . "\n";
echo "  ❌ Not Checked In: " . $notCheckedInCount . "\n";
echo "  📊 Total Approved: " . $allApprovedParticipants->count() . "\n";
