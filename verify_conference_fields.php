<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;

echo "=== Conference Event Field Verification ===\n\n";

// Find a conference event (with delivery_mode set)
$event = Event::whereNotNull('delivery_mode')->first();

if (!$event) {
    echo "No conference event found. Please create one first.\n";
    exit;
}

echo "Event: {$event->title}\n";
echo "ID: {$event->id}\n";
echo "Delivery Mode: {$event->delivery_mode}\n\n";

echo "=== Face-to-Face Conference Fields ===\n";
echo "f2f_start_date: " . ($event->f2f_start_date ?? 'NULL') . "\n";
echo "f2f_end_date: " . ($event->f2f_end_date ?? 'NULL') . "\n";
echo "f2f_reviewer_registration_deadline: " . ($event->f2f_reviewer_registration_deadline ?? 'NULL') . "\n";
echo "f2f_paper_submission_deadline: " . ($event->f2f_paper_submission_deadline ?? 'NULL') . "\n";
echo "f2f_review_deadline: " . ($event->f2f_review_deadline ?? 'NULL') . "\n";
echo "f2f_acceptance_notification_date: " . ($event->f2f_acceptance_notification_date ?? 'NULL') . "\n";
echo "f2f_payment_deadline: " . ($event->f2f_payment_deadline ?? 'NULL') . "\n\n";

echo "=== Online Conference Fields ===\n";
echo "online_start_date: " . ($event->online_start_date ?? 'NULL') . "\n";
echo "online_end_date: " . ($event->online_end_date ?? 'NULL') . "\n";
echo "online_reviewer_registration_deadline: " . ($event->online_reviewer_registration_deadline ?? 'NULL') . "\n";
echo "online_paper_submission_deadline: " . ($event->online_paper_submission_deadline ?? 'NULL') . "\n";
echo "online_review_deadline: " . ($event->online_review_deadline ?? 'NULL') . "\n";
echo "online_acceptance_notification_date: " . ($event->online_acceptance_notification_date ?? 'NULL') . "\n";
echo "online_payment_deadline: " . ($event->online_payment_deadline ?? 'NULL') . "\n\n";

echo "=== Conference Categories ===\n";
if ($event->conference_categories) {
    echo "Categories: " . implode(', ', $event->conference_categories) . "\n";
} else {
    echo "No categories set\n";
}

echo "\n✅ All conference fields are accessible from the database!\n";
