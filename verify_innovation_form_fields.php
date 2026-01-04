<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Event;
use Illuminate\Support\Facades\Schema;

echo "====================================================\n";
echo "INNOVATION FORM FIELDS VERIFICATION\n";
echo "====================================================\n\n";

// Get all columns from events table
$dbColumns = Schema::getColumnListing('events');
echo "Total database columns: " . count($dbColumns) . "\n\n";

// Form fields used in create_innovation.blade.php
$formFields = [
    // Hidden fields
    'is_free',
    'is_public',
    'allow_waitlist',
    'requires_approval',
    'event_form_type',
    
    // Basic info
    'title',
    'category_id',
    'description',
    
    // Innovation specific
    'innovation_categories',
    'innovation_theme',
    
    // Location
    'venue_name',
    'venue_address',
    'city',
    'country',
    'max_participants',
    'registration_fee',
    'featured_image',
    
    // Delivery mode
    'delivery_mode',
    
    // F2F deadline fields
    'f2f_participant_registration_deadline',
    'f2f_jury_registration_deadline',
    'f2f_submission_deadline',
    'f2f_acceptance_notification_date',
    'f2f_extended_registration_deadline',
    'f2f_extended_jury_deadline',
    'f2f_extended_submission_deadline',
    'f2f_extended_notification_date',
    'f2f_payment_deadline_new',
    
    // F2F date/time fields
    'f2f_start_datetime',
    'f2f_end_datetime',
    'f2f_start_date',
    'f2f_end_date',
    'f2f_start_time',
    'f2f_end_time',
    
    // Online deadline fields
    'online_registration_deadline',
    'online_jury_registration_deadline_new',
    'online_submission_deadline',
    'online_acceptance_notification_date_new',
    'online_extended_registration_deadline',
    'online_extended_jury_deadline',
    'online_extended_submission_deadline',
    'online_extended_notification_date',
    'online_payment_deadline_new',
    'online_platform_url',
    
    // Online date/time fields
    'online_start_datetime',
    'online_end_datetime',
    'online_start_date',
    'online_end_date',
    'online_start_time',
    'online_end_time',
    
    // Status field
    'status',
];

// Get fillable fields from Event model
$event = new Event();
$fillableFields = $event->getFillable();

echo "=== FORM FIELD VERIFICATION ===\n\n";

$missingInDb = [];
$missingInFillable = [];
$existsInBoth = [];
$uiOnlyFields = ['f2f_start_datetime', 'f2f_end_datetime', 'online_start_datetime', 'online_end_datetime', 'event_form_type', 'f2f_participant_registration_deadline'];

foreach ($formFields as $field) {
    // Skip UI-only fields that get converted
    if (in_array($field, $uiOnlyFields)) {
        echo "✓ $field - UI field (converts to other fields)\n";
        continue;
    }
    
    $inDb = in_array($field, $dbColumns);
    $inFillable = in_array($field, $fillableFields);
    
    if ($inDb && $inFillable) {
        $existsInBoth[] = $field;
        echo "✓ $field - OK (in DB & fillable)\n";
    } elseif (!$inDb) {
        $missingInDb[] = $field;
        echo "✗ $field - MISSING IN DATABASE\n";
    } elseif (!$inFillable) {
        $missingInFillable[] = $field;
        echo "⚠ $field - IN DB but NOT fillable\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "✓ Fields OK: " . count($existsInBoth) . "\n";
echo "✗ Missing in DB: " . count($missingInDb) . "\n";
echo "⚠ Not fillable: " . count($missingInFillable) . "\n";

if (!empty($missingInDb)) {
    echo "\n=== MISSING IN DATABASE ===\n";
    foreach ($missingInDb as $field) {
        echo "- $field\n";
    }
}

if (!empty($missingInFillable)) {
    echo "\n=== NOT IN FILLABLE ARRAY ===\n";
    foreach ($missingInFillable as $field) {
        echo "- $field\n";
    }
}

echo "\n=== CONTROLLER VALIDATION CHECK ===\n";
echo "Checking EventController validation rules...\n\n";

// Check what fields the controller expects
$expectedByController = [
    'title', 'description', 'category_id',
    'venue_name', 'venue_address', 'city', 'country',
    'max_participants', 'registration_fee',
    'delivery_mode', 'innovation_categories',
    'f2f_start_date', 'f2f_end_date', 'f2f_start_time', 'f2f_end_time',
    'online_start_date', 'online_end_date', 'online_start_time', 'online_end_time',
];

$controllerIssues = [];
foreach ($expectedByController as $field) {
    if (!in_array($field, $fillableFields) && !in_array($field, $dbColumns)) {
        $controllerIssues[] = $field;
        echo "✗ $field - Required by controller but missing!\n";
    } else {
        echo "✓ $field - OK\n";
    }
}

echo "\n=== FINAL STATUS ===\n";
if (empty($missingInDb) && empty($controllerIssues)) {
    echo "✅ ALL FORM FIELDS ARE PROPERLY CONFIGURED!\n";
} else {
    echo "⚠️ ISSUES FOUND - See details above\n";
}

echo "\n====================================================\n";
