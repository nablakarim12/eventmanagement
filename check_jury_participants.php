<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;
use App\Models\EventRegistration;

$event = Event::find(24);

$participants = EventRegistration::where('event_id', 24)
    ->where('approval_status', 'approved')
    ->whereNotNull('checked_in_at')
    ->where('role', 'participant')
    ->with('user')
    ->get();

$jury = EventRegistration::where('event_id', 24)
    ->where('approval_status', 'approved')
    ->whereNotNull('checked_in_at')
    ->whereIn('role', ['jury', 'both'])
    ->with('user')
    ->get();

echo "=== Smart City Idea Challenge - Jury Assignment Analysis ===\n\n";
echo "Event: " . $event->title . "\n";
echo "Event ID: " . $event->id . "\n\n";

echo "📊 COUNTS:\n";
echo "==========\n";
echo "Participants (role='participant'): " . $participants->count() . "\n";
echo "Jury Members (role='jury' or 'both'): " . $jury->count() . "\n\n";

echo "👥 PARTICIPANTS:\n";
echo "================\n";
foreach($participants as $p) {
    echo "  - " . $p->user->name . "\n";
    echo "    Email: " . $p->user->email . "\n";
    echo "    Registration ID: " . $p->id . "\n";
    echo "    Registration Code: " . $p->registration_code . "\n";
    echo "    Role: " . $p->role . "\n";
    echo "    Approval: " . $p->approval_status . "\n";
    echo "    Checked In: " . $p->checked_in_at . "\n";
    echo "    --------------------------------------------------\n";
}

echo "\n⚖️  JURY MEMBERS:\n";
echo "================\n";
foreach($jury as $j) {
    echo "  - " . $j->user->name . "\n";
    echo "    Email: " . $j->user->email . "\n";
    echo "    Registration ID: " . $j->id . "\n";
    echo "    Registration Code: " . $j->registration_code . "\n";
    echo "    Role: " . $j->role . "\n";
    echo "    Approval: " . $j->approval_status . "\n";
    echo "    Checked In: " . $j->checked_in_at . "\n";
    echo "    --------------------------------------------------\n";
}

echo "\n📋 SUMMARY:\n";
echo "===========\n";
echo "✅ " . $participants->count() . " participants ready for evaluation\n";
echo "✅ " . $jury->count() . " jury members available to judge\n";
echo "\n";
