<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$event = App\Models\Event::find(4);

echo "Event ID: 4\n";
echo "Event Title: " . $event->title . "\n";
echo "Event Type: " . $event->event_type . "\n";
echo "Delivery Mode: " . $event->delivery_mode . "\n\n";

echo "=== Innovation Base Columns ===\n";
echo "registration_deadline: " . ($event->registration_deadline ?? 'NULL') . "\n";
echo "jury_registration_deadline: " . ($event->jury_registration_deadline ?? 'NULL') . "\n";
echo "submission_deadline: " . ($event->submission_deadline ?? 'NULL') . "\n";
echo "acceptance_notification_date: " . ($event->acceptance_notification_date ?? 'NULL') . "\n\n";

echo "=== F2F Columns ===\n";
echo "f2f_registration_deadline: " . ($event->f2f_registration_deadline ?? 'NULL') . "\n";
echo "f2f_jury_registration_deadline: " . ($event->f2f_jury_registration_deadline ?? 'NULL') . "\n";
echo "f2f_submission_deadline: " . ($event->f2f_submission_deadline ?? 'NULL') . "\n";
echo "f2f_payment_deadline: " . ($event->f2f_payment_deadline ?? 'NULL') . "\n";
echo "f2f_event_start: " . ($event->f2f_event_start ?? 'NULL') . "\n";
echo "f2f_event_end: " . ($event->f2f_event_end ?? 'NULL') . "\n\n";

echo "=== Online Columns ===\n";
echo "online_registration_deadline: " . ($event->online_registration_deadline ?? 'NULL') . "\n";
echo "online_jury_registration_deadline: " . ($event->online_jury_registration_deadline ?? 'NULL') . "\n";
echo "online_submission_deadline: " . ($event->online_submission_deadline ?? 'NULL') . "\n";
echo "online_payment_deadline: " . ($event->online_payment_deadline ?? 'NULL') . "\n";
