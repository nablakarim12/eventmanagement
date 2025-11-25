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

echo "\n=== REASSIGNING EVENTS TO EXISTING META UPSI ACCOUNT ===\n\n";

// Find the existing organizer account
$organizer = EventOrganizer::where('org_email', 'd20221101811@siswa.upsi.edu.my')->first();

if (!$organizer) {
    echo "❌ ERROR: Organizer account not found!\n";
    echo "Please check if the account d20221101811@siswa.upsi.edu.my exists.\n";
    exit(1);
}

echo "✅ Found existing META UPSI account:\n";
echo "📧 Email: {$organizer->org_email}\n"; 
echo "🏢 Organization: {$organizer->org_name}\n";
echo "🔑 Use your existing password: password123\n";
echo "🆔 Organizer ID: {$organizer->id}\n\n";

// Check current events for this organizer
$currentEvents = Event::where('organizer_id', $organizer->id)->get();
echo "📊 Current events for this organizer: {$currentEvents->count()}\n";

if ($currentEvents->count() > 0) {
    echo "Existing events:\n";
    foreach ($currentEvents as $event) {
        echo "   - {$event->title}\n";
    }
    echo "\n";
}

// Ensure categories exist
$academicCategory = EventCategory::firstOrCreate([
    'name' => 'Academic Conference'
], [
    'description' => 'Academic conferences for research presentations and knowledge sharing',
    'color' => '#2563eb',
    'icon' => 'academic-cap'
]);

$innovationCategory = EventCategory::firstOrCreate([
    'name' => 'Innovation Competition'  
], [
    'description' => 'Innovation and entrepreneurship competitions',
    'color' => '#dc2626',
    'icon' => 'light-bulb'
]);

echo "✅ Categories ready: {$academicCategory->name} & {$innovationCategory->name}\n\n";

// Delete previous sample events created by the script to avoid duplicates
$previousSampleEvents = Event::whereIn('title', [
    'International AI Research Conference 2025',
    'Global Innovation Summit & Competition 2025', 
    'Sustainable Technology Research Symposium'
])->get();

if ($previousSampleEvents->count() > 0) {
    echo "🗑️ Removing previous sample events to avoid duplicates...\n";
    foreach ($previousSampleEvents as $event) {
        echo "   - Deleted: {$event->title}\n";
        $event->delete();
    }
    echo "\n";
}

// Sample academic events with poster support for the existing organizer
$events = [
    [
        'title' => 'International AI Research Conference 2025',
        'description' => "Join leading AI researchers, academics, and industry experts for three days of cutting-edge presentations on artificial intelligence, machine learning, and computational intelligence.\n\nFeatured Topics:\n• Deep Learning & Neural Networks\n• Natural Language Processing\n• Computer Vision & Image Recognition\n• AI Ethics & Responsible AI\n• AI in Education & Healthcare\n\nParticipant Benefits:\n• Access to 50+ research presentations\n• Networking with global AI community\n• Certificate of participation\n• Published conference proceedings\n\nJury Benefits:\n• Evaluate innovative AI research\n• Contribute to academic excellence\n• Recognition in conference materials\n• Professional development credits",
        'short_description' => 'Leading international conference on AI research with presentations from top global researchers and industry experts.',
        'category_id' => $academicCategory->id,
        'start_date' => '2025-12-15 09:00:00',
        'end_date' => '2025-12-17 18:00:00',
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
        'venue_name' => 'Silicon Valley Convention Center',
        'venue_address' => '2001 Great America Parkway',
        'city' => 'Santa Clara',
        'state' => 'California',
        'country' => 'United States',
        'postal_code' => '95054',
        'max_participants' => 300,
        'registration_fee' => 299.00,
        'registration_deadline' => '2025-12-01 23:59:59',
        'featured_image' => 'events/posters/ai-conference-2025.jpg',
        'gallery_images' => json_encode([
            'events/gallery/ai-conf-venue.jpg',
            'events/gallery/ai-conf-speakers.jpg',
            'events/gallery/ai-conf-networking.jpg'
        ]),
        'tags' => json_encode(['AI', 'Machine Learning', 'Research', 'Academic', 'International']),
        'requirements' => json_encode([
            'participant' => [
                'Academic background in AI/CS or related field',
                'Research paper submission (optional)',
                'Valid student/professional ID'
            ],
            'jury' => [
                'PhD in AI/CS or equivalent experience',
                'Minimum 5 years research experience', 
                'Publication record in AI journals/conferences',
                'CV and recommendation letter required'
            ]
        ]),
        'benefits' => json_encode([
            'participant' => [
                'Conference certificate',
                'Access to all sessions',
                'Networking opportunities',
                'Conference proceedings',
                'Welcome kit'
            ],
            'jury' => [
                'Jury certificate with recognition',
                'Professional development credits',
                'VIP access to all sessions',
                'Speaker dinner invitation',
                'Travel allowance (if applicable)'
            ]
        ]),
        'is_active' => true,
        'status' => 'published'
    ],
    [
        'title' => 'Global Innovation Summit & Competition 2025',
        'description' => "A premier innovation competition bringing together entrepreneurs, inventors, and innovators from around the world to showcase breakthrough technologies and business ideas.\n\nCompetition Categories:\n• Tech Innovation & Startups\n• Sustainable Solutions\n• Healthcare Innovation\n• Educational Technology\n• Social Impact Projects\n\nParticipant Opportunities:\n• Pitch your innovation to expert judges\n• Network with investors and mentors\n• Win prizes up to $50,000\n• Gain media exposure\n• Access to incubation programs\n\nJury Responsibilities:\n• Evaluate innovation proposals\n• Conduct live pitch assessments\n• Provide constructive feedback\n• Select competition winners",
        'short_description' => 'Global competition for breakthrough innovations with expert jury evaluation and substantial prizes.',
        'category_id' => $innovationCategory->id,
        'start_date' => '2025-11-25 08:00:00',
        'end_date' => '2025-11-27 20:00:00',
        'start_time' => '08:00:00',
        'end_time' => '20:00:00',
        'venue_name' => 'Innovation Hub Center',
        'venue_address' => '1500 Innovation Drive',
        'city' => 'Austin',
        'state' => 'Texas',
        'country' => 'United States',
        'postal_code' => '78701',
        'max_participants' => 150,
        'registration_fee' => 199.00,
        'registration_deadline' => '2025-11-15 23:59:59',
        'featured_image' => 'events/posters/innovation-summit-2025.jpg',
        'gallery_images' => json_encode([
            'events/gallery/innovation-hub.jpg',
            'events/gallery/pitch-presentations.jpg',
            'events/gallery/networking-sessions.jpg'
        ]),
        'tags' => json_encode(['Innovation', 'Startup', 'Competition', 'Entrepreneurship', 'Technology']),
        'requirements' => json_encode([
            'participant' => [
                'Original innovation or business idea',
                'Prototype or proof of concept',
                'Business plan or project proposal',
                'Team registration (1-5 members)'
            ],
            'jury' => [
                'Industry expertise in relevant field',
                'Investment or business evaluation experience',
                'Professional background verification',
                'Signed confidentiality agreement'
            ]
        ]),
        'benefits' => json_encode([
            'participant' => [
                'Competition certificate',
                'Prize opportunities ($5K-$50K)',
                'Investor networking',
                'Mentorship opportunities',
                'Media exposure'
            ],
            'jury' => [
                'Expert jury certificate',
                'Professional networking',
                'Industry recognition',
                'VIP event access',
                'Compensation for time'
            ]
        ]),
        'is_active' => true,
        'status' => 'published'
    ],
    [
        'title' => 'Sustainable Technology Research Symposium',
        'description' => "An academic symposium focused on sustainable technology research, green innovation, and environmental solutions for the future.\n\nResearch Tracks:\n• Renewable Energy Technologies\n• Sustainable Materials & Manufacturing\n• Environmental Monitoring Systems\n• Green Computing & Data Centers\n• Climate Change Mitigation\n\nSymposium Features:\n• Peer-reviewed research presentations\n• Panel discussions with industry leaders\n• Poster sessions for emerging research\n• Collaborative research opportunities\n• Publication in symposium proceedings\n\nParticipant Tracks:\n• Research paper presentations\n• Poster presentations\n• Workshop participation\n• Industry collaboration sessions\n\nJury Excellence:\n• Peer review process participation\n• Research quality evaluation\n• Best paper award selection\n• Academic standards maintenance",
        'short_description' => 'Academic research symposium on sustainable technology and environmental solutions.',
        'category_id' => $academicCategory->id,
        'start_date' => '2026-01-20 09:30:00',
        'end_date' => '2026-01-22 17:30:00',
        'start_time' => '09:30:00',
        'end_time' => '17:30:00',
        'venue_name' => 'Green Technology Institute',
        'venue_address' => '800 Sustainability Boulevard',
        'city' => 'Portland',
        'state' => 'Oregon',
        'country' => 'United States',
        'postal_code' => '97201',
        'max_participants' => 200,
        'registration_fee' => 249.00,
        'registration_deadline' => '2026-01-05 23:59:59',
        'featured_image' => 'events/posters/sustainability-symposium-2026.jpg',
        'gallery_images' => json_encode([
            'events/gallery/green-tech-institute.jpg',
            'events/gallery/research-presentations.jpg',
            'events/gallery/sustainable-exhibits.jpg'
        ]),
        'tags' => json_encode(['Sustainability', 'Green Technology', 'Research', 'Environment', 'Academic']),
        'requirements' => json_encode([
            'participant' => [
                'Research background in sustainability/environment',
                'Abstract or paper submission',
                'Academic or industry affiliation',
                'Research ethics compliance'
            ],
            'jury' => [
                'PhD in environmental science or related field',
                'Published research in sustainability',
                'Peer review experience',
                'Academic credentials verification'
            ]
        ]),
        'benefits' => json_encode([
            'participant' => [
                'Symposium certificate',
                'Publication opportunity',
                'Research collaboration',
                'Professional networking',
                'Industry insights'
            ],
            'jury' => [
                'Peer review certification',
                'Academic recognition',
                'Research community contribution',
                'Professional development',
                'Symposium proceedings editor credit'
            ]
        ]),
        'is_active' => true,
        'status' => 'published'
    ]
];

// Create events for the existing organizer
foreach ($events as $eventData) {
    $eventData['organizer_id'] = $organizer->id;
    $eventData['created_at'] = now();
    $eventData['updated_at'] = now();
    
    $event = Event::create($eventData);
    
    echo "✅ Created event: {$event->title}\n";
    echo "   📅 Event Date: " . Carbon::parse($event->start_date)->format('M d, Y') . "\n";
    echo "   ⏰ Registration Deadline: " . Carbon::parse($event->registration_deadline)->format('M d, Y H:i') . "\n";
    echo "   💰 Fee: $" . number_format($event->registration_fee, 2) . "\n";
    echo "   👥 Max Participants: {$event->max_participants}\n";
    echo "   📍 Location: {$event->city}, {$event->state}\n";
    echo "   🖼️ Poster: {$event->featured_image}\n\n";
}

echo "📊 FINAL SUMMARY - YOUR EDITABLE EVENTS:\n";
echo "══════════════════════════════════════════════\n";

$finalEvents = Event::where('organizer_id', $organizer->id)->get();
echo "Total Events for Your Account: {$finalEvents->count()}\n\n";

echo "🔑 YOUR LOGIN CREDENTIALS:\n";
echo "📧 Email: {$organizer->org_email}\n";
echo "🔑 Password: password123\n";
echo "🏢 Organization: {$organizer->org_name}\n\n";

echo "📱 EVENTS YOU CAN NOW EDIT:\n";
foreach ($finalEvents as $event) {
    $daysLeft = now()->diffInDays(Carbon::parse($event->registration_deadline), false);
    $status = $daysLeft >= 0 ? "✅ Open ({$daysLeft} days left)" : "❌ Closed";
    
    echo "🎓 {$event->title}\n";
    echo "   Category: {$event->category->name}\n";
    echo "   Registration Status: {$status}\n";
    echo "   Event Date: " . Carbon::parse($event->start_date)->format('M d, Y') . "\n";
    echo "   Fee: $" . number_format($event->registration_fee, 2) . "\n";
    echo "   Poster Path: {$event->featured_image}\n";
    echo "   📝 You can edit this event and upload posters!\n\n";
}

echo "🎯 NEXT STEPS FOR YOU:\n";
echo "══════════════════════════════════════════════\n";
echo "1. Login to organizer dashboard with your existing credentials\n";
echo "2. Navigate to 'My Events' section\n";
echo "3. Edit each event to upload real poster images\n";
echo "4. Customize event details, requirements, and benefits\n";
echo "5. Your friend can now see these events with poster support!\n\n";

echo "🔗 INTEGRATION READY FOR FRIEND:\n";
echo "══════════════════════════════════════════════\n";
echo "✅ Events assigned to existing organizer account\n";
echo "✅ Poster fields populated with paths\n";
echo "✅ Role-specific requirements and benefits defined\n";
echo "✅ Event categories and tags configured\n";
echo "✅ Registration deadlines and fees set\n";
echo "✅ Gallery images support included\n\n";

echo "=== EVENTS SUCCESSFULLY REASSIGNED TO YOUR EXISTING ACCOUNT ===\n";