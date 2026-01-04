<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Event;

echo "=== Checking Conference Event Columns ===\n\n";

// Check if columns exist in database
$columns = DB::select("
    SELECT column_name 
    FROM information_schema.columns 
    WHERE table_name = 'events' 
    AND column_name LIKE '%platform%'
    OR column_name LIKE '%conference%'
    OR column_name LIKE '%reviewer%'
    OR column_name LIKE '%review_%'
    OR column_name LIKE '%payment%'
    ORDER BY column_name
");

echo "Columns found in database:\n";
foreach ($columns as $column) {
    echo "  - " . $column->column_name . "\n";
}

echo "\n=== Checking Conference Event Data ===\n\n";

// Get the first conference event
$event = Event::whereNotNull('delivery_mode')
    ->whereIn('delivery_mode', ['face_to_face', 'online', 'hybrid'])
    ->first();

if ($event) {
    echo "Conference Event Found: {$event->title}\n";
    echo "Delivery Mode: {$event->delivery_mode}\n\n";
    
    echo "F2F Fields:\n";
    echo "  - f2f_reviewer_registration_deadline: " . ($event->f2f_reviewer_registration_deadline ?? 'NULL') . "\n";
    echo "  - f2f_paper_deadline: " . ($event->f2f_paper_deadline ?? 'NULL') . "\n";
    echo "  - f2f_review_deadline: " . ($event->f2f_review_deadline ?? 'NULL') . "\n";
    echo "  - f2f_acceptance_date: " . ($event->f2f_acceptance_date ?? 'NULL') . "\n";
    echo "  - f2f_payment_deadline: " . ($event->f2f_payment_deadline ?? 'NULL') . "\n";
    
    echo "\nOnline Fields:\n";
    echo "  - online_reviewer_registration_deadline: " . ($event->online_reviewer_registration_deadline ?? 'NULL') . "\n";
    echo "  - online_paper_deadline: " . ($event->online_paper_deadline ?? 'NULL') . "\n";
    echo "  - online_review_deadline: " . ($event->online_review_deadline ?? 'NULL') . "\n";
    echo "  - online_acceptance_date: " . ($event->online_acceptance_date ?? 'NULL') . "\n";
    echo "  - online_payment_deadline: " . ($event->online_payment_deadline ?? 'NULL') . "\n";
    echo "  - online_platform_url: " . ($event->online_platform_url ?? 'NULL') . "\n";
    
    // Check if online_platform_link exists
    try {
        echo "  - online_platform_link: " . ($event->online_platform_link ?? 'NULL') . "\n";
    } catch (\Exception $e) {
        echo "  - online_platform_link: Column doesn't exist\n";
    }
    
    echo "\nConference Categories:\n";
    echo "  - conference_categories: " . json_encode($event->conference_categories) . "\n";
    
} else {
    echo "No conference event found in database.\n";
}

echo "\n=== Model Fillable Check ===\n\n";
$eventModel = new Event();
$fillable = $eventModel->getFillable();
$conferenceFields = array_filter($fillable, function($field) {
    return str_contains($field, 'conference') || 
           str_contains($field, 'reviewer') || 
           str_contains($field, 'review_') ||
           str_contains($field, 'platform');
});

echo "Conference-related fields in Model fillable:\n";
foreach ($conferenceFields as $field) {
    echo "  - $field\n";
}
