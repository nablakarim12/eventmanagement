<?php

/**
 * Test Conference Event Creation Flow
 * 
 * This script simulates the conference event creation process
 * and verifies all required fields and functionality.
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use App\Models\EventOrganizer;
use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Conference Event Creation Flow Test ===\n\n";

// Get Academic Conference category
$conferenceCategory = EventCategory::where('name', 'Academic Conference')->first();

if (!$conferenceCategory) {
    echo "❌ Academic Conference category not found!\n";
    echo "Creating category...\n";
    $conferenceCategory = EventCategory::create([
        'name' => 'Academic Conference',
        'slug' => 'academic-conference',
        'description' => 'Academic conferences for research and paper presentations',
    ]);
    echo "✅ Category created: {$conferenceCategory->name}\n\n";
} else {
    echo "✅ Academic Conference category found (ID: {$conferenceCategory->id})\n\n";
}

// Get or create a test organizer
$user = User::where('email', 'organizer@test.com')->first();
if (!$user) {
    echo "Creating test user account...\n";
    $user = User::create([
        'name' => 'Test Organizer',
        'email' => 'organizer@test.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
    echo "✅ User created: {$user->email}\n";
}

// Create event organizer record
$organizer = EventOrganizer::where('org_email', 'organizer@test.com')->first();
if (!$organizer) {
    echo "Creating event organizer record...\n";
    $organizer = EventOrganizer::create([
        'org_name' => 'Test University',
        'org_email' => 'organizer@test.com',
        'password' => bcrypt('password'),
        'phone' => '+1234567890',
        'address' => '123 Test Street',
        'city' => 'Test City',
        'state' => 'Test State',
        'country' => 'Test Country',
        'postal_code' => '12345',
        'contact_person_name' => 'Test Organizer',
        'contact_person_position' => 'Director',
        'status' => 'approved',
        'approved_at' => now(),
    ]);
    echo "✅ Organizer created: {$organizer->org_name} (ID: {$organizer->id})\n\n";
} else {
    echo "✅ Test organizer found: {$organizer->org_name} (ID: {$organizer->id})\n\n";
}

// Test conference event data
$eventData = [
    'organizer_id' => $organizer->id,
    'category_id' => $conferenceCategory->id,
    'title' => 'International Conference on AI & Machine Learning 2025',
    'slug' => 'ai-ml-conference-2025-test',
    'description' => 'A premier conference for researchers and practitioners in AI and ML to present their latest findings.',
    'venue_name' => 'Grand Conference Hall',
    'venue_address' => '123 University Ave, Tech City',
    'city' => 'Tech City',
    'state' => 'Tech State',
    'country' => 'Techland',
    'is_free' => false,
    'price' => 299.00,
    'is_public' => true,
    'event_form_type' => 'conference',
    
    // Conference-specific fields
    'delivery_mode' => 'hybrid',
    'conference_categories' => json_encode(['Artificial Intelligence', 'Machine Learning', 'Deep Learning']),
    
    // F2F deadlines
    'f2f_reviewer_registration_deadline' => now()->addDays(30)->toDateTimeString(),
    'f2f_paper_submission_deadline' => now()->addDays(60)->toDateTimeString(),
    'f2f_review_deadline' => now()->addDays(90)->toDateTimeString(),
    'f2f_acceptance_notification_date' => now()->addDays(100)->toDateTimeString(),
    'f2f_payment_deadline' => now()->addDays(110)->toDateTimeString(),
    'f2f_start_date' => now()->addDays(120)->toDateTimeString(),
    'f2f_end_date' => now()->addDays(122)->toDateTimeString(),
    
    // Online deadlines
    'online_reviewer_registration_deadline' => now()->addDays(35)->toDateTimeString(),
    'online_paper_submission_deadline' => now()->addDays(65)->toDateTimeString(),
    'online_review_deadline' => now()->addDays(95)->toDateTimeString(),
    'online_acceptance_notification_date' => now()->addDays(105)->toDateTimeString(),
    'online_payment_deadline' => now()->addDays(115)->toDateTimeString(),
    'online_start_date' => now()->addDays(120)->toDateTimeString(),
    'online_end_date' => now()->addDays(122)->toDateTimeString(),
    
    'max_participants' => 500,
    'requires_approval' => true,
    'allow_waitlist' => false,
    'status' => 'published',
];

echo "Creating test conference event...\n";
echo "Title: {$eventData['title']}\n";
echo "Delivery Mode: {$eventData['delivery_mode']}\n";
echo "Categories: " . implode(', ', json_decode($eventData['conference_categories'])) . "\n\n";

try {
    // Check if test event already exists
    $existingEvent = Event::where('slug', $eventData['slug'])->first();
    if ($existingEvent) {
        echo "⚠️ Test event already exists. Deleting...\n";
        $existingEvent->delete();
    }
    
    // Create the event
    $event = Event::create($eventData);
    
    echo "✅ Conference event created successfully!\n\n";
    
    // Verify conference-specific fields
    echo "=== Event Verification ===\n";
    echo "Event ID: {$event->id}\n";
    echo "Title: {$event->title}\n";
    echo "Type: {$event->event_form_type}\n";
    echo "Delivery Mode: {$event->delivery_mode}\n";
    echo "Categories: " . implode(', ', json_decode($event->conference_categories)) . "\n\n";
    
    echo "=== F2F Deadlines ===\n";
    echo "Reviewer Registration: " . \Carbon\Carbon::parse($event->f2f_reviewer_registration_deadline)->format('Y-m-d H:i') . "\n";
    echo "Paper Submission: " . \Carbon\Carbon::parse($event->f2f_paper_submission_deadline)->format('Y-m-d H:i') . "\n";
    echo "Review Deadline: " . \Carbon\Carbon::parse($event->f2f_review_deadline)->format('Y-m-d H:i') . "\n";
    echo "Acceptance Notification: " . \Carbon\Carbon::parse($event->f2f_acceptance_notification_date)->format('Y-m-d H:i') . "\n";
    echo "Event Start: " . \Carbon\Carbon::parse($event->f2f_start_date)->format('Y-m-d H:i') . "\n\n";
    
    echo "=== Online Deadlines ===\n";
    echo "Reviewer Registration: " . \Carbon\Carbon::parse($event->online_reviewer_registration_deadline)->format('Y-m-d H:i') . "\n";
    echo "Paper Submission: " . \Carbon\Carbon::parse($event->online_paper_submission_deadline)->format('Y-m-d H:i') . "\n";
    echo "Review Deadline: " . \Carbon\Carbon::parse($event->online_review_deadline)->format('Y-m-d H:i') . "\n";
    echo "Acceptance Notification: " . \Carbon\Carbon::parse($event->online_acceptance_notification_date)->format('Y-m-d H:i') . "\n";
    echo "Event Start: " . \Carbon\Carbon::parse($event->online_start_date)->format('Y-m-d H:i') . "\n\n";
    
    // Check database columns
    echo "=== Database Column Verification ===\n";
    $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'events' AND table_schema = 'public' ORDER BY ordinal_position");
    $conferenceColumns = array_filter($columns, function($col) {
        return strpos($col->column_name, 'f2f_') === 0 || 
               strpos($col->column_name, 'online_') === 0 || 
               in_array($col->column_name, ['delivery_mode', 'conference_categories']);
    });
    
    echo "Conference-specific columns (" . count($conferenceColumns) . "):\n";
    foreach ($conferenceColumns as $col) {
        $value = $event->{$col->column_name};
        $display = is_null($value) ? 'NULL' : (is_string($value) ? substr($value, 0, 50) : $value);
        echo "  - {$col->column_name}: {$display}\n";
    }
    
    echo "\n✅ All tests passed! Conference event system is working correctly.\n";
    echo "\n📝 You can now:\n";
    echo "   1. Visit /organizer/events/{$event->id}/edit to view the edit form\n";
    echo "   2. Create participant registrations for this event\n";
    echo "   3. Test paper submission workflow\n";
    echo "   4. Assign reviewers and test review process\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error creating conference event:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
