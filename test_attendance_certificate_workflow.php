<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== QR ATTENDANCE TO CERTIFICATE WORKFLOW DEMO ===\n\n";

// Get an event
$event = Event::first();

if (!$event) {
    echo "❌ No events found. Create an event first.\n";
    exit;
}

echo "📅 Event: " . $event->title . "\n";
echo "📍 Location: " . $event->venue_name . "\n";
echo "⏰ Date: " . $event->start_date->format('M d, Y') . "\n\n";

// Simulate some attendance records for demo
echo "🎭 SIMULATING ATTENDANCE RECORDS...\n";
echo "═══════════════════════════════════════\n\n";

// Create some test users and attendance
$participantUser = User::where('email', 'participant@test.com')->first();
$juryUser = User::where('email', 'jury@test.com')->first();

if (!$participantUser) {
    $participantUser = User::create([
        'name' => 'John Participant',
        'email' => 'participant@test.com',
        'password' => bcrypt('password'),
    ]);
    echo "✅ Created test participant: " . $participantUser->name . "\n";
}

if (!$juryUser) {
    $juryUser = User::create([
        'name' => 'Dr. Jane Jury',
        'email' => 'jury@test.com', 
        'password' => bcrypt('password'),
    ]);
    echo "✅ Created test jury: " . $juryUser->name . "\n";
}

// Create registrations
$participantRegistration = EventRegistration::firstOrCreate([
    'event_id' => $event->id,
    'user_id' => $participantUser->id
], [
    'registration_code' => 'PART-' . time(),
    'status' => 'confirmed'
]);

$juryRegistration = EventRegistration::firstOrCreate([
    'event_id' => $event->id,
    'user_id' => $juryUser->id
], [
    'registration_code' => 'JURY-' . time(),
    'status' => 'confirmed'
]);

// Create attendance records (simulating QR scan results)
$participantAttendance = EventAttendance::firstOrCreate([
    'event_id' => $event->id,
    'user_id' => $participantUser->id
], [
    'registration_id' => $participantRegistration->id,
    'check_in_time' => now()->subHours(4), // Checked in 4 hours ago
    'check_out_time' => now()->subHour(1), // Checked out 1 hour ago
    'check_in_method' => 'qr_scan',
    'check_out_method' => 'qr_scan',
    'status' => 'present'
]);

$juryAttendance = EventAttendance::firstOrCreate([
    'event_id' => $event->id,
    'user_id' => $juryUser->id
], [
    'registration_id' => $juryRegistration->id,
    'check_in_time' => now()->subHours(5), // Checked in 5 hours ago  
    'check_out_time' => now()->subMinutes(30), // Checked out 30 mins ago
    'check_in_method' => 'qr_scan',
    'check_out_method' => 'qr_scan',
    'status' => 'present'
]);

echo "\n📊 ATTENDANCE SUMMARY:\n";
echo "═══════════════════════════════════════\n";

$attendanceStats = [
    'total_registered' => EventRegistration::where('event_id', $event->id)->count(),
    'total_checked_in' => EventAttendance::where('event_id', $event->id)->whereNotNull('check_in_time')->count(),
    'total_completed' => EventAttendance::where('event_id', $event->id)->whereNotNull('check_out_time')->count(),
    'participants_completed' => EventAttendance::where('event_id', $event->id)->whereNotNull('check_out_time')->whereHas('registration', function($q) { $q->where('registration_code', 'LIKE', 'PART-%'); })->count(),
    'jury_completed' => EventAttendance::where('event_id', $event->id)->whereNotNull('check_out_time')->whereHas('registration', function($q) { $q->where('registration_code', 'LIKE', 'JURY-%'); })->count(),
];

$attendanceStats['attendance_rate'] = $attendanceStats['total_registered'] > 0 
    ? round(($attendanceStats['total_checked_in'] / $attendanceStats['total_registered']) * 100, 2) 
    : 0;

echo "📝 Total Registered: " . $attendanceStats['total_registered'] . "\n";
echo "✅ Total Checked In: " . $attendanceStats['total_checked_in'] . "\n";
echo "🎯 Completed Attendance: " . $attendanceStats['total_completed'] . "\n";
echo "👨‍🎓 Participants Completed: " . $attendanceStats['participants_completed'] . "\n";
echo "⚖️ Jury Completed: " . $attendanceStats['jury_completed'] . "\n";
echo "📈 Attendance Rate: " . $attendanceStats['attendance_rate'] . "%\n\n";

echo "🎯 ATTENDANCE DETAILS:\n";
echo "═══════════════════════════════════════\n";

$completedAttendances = EventAttendance::where('event_id', $event->id)
    ->whereNotNull('check_out_time') // Has completed full attendance
    ->with(['user', 'registration'])
    ->get();

foreach ($completedAttendances as $attendance) {
    $duration = $attendance->check_out_time->diffInMinutes($attendance->check_in_time);
    $hours = intval($duration / 60);
    $minutes = $duration % 60;
    $durationText = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
    
    echo "👤 " . $attendance->user->name . " (" . (strpos($attendance->registration->registration_code, 'PART-') === 0 ? 'Participant' : 'Jury') . ")\n";
    echo "   ⏰ Duration: " . $durationText . "\n";
    echo "   📥 Check-in: " . $attendance->check_in_time->format('H:i') . "\n";
    echo "   📤 Check-out: " . $attendance->check_out_time->format('H:i') . "\n";
    echo "   ✅ Eligible for Certificate: YES\n\n";
}

echo "🏆 CERTIFICATE GENERATION WORKFLOW:\n";
echo "═══════════════════════════════════════\n";
echo "1. ✅ QR Attendance Collected: Users scanned check-in and check-out QR codes\n";
echo "2. ✅ Attendance Verified: System confirmed completed attendance\n";
echo "3. 🎯 Ready for Certificate Generation:\n";
echo "   • Participants → Participation Certificate\n";
echo "   • Jury Members → Appreciation Certificate\n\n";

echo "🌐 ORGANIZER NEXT STEPS:\n";
echo "═══════════════════════════════════════\n";
echo "1. Visit: http://localhost:8000/organizer/certificates/event/{$event->id}/attendance-summary\n";
echo "2. Review attendance statistics and eligible attendees\n";
echo "3. Generate certificates for completed attendance\n";
echo "4. Download or email certificates to recipients\n\n";

echo "✨ WORKFLOW COMPLETE! QR attendance successfully enables certificate generation.\n";
echo "=== END DEMO ===\n";