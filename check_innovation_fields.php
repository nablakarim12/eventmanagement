<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Event;
use Illuminate\Support\Facades\DB;

echo "=== INNOVATION EVENT FORM - DATABASE ANALYSIS ===\n\n";

// Get innovation event
$innovationEvent = Event::whereHas('category', function($query) {
    $query->where('name', 'Innovation Competition');
})->first();

if ($innovationEvent) {
    echo "Innovation Event Found: {$innovationEvent->title}\n";
    echo "Delivery Mode: {$innovationEvent->delivery_mode}\n\n";
    
    echo "=== ALL INNOVATION FORM FIELDS ===\n\n";
    
    $fields = [
        'Basic Info' => [
            'title' => 'title',
            'category_id' => 'category_id',
            'description' => 'description',
            'innovation_categories' => 'innovation_categories',
        ],
        'Location' => [
            'venue_name' => 'venue_name',
            'venue_address' => 'venue_address',
            'city' => 'city',
            'country' => 'country',
        ],
        'Event Settings' => [
            'max_participants' => 'max_participants',
            'registration_fee' => 'registration_fee',
            'payment_deadline' => 'payment_deadline',
            'featured_image' => 'featured_image',
            'delivery_mode' => 'delivery_mode',
        ],
        'F2F Fields' => [
            'f2f_paper_deadline' => 'f2f_paper_deadline',
            'f2f_acceptance_date' => 'f2f_acceptance_date',
            'f2f_jury_registration_deadline' => 'f2f_jury_registration_deadline',
            'f2f_extended_paper_deadlines' => 'f2f_extended_paper_deadlines',
            'f2f_extended_acceptance_dates' => 'f2f_extended_acceptance_dates',
            'f2f_extension_count' => 'f2f_extension_count',
            'f2f_start_date' => 'f2f_start_date',
            'f2f_start_time' => 'f2f_start_time',
            'f2f_end_date' => 'f2f_end_date',
            'f2f_end_time' => 'f2f_end_time',
        ],
        'Online Fields' => [
            'online_paper_deadline' => 'online_paper_deadline',
            'online_acceptance_date' => 'online_acceptance_date',
            'online_jury_registration_deadline' => 'online_jury_registration_deadline',
            'online_extended_paper_deadlines' => 'online_extended_paper_deadlines',
            'online_extended_acceptance_dates' => 'online_extended_acceptance_dates',
            'online_extension_count' => 'online_extension_count',
            'online_start_date' => 'online_start_date',
            'online_start_time' => 'online_start_time',
            'online_end_date' => 'online_end_date',
            'online_end_time' => 'online_end_time',
            'online_platform_url' => 'online_platform_url',
        ],
    ];
    
    foreach ($fields as $section => $sectionFields) {
        echo "--- $section ---\n";
        foreach ($sectionFields as $formField => $dbColumn) {
            $value = $innovationEvent->$dbColumn ?? 'NULL';
            $hasData = $innovationEvent->$dbColumn ? '✅ YES' : '❌ NO';
            
            if (is_array($value)) {
                $value = json_encode($value);
            }
            
            $valueStr = strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value;
            
            echo sprintf("  %-35s → %-35s [%s] %s\n", 
                $formField, 
                $dbColumn, 
                $hasData,
                $valueStr
            );
        }
        echo "\n";
    }
    
} else {
    echo "No innovation event found.\n";
}

echo "\n=== CHECKING COLUMN EXISTENCE IN DATABASE ===\n\n";

$columns = DB::select("
    SELECT column_name 
    FROM information_schema.columns 
    WHERE table_name = 'events' 
    AND (
        column_name LIKE '%innovation%'
        OR column_name LIKE '%jury%'
        OR column_name LIKE '%extended%'
        OR column_name LIKE '%extension%'
        OR column_name = 'delivery_mode'
        OR column_name = 'payment_deadline'
    )
    ORDER BY column_name
");

echo "Innovation-related columns in database:\n";
foreach ($columns as $column) {
    echo "  - " . $column->column_name . "\n";
}
