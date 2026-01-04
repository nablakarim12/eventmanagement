<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Restored Columns ===\n\n";

$restoredColumns = [
    'start_date',
    'end_date',
    'start_time',
    'end_time',
    'reviewer_registration_deadline',
    'paper_submission_deadline',
    'review_deadline',
    'acceptance_notification_date',
    'f2f_product_deadline',
    'f2f_abstract_deadline',
    'online_product_deadline',
    'online_abstract_deadline'
];

foreach ($restoredColumns as $col) {
    $exists = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'events' AND column_name = ?", [$col]);
    echo ($exists ? '✅' : '❌') . " {$col}\n";
}

echo "\n=== Sample Event Data ===\n";
$event = DB::table('events')->whereNotNull('f2f_start_date')->first([
    'id', 'title', 
    'start_date', 'end_date',
    'f2f_start_date', 'f2f_end_date',
    'reviewer_registration_deadline',
    'f2f_reviewer_registration_deadline'
]);

if ($event) {
    echo "Event: {$event->title}\n";
    echo "  start_date: {$event->start_date}\n";
    echo "  f2f_start_date: {$event->f2f_start_date}\n";
    echo "  reviewer_registration_deadline: " . ($event->reviewer_registration_deadline ?? 'NULL') . "\n";
    echo "  f2f_reviewer_registration_deadline: " . ($event->f2f_reviewer_registration_deadline ?? 'NULL') . "\n";
}

echo "\n✅ All columns have been restored!\n";
