<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Event;
use App\Models\EventOrganizer;
use App\Models\EventCategory;
use Illuminate\Support\Str;

<?php

require_once 'vendor/autoload.php';

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventOrganizer;
use Carbon\Carbon;

// Laravel Bootstrap
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== CREATING SAMPLE ACADEMIC EVENTS WITH POSTER SUPPORT ===\n\n";

// Get or create an organizer
$organizer = EventOrganizer::first();
if (!$organizer) {
    $organizer = EventOrganizer::create([
        'name' => 'Academic Events Organizer',
        'email' => 'organizer@academic.edu',
        'password' => bcrypt('password'),
        'organization_name' => 'University Research Center',
        'phone' => '+1234567890',
        'is_verified' => true,
        'email_verified_at' => now(),
    ]);
    echo "✅ Created sample organizer: " . $organizer->name . "\n";
}

// Get or create categories
$academicCategory = EventCategory::firstOrCreate(['name' => 'Academic Conference']);
$innovationCategory = EventCategory::firstOrCreate(['name' => 'Innovation Competition']);

echo "✅ Categories ready: Academic Conference & Innovation Competition\n\n";

// Academic Conference Events
$academicEvents = [
    [
        'title' => 'International AI Research Conference 2025',
        'description' => 'Join leading researchers, academics, and industry experts for the premier conference on Artificial Intelligence and Machine Learning. Present your research, learn from keynote speakers, and network with the global AI community.',
        'short_description' => 'Premier AI research conference bringing together global experts and researchers.',
        'venue_name' => 'Grand Convention Center',
        'venue_address' => '123 Research Drive, Tech City',
        'city' => 'Tech City',
        'state' => 'California',
        'country' => 'United States',
        'start_date' => '2025-12-15 09:00:00',
        'end_date' => '2025-12-17 18:00:00',
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
        'registration_deadline' => '2025-12-01 23:59:59',
        'max_participants' => 300,
        'registration_fee' => 299.00,
        'requirements' => json_encode([
            'participant' => [
                'Research paper submission',
                'Abstract submission',
                'Poster presentation materials',
                'University affiliation proof'
            ],
            'jury' => [
                'PhD in relevant field',
                'Minimum 5 years research experience',
                'Publication record',
                'CV and credentials'
            ]
        ]),
    ],
    [
        'title' => 'Global Innovation Summit & Competition 2025',
        'description' => 'The ultimate platform for innovators, entrepreneurs, and tech enthusiasts. Present your groundbreaking innovations, compete for prizes, and get evaluated by industry experts and venture capitalists.',
        'short_description' => 'Innovation competition with industry expert evaluation and prizes.',
        'venue_name' => 'Innovation Hub Center',
        'venue_address' => '456 Innovation Boulevard, Startup Valley',
        'city' => 'Startup Valley',
        'state' => 'California',
        'country' => 'United States',
        'start_date' => '2025-11-25 08:00:00',
        'end_date' => '2025-11-27 20:00:00',
        'start_time' => '08:00:00',
        'end_time' => '20:00:00',
        'registration_deadline' => '2025-11-15 23:59:59',
        'max_participants' => 150,
        'registration_fee' => 199.00,
        'requirements' => json_encode([
            'participant' => [
                'Innovation prototype or concept',
                'Business plan or pitch deck',
                'Demo video (max 5 minutes)',
                'Technical documentation'
            ],
            'jury' => [
                'Industry experience (minimum 7 years)',
                'Investment or entrepreneurship background',
                'Technical expertise in relevant field',
                'Professional references'
            ]
        ]),
    ],
    [
        'title' => 'Sustainable Technology Research Symposium',
        'description' => 'Focus on sustainable technology solutions and green innovations. Academic researchers and industry professionals collaborate to address environmental challenges through technology.',
        'short_description' => 'Sustainable technology research and innovation symposium.',
        'venue_name' => 'Green Tech University',
        'venue_address' => '789 Sustainability Lane, EcoCity',
        'city' => 'EcoCity',
        'state' => 'Oregon',
        'country' => 'United States',
        'start_date' => '2026-01-20 09:30:00',
        'end_date' => '2026-01-22 17:30:00',
        'start_time' => '09:30:00',
        'end_time' => '17:30:00',
        'registration_deadline' => '2026-01-05 23:59:59',
        'max_participants' => 200,
        'registration_fee' => 249.00,
        'requirements' => json_encode([
            'participant' => [
                'Research in sustainable technology',
                'Environmental impact analysis',
                'Prototype or research findings',
                'Sustainability credentials'
            ],
            'jury' => [
                'Environmental science expertise',
                'Sustainable technology background',
                'Academic or industry experience',
                'Published research in sustainability'
            ]
        ]),
    ]
];

$createdEvents = [];

foreach ($academicEvents as $index => $eventData) {
    // Check if event already exists
    $existingEvent = Event::where('title', $eventData['title'])->first();
    
    if (!$existingEvent) {
        $event = Event::create([
            'organizer_id' => $organizer->id,
            'category_id' => $index === 1 ? $innovationCategory->id : $academicCategory->id, // Second event is innovation competition
            'slug' => Str::slug($eventData['title']),
            'status' => 'published',
            'is_public' => true,
            'requires_approval' => true, // Academic events typically require approval
            'is_free' => false,
            'allow_waitlist' => true,
            'current_participants' => 0,
            'contact_email' => $organizer->email,
            'contact_phone' => $organizer->phone,
            'views' => rand(50, 200),
            'budget' => $eventData['registration_fee'] * $eventData['max_participants'],
            'tags' => json_encode(['academic', 'research', 'conference', 'innovation']),
            ...$eventData
        ]);
        
        $createdEvents[] = $event;
        echo "✅ Created event: " . $event->title . "\n";
        echo "   📅 Event Date: " . $event->start_date->format('M d, Y') . "\n";
        echo "   ⏰ Registration Deadline: " . $event->registration_deadline->format('M d, Y H:i') . "\n";
        echo "   💰 Fee: $" . number_format($event->registration_fee, 2) . "\n";
        echo "   👥 Max Participants: " . $event->max_participants . "\n";
        echo "   📍 Location: " . $event->city . ", " . $event->state . "\n\n";
    } else {
        echo "⚠️  Event already exists: " . $eventData['title'] . "\n\n";
        $createdEvents[] = $existingEvent;
    }
}

echo "📊 EVENTS SUMMARY FOR FRIEND'S DASHBOARD:\n";
echo "══════════════════════════════════════════════\n";
echo "Total Events Available: " . count($createdEvents) . "\n\n";

foreach ($createdEvents as $event) {
    $daysUntilDeadline = now()->diffInDays($event->registration_deadline, false);
    $deadlineStatus = $daysUntilDeadline > 0 ? "✅ Open ({$daysUntilDeadline} days left)" : "❌ Closed";
    
    echo "🎓 " . $event->title . "\n";
    echo "   Category: " . ($event->category->name ?? 'N/A') . "\n";
    echo "   Registration Status: " . $deadlineStatus . "\n";
    echo "   Event Date: " . $event->start_date->format('M d, Y') . "\n";
    echo "   Fee: $" . number_format($event->registration_fee, 2) . "\n";
    echo "   Available Spots: " . ($event->max_participants - $event->current_participants) . "/" . $event->max_participants . "\n\n";
}

echo "🔗 INTEGRATION POINTS FOR FRIEND:\n";
echo "══════════════════════════════════════════════\n";
echo "Database Tables Available:\n";
echo "✅ events - All event details with registration deadlines\n";
echo "✅ event_categories - Academic Conference & Innovation Competition\n";
echo "✅ event_organizers - Organizer information\n\n";

echo "API Endpoints Friend Should Create:\n";
echo "📍 GET /api/events/available - List events open for registration\n";
echo "📍 GET /api/events/{id} - Event details for registration form\n";
echo "📍 POST /api/events/{id}/register - User registration with role selection\n";
echo "📍 POST /api/registrations/{id}/upload - File uploads for materials/documents\n\n";

echo "Database Schema Friend Should Follow:\n";
echo "📋 See DATABASE_COORDINATION.md for complete schema requirements\n\n";

echo "✨ READY FOR FRIEND'S DEVELOPMENT!\n";
echo "Your friend can now:\n";
echo "1. Display these events on user dashboard\n";
echo "2. Show registration deadlines and event details\n";
echo "3. Build role selection (participant/jury/both)\n";
echo "4. Create dynamic forms for each role\n";
echo "5. Implement file upload system\n\n";

echo "=== SAMPLE EVENTS CREATION COMPLETE ===\n";