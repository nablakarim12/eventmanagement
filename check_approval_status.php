<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EventRegistration;

$all = EventRegistration::where('event_id', 24)
    ->whereNotNull('checked_in_at')
    ->with('user')
    ->orderBy('role')
    ->orderBy('approval_status')
    ->get();

echo "=== All Checked-In Registrations for Smart City ===\n\n";
echo "Total Checked-In: " . $all->count() . "\n\n";

$grouped = $all->groupBy('approval_status');

foreach($grouped as $status => $registrations) {
    echo "📋 APPROVAL STATUS: " . strtoupper($status) . " (" . $registrations->count() . ")\n";
    echo str_repeat("=", 60) . "\n";
    
    foreach($registrations as $r) {
        echo "  - " . $r->user->name . " (" . $r->user->email . ")\n";
        echo "    Role: " . $r->role . "\n";
        echo "    Registration Code: " . $r->registration_code . "\n";
        echo "    Checked In: " . $r->checked_in_at . "\n";
        echo "    --------------------------------------------------\n";
    }
    echo "\n";
}

echo "\n💡 ISSUE FOUND:\n";
echo "==============\n";
$pendingParticipants = $all->where('role', 'participant')->where('approval_status', 'pending')->count();
echo "❌ " . $pendingParticipants . " participants with 'pending' approval status\n";
echo "✅ They are checked-in but NOT approved yet\n";
echo "⚠️  Jury assignment requires 'approved' status\n";
echo "\n";
echo "👉 ACTION NEEDED: Approve participant registrations first!\n";
