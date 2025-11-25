<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventOrganizer;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SampleEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First create sample organizer if none exists
        $organizer = EventOrganizer::firstOrCreate(
            ['org_email' => 'organizer@eventsphere.com'],
            [
                'org_name' => 'EventSphere Organizer',
                'contact_person_name' => 'John Doe',
                'contact_person_position' => 'Event Manager',
                'phone' => '+1234567890',
                'password' => bcrypt('password123'),
                'status' => 'approved',
                'city' => 'San Francisco',
                'state' => 'CA',
                'country' => 'USA',
                'description' => 'Premier event organization company specializing in technology and innovation events.',
            ]
        );

        // Get categories
        $categories = EventCategory::all();
        
        if ($categories->isEmpty()) {
            // Create categories if they don't exist
            $categories = collect([
                EventCategory::create([
                    'name' => 'Academic Conference',
                    'description' => 'Scientific and academic conferences for research presentations and knowledge sharing.',
                    'color' => '#3b82f6',
                ]),
                EventCategory::create([
                    'name' => 'Innovation Competition',
                    'description' => 'Competitions focused on innovative solutions, startups, and entrepreneurship.',
                    'color' => '#ef4444',
                ]),
                EventCategory::create([
                    'name' => 'Workshop',
                    'description' => 'Hands-on learning sessions and skill development workshops.',
                    'color' => '#10b981',
                ]),
            ]);
        }

        $sampleEvents = [
            [
                'title' => 'Tech Innovation Summit 2025',
                'description' => 'Join us for the most anticipated technology summit of the year! Discover groundbreaking innovations, connect with industry leaders, and explore the future of technology. This event features keynote speakers from major tech companies, hands-on workshops, networking sessions, and product demonstrations. Whether you\'re a developer, entrepreneur, or tech enthusiast, this summit offers valuable insights into emerging technologies like AI, blockchain, IoT, and quantum computing.',
                'short_description' => 'The ultimate technology summit featuring innovation, networking, and future tech insights.',
                'start_date' => Carbon::now()->addDays(15)->setTime(9, 0),
                'end_date' => Carbon::now()->addDays(17)->setTime(18, 0),
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'venue_name' => 'Grand Tech Convention Center',
                'venue_address' => '123 Innovation Drive',
                'city' => 'San Francisco',
                'state' => 'CA',
                'country' => 'USA',
                'max_participants' => 500,
                'current_participants' => 187,
                'registration_fee' => 299.00,
                'is_free' => false,
                'registration_deadline' => Carbon::now()->addDays(10),
                'status' => 'published',
                'requires_approval' => false,
                'is_public' => true,
                'allow_waitlist' => true,
                'contact_email' => 'info@techsummit.com',
                'contact_phone' => '+1-555-TECH-SUM',
                'website_url' => 'https://techsummit2025.com',
                'requirements' => ['Laptop', 'Business attire recommended'],
                'tags' => ['technology', 'innovation', 'networking', 'AI', 'blockchain'],
            ],
            [
                'title' => 'Digital Marketing Masterclass',
                'description' => 'Master the art of digital marketing in this comprehensive workshop. Learn cutting-edge strategies for social media marketing, search engine optimization, content creation, email marketing, and data analytics. Our expert instructors will guide you through real-world case studies and hands-on exercises. Perfect for marketing professionals, business owners, and entrepreneurs looking to enhance their digital presence and drive growth.',
                'short_description' => 'Comprehensive digital marketing workshop covering SEO, social media, and analytics.',
                'start_date' => Carbon::now()->addDays(8)->setTime(10, 0),
                'end_date' => Carbon::now()->addDays(8)->setTime(16, 0),
                'start_time' => '10:00:00',
                'end_time' => '16:00:00',
                'venue_name' => 'Marketing Hub Conference Room',
                'venue_address' => '456 Business Boulevard',
                'city' => 'New York',
                'state' => 'NY', 
                'country' => 'USA',
                'max_participants' => 50,
                'current_participants' => 23,
                'registration_fee' => 0.00,
                'is_free' => true,
                'registration_deadline' => Carbon::now()->addDays(5),
                'status' => 'published',
                'requires_approval' => false,
                'is_public' => true,
                'allow_waitlist' => false,
                'contact_email' => 'register@marketinghub.com',
                'contact_phone' => '+1-555-MKT-HUB',
                'requirements' => ['Notebook', 'Laptop or tablet'],
                'tags' => ['marketing', 'digital', 'SEO', 'social media', 'free'],
            ],
            [
                'title' => 'Startup Pitch Competition 2025',
                'description' => 'Calling all entrepreneurs! Present your innovative startup ideas to a panel of seasoned investors and industry experts. This competition offers a platform for emerging startups to showcase their business models, receive valuable feedback, and potentially secure funding. Winners receive cash prizes, mentorship opportunities, and access to our exclusive incubator program. Whether you\'re in tech, healthcare, fintech, or any other industry, this is your chance to shine.',
                'short_description' => 'Startup competition with investor panel, prizes, and incubator opportunities.',
                'start_date' => Carbon::now()->addDays(25)->setTime(13, 0),
                'end_date' => Carbon::now()->addDays(25)->setTime(19, 0),
                'start_time' => '13:00:00',
                'end_time' => '19:00:00',
                'venue_name' => 'Entrepreneur Center Auditorium',
                'venue_address' => '789 Startup Street',
                'city' => 'Austin',
                'state' => 'TX',
                'country' => 'USA',
                'max_participants' => 100,
                'current_participants' => 45,
                'registration_fee' => 75.00,
                'is_free' => false,
                'registration_deadline' => Carbon::now()->addDays(20),
                'status' => 'published',
                'requires_approval' => true,
                'is_public' => true,
                'allow_waitlist' => true,
                'contact_email' => 'pitch@entrepreneurcenter.com',
                'contact_phone' => '+1-555-STARTUP',
                'requirements' => ['Business plan', 'Pitch deck (10-15 slides)', 'Demo (if applicable)'],
                'tags' => ['startup', 'entrepreneurship', 'pitch', 'competition', 'investment'],
            ],
        ];

        foreach ($sampleEvents as $index => $eventData) {
            // Assign category cyclically
            $eventData['organizer_id'] = $organizer->id;
            $eventData['category_id'] = $categories->get($index % $categories->count())->id;
            
            Event::create($eventData);
        }
    }
}
