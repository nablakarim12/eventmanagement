<?php

/**
 * DATABASE RECOVERY SCRIPT
 * This script will help recover your database after data deletion
 * 
 * What this script does:
 * 1. Creates/resets the admin account
 * 2. Reseeds essential data (Event Categories, Event Types)
 * 3. Shows current database status
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\EventCategory;
use App\Models\EventType;
use Illuminate\Support\Str;

echo "\n";
echo "=====================================\n";
echo "  DATABASE RECOVERY SCRIPT\n";
echo "=====================================\n\n";

try {
    // Test database connection
    echo "Testing database connection...\n";
    DB::connection()->getPdo();
    echo "✓ Database connection successful!\n\n";

    // ============================================
    // STEP 1: RECOVER/CREATE ADMIN ACCOUNT
    // ============================================
    echo "STEP 1: Recovering Admin Account\n";
    echo "------------------------------------\n";
    
    // Delete existing admin if any
    DB::table('admins')->truncate();
    echo "✓ Cleared existing admin records\n";
    
    // Create new admin
    $admin = Admin::create([
        'username' => 'admin',
        'email' => 'admin@eventsphere.com',
        'password' => Hash::make('admin123'),
    ]);
    
    echo "✓ Admin account created successfully!\n";
    echo "  Username: admin\n";
    echo "  Email: admin@eventsphere.com\n";
    echo "  Password: admin123\n";
    echo "  ⚠️  PLEASE CHANGE THIS PASSWORD AFTER LOGIN!\n\n";

    // ============================================
    // STEP 2: RECOVER EVENT CATEGORIES
    // ============================================
    echo "STEP 2: Recovering Event Categories\n";
    echo "------------------------------------\n";
    
    $existingCategories = EventCategory::count();
    echo "Current categories: $existingCategories\n";
    
    if ($existingCategories == 0) {
        $categories = [
            [
                'name' => 'Academic Conference',
                'description' => 'Scientific and academic conferences for research presentations and knowledge sharing.',
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Innovation Competition',
                'description' => 'Competitions focused on innovative solutions, startups, and entrepreneurship.',
                'color' => '#ef4444',
            ],
            [
                'name' => 'Workshop',
                'description' => 'Hands-on learning sessions and skill development workshops.',
                'color' => '#10b981',
            ],
            [
                'name' => 'Seminar',
                'description' => 'Educational seminars and informational sessions.',
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Training Session',
                'description' => 'Professional training and skill development programs.',
                'color' => '#8b5cf6',
            ],
        ];

        foreach ($categories as $category) {
            EventCategory::create($category);
            echo "  ✓ Created: {$category['name']}\n";
        }
        echo "\n✓ Event categories restored!\n\n";
    } else {
        echo "✓ Event categories already exist (skipping)\n\n";
    }

    // ============================================
    // STEP 3: RECOVER EVENT TYPES
    // ============================================
    echo "STEP 3: Recovering Event Types\n";
    echo "------------------------------------\n";
    
    $existingTypes = EventType::count();
    echo "Current event types: $existingTypes\n";
    
    if ($existingTypes == 0) {
        // Insert event types using raw SQL for PostgreSQL boolean compatibility
        DB::statement("
            INSERT INTO event_types (name, slug, description, icon, is_active, created_at, updated_at)
            VALUES 
                ('Innovation', 'innovation', 'Innovation events focused on new ideas, technologies, and creative solutions.', 'lightbulb', TRUE, NOW(), NOW()),
                ('Conference', 'conference', 'Professional conferences for knowledge sharing and networking.', 'users-rectangle', TRUE, NOW(), NOW())
        ");
        
        echo "  ✓ Created: Innovation\n";
        echo "  ✓ Created: Conference\n";
        echo "\n✓ Event types restored!\n\n";
    } else {
        echo "✓ Event types already exist (skipping)\n\n";
    }

    // ============================================
    // STEP 4: DATABASE STATUS REPORT
    // ============================================
    echo "STEP 4: Database Status Report\n";
    echo "------------------------------------\n";
    
    $tables = [
        'admins' => 'Admins',
        'users' => 'Users (Organizers)',
        'event_categories' => 'Event Categories',
        'event_types' => 'Event Types',
        'events' => 'Events',
        'registrations' => 'Event Registrations',
        'jury_members' => 'Jury Members',
        'evaluations' => 'Evaluations',
        'certificates' => 'Certificates',
        'paper_submissions' => 'Paper Submissions',
    ];

    foreach ($tables as $table => $label) {
        try {
            $count = DB::table($table)->count();
            echo sprintf("  %-25s: %d records\n", $label, $count);
        } catch (\Exception $e) {
            echo sprintf("  %-25s: Table not found or error\n", $label);
        }
    }

    echo "\n";
    echo "=====================================\n";
    echo "  RECOVERY COMPLETE!\n";
    echo "=====================================\n\n";
    
    echo "✓ You can now login with:\n";
    echo "  URL: " . env('APP_URL') . "/admin/login\n";
    echo "  Username: admin\n";
    echo "  Password: admin123\n\n";
    
    echo "⚠️  IMPORTANT NEXT STEPS:\n";
    echo "1. Login to admin panel\n";
    echo "2. Change the admin password immediately\n";
    echo "3. Check your data in each section\n";
    echo "4. Restore any remaining data from backups if available\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
