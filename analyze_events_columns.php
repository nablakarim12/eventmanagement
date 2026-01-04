<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "\n=== EVENTS TABLE COLUMN ANALYSIS ===\n\n";

// Get all columns
$columns = Schema::getColumnListing('events');

echo "Total Columns: " . count($columns) . "\n\n";

// Categorize columns
$sharedColumns = [
    'id', 'organizer_id', 'category_id', 'title', 'description', 'short_description',
    'venue_name', 'venue_address', 'city', 'state', 'country', 'latitude', 'longitude',
    'max_participants', 'current_participants', 'registration_fee', 'is_free', 'currency',
    'status', 'requires_approval', 'is_public', 'allow_waitlist',
    'requirements', 'tags', 'contact_email', 'contact_phone', 'website_url',
    'slug', 'featured_image', 'gallery_images', 'created_at', 'updated_at',
    'views', 'budget', 'min_attendance_hours', 'auto_generate_certificates', 'requires_attendance',
    'registration_deadline', 'payment_deadline'
];

$innovationColumns = [
    'delivery_mode', 'innovation_categories',
    // F2F Innovation
    'f2f_start_date', 'f2f_end_date', 'f2f_start_time', 'f2f_end_time',
    'f2f_paper_deadline', 'f2f_product_deadline', 'f2f_abstract_deadline', 'f2f_acceptance_date',
    'f2f_jury_deadline', 'f2f_jury_registration_deadline', 'f2f_extension_count', 
    'f2f_extended_jury_deadlines', 'f2f_extended_acceptance_dates', 'f2f_extended_paper_deadlines',
    'f2f_payment_deadline',
    // Online Innovation
    'online_start_date', 'online_end_date', 'online_start_time', 'online_end_time',
    'online_platform_url', 'online_paper_deadline', 'online_product_deadline', 
    'online_abstract_deadline', 'online_acceptance_date',
    'online_jury_deadline', 'online_jury_registration_deadline', 'online_extension_count', 
    'online_extended_jury_deadlines', 'online_extended_acceptance_dates', 'online_extended_paper_deadlines',
    'online_payment_deadline'
];

$conferenceColumns = [
    'delivery_mode', 'conference_categories',
    // F2F Conference
    'f2f_start_date', 'f2f_end_date', 'f2f_start_time', 'f2f_end_time',
    'f2f_reviewer_registration_deadline', 'f2f_paper_submission_deadline',
    'f2f_review_deadline', 'f2f_acceptance_notification_date',
    'f2f_payment_deadline',
    // Online Conference
    'online_start_date', 'online_end_date', 'online_start_time', 'online_end_time',
    'online_platform_url', 'online_reviewer_registration_deadline', 'online_paper_submission_deadline',
    'online_review_deadline', 'online_acceptance_notification_date',
    'online_payment_deadline',
    // Conference settings
    'min_abstract_words', 'min_keywords', 'max_paper_size_mb', 'paper_format_guidelines',
    'allow_multiple_submissions', 'min_reviewers_per_paper'
];

$legacyColumns = [
    // All legacy columns have been removed or migrated
];

echo "=== COLUMN CATEGORIZATION ===\n\n";

echo "SHARED COLUMNS (Used by all event types):\n";
echo str_repeat("-", 80) . "\n";
foreach ($sharedColumns as $col) {
    if (in_array($col, $columns)) {
        echo "✓ $col\n";
    }
}

echo "\n\nINNOVATION-SPECIFIC COLUMNS:\n";
echo str_repeat("-", 80) . "\n";
foreach ($innovationColumns as $col) {
    if (in_array($col, $columns)) {
        echo "✓ $col\n";
    }
}

echo "\n\nCONFERENCE-SPECIFIC COLUMNS:\n";
echo str_repeat("-", 80) . "\n";
foreach ($conferenceColumns as $col) {
    if (in_array($col, $columns)) {
        echo "✓ $col\n";
    }
}

echo "\n\nLEGACY/DEPRECATED COLUMNS (May need removal):\n";
echo str_repeat("-", 80) . "\n";
foreach ($legacyColumns as $col) {
    if (in_array($col, $columns)) {
        echo "⚠ $col";
        
        // Check if column has data
        $count = DB::table('events')->whereNotNull($col)->count();
        if ($count > 0) {
            echo " - HAS DATA ($count records)";
        } else {
            echo " - EMPTY (safe to remove)";
        }
        echo "\n";
    }
}

// Find uncategorized columns
$allCategorized = array_merge($sharedColumns, $innovationColumns, $conferenceColumns, $legacyColumns);
$uncategorized = array_diff($columns, $allCategorized);

if (!empty($uncategorized)) {
    echo "\n\nUNCATEGORIZED COLUMNS:\n";
    echo str_repeat("-", 80) . "\n";
    foreach ($uncategorized as $col) {
        echo "? $col\n";
    }
}

// Check for duplicate/redundant columns
echo "\n\n=== DUPLICATE/REDUNDANT COLUMN ANALYSIS ===\n\n";

if (in_array('reviewer_registration_deadline', $columns) && 
    in_array('f2f_reviewer_registration_deadline', $columns)) {
    echo "⚠ Found duplicate reviewer registration deadline columns:\n";
    echo "  - reviewer_registration_deadline (legacy)\n";
    echo "  - f2f_reviewer_registration_deadline (new)\n";
    echo "  - online_reviewer_registration_deadline (new)\n\n";
}

if (in_array('paper_submission_deadline', $columns) && 
    in_array('f2f_paper_submission_deadline', $columns)) {
    echo "⚠ Found duplicate paper submission deadline columns:\n";
    echo "  - paper_submission_deadline (legacy)\n";
    echo "  - f2f_paper_submission_deadline (new)\n";
    echo "  - online_paper_submission_deadline (new)\n\n";
}

if (in_array('start_date', $columns) && 
    in_array('f2f_start_date', $columns)) {
    echo "⚠ Found duplicate start/end date columns:\n";
    echo "  - start_date, end_date, start_time, end_time (legacy)\n";
    echo "  - f2f_start_date, f2f_end_date, f2f_start_time, f2f_end_time (new)\n";
    echo "  - online_start_date, online_end_date, online_start_time, online_end_time (new)\n\n";
}

// Summary
echo "\n=== RECOMMENDATIONS ===\n\n";
echo "1. Remove legacy columns that are no longer used:\n";
foreach ($legacyColumns as $col) {
    if (in_array($col, $columns)) {
        $count = DB::table('events')->whereNotNull($col)->count();
        if ($count == 0) {
            echo "   - DROP: $col (empty)\n";
        }
    }
}

echo "\n2. Keep these column groups:\n";
echo "   - Shared columns (all event types)\n";
echo "   - delivery_mode (to distinguish event types)\n";
echo "   - innovation_categories (Innovation events only)\n";
echo "   - conference_categories (Conference events only)\n";
echo "   - f2f_* columns (for face-to-face mode)\n";
echo "   - online_* columns (for online mode)\n";

echo "\n\nDone!\n\n";
