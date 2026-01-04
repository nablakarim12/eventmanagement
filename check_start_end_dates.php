<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$event = App\Models\Event::find(4);

echo "Event ID: 4 - " . $event->title . "\n\n";

// Check start and end dates
echo "=== Event Start/End Dates ===\n";
echo "start_date: " . ($event->start_date ?? 'NULL') . "\n";
echo "end_date: " . ($event->end_date ?? 'NULL') . "\n";
echo "event_start_date: " . ($event->event_start_date ?? 'NULL') . "\n";
echo "event_end_date: " . ($event->event_end_date ?? 'NULL') . "\n";
echo "f2f_event_start: " . ($event->f2f_event_start ?? 'NULL') . "\n";
echo "f2f_event_end: " . ($event->f2f_event_end ?? 'NULL') . "\n";
