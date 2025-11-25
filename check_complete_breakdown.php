<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EventRegistration;

echo "=== Complete Breakdown of All Registrations for Event 24 ===\n\n";

// Get ALL registrations
$all = EventRegistration::where('event_id', 24)
    ->with('user')
    ->orderBy('role')
    ->orderBy('approval_status')
    ->get();

echo "TOTAL REGISTRATIONS: " . $all->count() . "\n\n";

// Group by role
$byRole = $all->groupBy('role');

foreach(['participant', 'jury', 'both'] as $role) {
    if (!isset($byRole[$role])) continue;
    
    $registrations = $byRole[$role];
    echo "📋 ROLE: " . strtoupper($role) . " (" . $registrations->count() . ")\n";
    echo str_repeat("=", 70) . "\n";
    
    foreach($registrations as $r) {
        $checkedIn = $r->checked_in_at ? '✅' : '❌';
        $approved = $r->approval_status === 'approved' ? '✅' : '⏳';
        
        echo "  " . $checkedIn . " " . $approved . " " . $r->user->name . " (" . $r->registration_code . ")\n";
        echo "      Approval: " . $r->approval_status . "\n";
        echo "      Checked In: " . ($r->checked_in_at ?? 'No') . "\n";
        echo "      --------------------------------------------------\n";
    }
    echo "\n";
}

// Summary
echo "📊 SUMMARY FOR JURY ASSIGNMENT PAGE:\n";
echo str_repeat("=", 70) . "\n";

$participants = EventRegistration::where('event_id', 24)
    ->where('approval_status', 'approved')
    ->whereNotNull('checked_in_at')
    ->where('role', 'participant')
    ->count();

$jury = EventRegistration::where('event_id', 24)
    ->where('approval_status', 'approved')
    ->whereNotNull('checked_in_at')
    ->whereIn('role', ['jury', 'both'])
    ->count();

echo "Should show:\n";
echo "  👥 Participants: " . $participants . " (approved + checked-in + role='participant')\n";
echo "  ⚖️  Jury: " . $jury . " (approved + checked-in + role='jury' or 'both')\n";
