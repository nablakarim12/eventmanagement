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

// Find or create META UPSI organizer
$metaUpsi = EventOrganizer::firstOrCreate(
    ['org_email' => 'metaupsi@upsi.edu.my'],
    [
        'org_name' => 'META UPSI',
        'password' => bcrypt('metaupsi123'),
        'description' => 'Multimedia, Educational Technology & Academic Research Unit - Universiti Pendidikan Sultan Idris',
        'phone' => '+603-3513-5000',
        'website' => 'https://www.upsi.edu.my',
        'address' => 'Universiti Pendidikan Sultan Idris',
        'city' => 'Tanjung Malim',
        'state' => 'Perak',
        'country' => 'Malaysia',
        'postal_code' => '35900',
        'contact_person_name' => 'Dr. Ahmad Rahman',
        'contact_person_position' => 'Director of META UPSI',
        'status' => 'approved',
        'approved_at' => now(),
        'created_at' => now(),
        'updated_at' => now()
    ]
);

echo "✅ META UPSI Organizer ready: {$metaUpsi->org_name}\n";
echo "   📧 Login: {$metaUpsi->org_email}\n";
echo "   🔑 Password: metaupsi123\n\n";

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

// Sample academic events with poster support
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

// Create events
foreach ($events as $eventData) {
    $eventData['organizer_id'] = $metaUpsi->id;
    $eventData['created_at'] = now();
    $eventData['updated_at'] = now();
    
    $event = Event::create($eventData);
    
    echo "✅ Created event: {$event->title}\n";
    echo "   📅 Event Date: " . Carbon::parse($event->start_date)->format('M d, Y') . "\n";
    echo "   ⏰ Registration Deadline: " . Carbon::parse($event->registration_deadline)->format('M d, Y H:i') . "\n";
    echo "   💰 Fee: $" . number_format($event->registration_fee, 2) . "\n";
    echo "   👥 Max Participants: {$event->max_participants}\n";
    echo "   📍 Location: {$event->city}, {$event->state}\n";
    echo "   🖼️ Poster: {$event->featured_image}\n";
    echo "   📂 Organizer: {$metaUpsi->org_name}\n\n";
}

echo "📊 EVENTS SUMMARY WITH POSTER SUPPORT:\n";
echo "══════════════════════════════════════════════\n";

$totalEvents = Event::where('organizer_id', $metaUpsi->id)->count();
echo "Total Events Created: {$totalEvents}\n";
echo "Organizer Account: {$metaUpsi->org_name}\n";
echo "Login Email: {$metaUpsi->org_email}\n";
echo "Password: metaupsi123\n\n";

$events = Event::where('organizer_id', $metaUpsi->id)->get();
foreach ($events as $event) {
    $daysLeft = now()->diffInDays(Carbon::parse($event->registration_deadline), false);
    $status = $daysLeft >= 0 ? "✅ Open ({$daysLeft} days left)" : "❌ Closed";
    
    echo "🎓 {$event->title}\n";
    echo "   Category: {$event->category->name}\n";
    echo "   Registration Status: {$status}\n";
    echo "   Event Date: " . Carbon::parse($event->start_date)->format('M d, Y') . "\n";
    echo "   Fee: $" . number_format($event->registration_fee, 2) . "\n";
    echo "   Available Spots: {$event->max_participants}/{$event->max_participants}\n";
    echo "   Poster: {$event->featured_image}\n\n";
}

echo "🔗 POSTER INTEGRATION FOR FRIEND:\n";
echo "══════════════════════════════════════════════\n";
echo "Database Fields Available:\n";
echo "✅ featured_image - Main event poster/banner\n";
echo "✅ gallery_images - Additional event images (JSON array)\n";
echo "✅ requirements - Role-specific requirements (JSON)\n";
echo "✅ benefits - Role-specific benefits (JSON)\n";
echo "✅ tags - Event tags for filtering (JSON array)\n\n";

echo "Frontend Implementation Suggestions:\n";
echo "📍 Display featured_image as event poster in dashboard\n";
echo "📍 Show gallery_images in event detail carousel\n";
echo "📍 Use requirements/benefits for role-specific info\n";
echo "📍 Implement image upload for poster management\n\n";

echo "File Upload Structure:\n";
echo "📁 public/storage/events/posters/ - Main event posters\n";
echo "📁 public/storage/events/gallery/ - Additional images\n";
echo "📁 storage/app/public/events/ - Actual file storage\n\n";

echo "🎯 YOUR EDIT ACCESS:\n";
echo "══════════════════════════════════════════════\n";
echo "Login to organizer dashboard with:\n";
echo "📧 Email: {$metaUpsi->org_email}\n";
echo "🔑 Password: metaupsi123\n";
echo "📝 You can now edit these events and upload posters!\n";
echo "🖼️ Add real poster images through the organizer interface\n";
echo "📊 Manage event details, requirements, and benefits\n\n";

echo "✨ READY FOR FRIEND'S POSTER INTEGRATION!\n";
echo "Your friend can now:\n";
echo "1. Display event posters in user dashboard\n";
echo "2. Show detailed event information with images\n";
echo "3. Implement role-specific requirements/benefits display\n";
echo "4. Create poster upload functionality for organizers\n";
echo "5. Build image gallery for events\n\n";

echo "=== SAMPLE EVENTS WITH POSTER SUPPORT COMPLETE ===\n";