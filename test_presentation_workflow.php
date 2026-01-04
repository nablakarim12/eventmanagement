<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Testing Presentation Approval Integration ===\n\n";

// Find a conference event
$event = Event::whereNotNull('delivery_mode')->first();

if (!$event) {
    echo "❌ No conference events found!\n";
    exit;
}

echo "✅ Found Event: {$event->title}\n";
echo "   Event ID: {$event->id}\n";
echo "   Delivery Mode: {$event->delivery_mode}\n\n";

// Find papers for this event
$papers = DB::table('event_papers')
    ->where('event_id', $event->id)
    ->get();

echo "📄 Total Papers: " . $papers->count() . "\n\n";

if ($papers->isEmpty()) {
    echo "❌ No papers found for this event!\n";
    exit;
}

// Check first paper details
$paper = $papers->first();

echo "📝 Testing with Paper ID: {$paper->id}\n";
echo "   Title: " . ($paper->title ?? 'Untitled') . "\n";
echo "   User ID: {$paper->user_id}\n\n";

// Check if user exists
$user = User::find($paper->user_id);
if (!$user) {
    echo "❌ User not found for this paper!\n";
    exit;
}

echo "👤 Participant: {$user->name}\n";
echo "   Email: {$user->email}\n\n";

// Check registration
$registration = DB::table('event_registrations')
    ->where('user_id', $paper->user_id)
    ->where('event_id', $event->id)
    ->first();

if (!$registration) {
    echo "❌ No registration found for this participant!\n";
    exit;
}

echo "✅ Registration Found: ID {$registration->id}\n";
echo "   Current Status: " . ($registration->presentation_status ?? 'Pending') . "\n\n";

// Check jury scores
$scores = DB::table('rubric_item_scores')
    ->where('event_paper_id', $paper->id)
    ->get();

echo "📊 Rubric Scores: {$scores->count()} scores found\n";

if ($scores->isEmpty()) {
    echo "⚠️  No scores yet - reviewers haven't evaluated this paper\n\n";
} else {
    $avgScore = $scores->avg('score');
    echo "   Average Score: " . number_format($avgScore, 2) . "\n";
    
    $evaluators = $scores->pluck('evaluator_id')->unique();
    echo "   Total Evaluators: {$evaluators->count()}\n\n";
}

// Check presentation fields
echo "📋 Presentation Details:\n";
echo "   Status: " . ($registration->presentation_status ?? 'NULL') . "\n";
echo "   Queue: " . ($registration->presentation_queue ?? 'NULL') . "\n";
echo "   Time: " . ($registration->presentation_time ?? 'NULL') . "\n";
echo "   Link: " . ($registration->presentation_link ?? 'NULL') . "\n";
echo "   Location: " . ($registration->presentation_location ?? 'NULL') . "\n";
echo "   Average Score Saved: " . ($registration->average_score ?? 'NULL') . "\n";
echo "   Rejection Reason: " . ($registration->rejection_reason ?? 'NULL') . "\n";
echo "   Notified At: " . ($registration->presentation_notified_at ?? 'NULL') . "\n\n";

// Check routes
echo "🔗 Routes Check:\n";
$routes = [
    'organizer.evaluation-results.index',
    'organizer.evaluation-results.show-event',
    'organizer.evaluation-results.paper-details',
    'organizer.evaluation-results.approve-presentation',
    'organizer.evaluation-results.reject-presentation',
];

foreach ($routes as $route) {
    if (Route::has($route)) {
        echo "   ✅ {$route}\n";
    } else {
        echo "   ❌ {$route} - NOT FOUND\n";
    }
}

echo "\n";

// Check notification classes
echo "📧 Notification Classes Check:\n";
$notificationClasses = [
    'App\Notifications\PresentationSelectedNotification',
    'App\Notifications\PresentationRejectedNotification',
];

foreach ($notificationClasses as $class) {
    if (class_exists($class)) {
        echo "   ✅ {$class}\n";
    } else {
        echo "   ❌ {$class} - NOT FOUND\n";
    }
}

echo "\n";

echo "=== Test Complete ===\n";
echo "\n📍 Next Steps:\n";
echo "1. Login: http://eventmanagement.test/organizer/login\n";
echo "2. Navigate: Evaluation Results → {$event->title}\n";
$paperTitle = $paper->title ?? ('Paper #' . $paper->id);
echo "3. Click on paper: {$paperTitle}\n";
echo "4. Test: Click 'Approve for Presentation' or 'Reject Presentation'\n";
echo "5. Verify: Email sent to {$user->email}\n";

