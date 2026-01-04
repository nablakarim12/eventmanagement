<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$event = App\Models\Event::find(4);

echo "Event ID: 4 - " . $event->title . "\n\n";

// Get all date-related columns
$dates = [
    'f2f_registration_deadline',
    'f2f_jury_registration_deadline',
    'f2f_submission_deadline',
    'f2f_acceptance_notification_date',
    'f2f_payment_deadline',
    'f2f_event_start',
    'f2f_event_end',
    
    'f2f_extended_registration_deadline',
    'f2f_extended_jury_deadline',
    'f2f_extended_submission_deadline',
    'f2f_extended_notification_date',
    'f2f_extended_payment_deadline',
];

echo "=== All F2F Deadline Fields ===\n";
foreach ($dates as $date) {
    $value = $event->$date ?? 'NULL';
    echo "$date: $value\n";
}
