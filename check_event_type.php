<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;

$event = Event::find(24);

echo "=== Event Attributes Analysis ===\n\n";
echo "Event ID: " . $event->id . "\n";
echo "Event Title: " . $event->title . "\n\n";

echo "All Attributes:\n";
echo "================\n";
foreach ($event->getAttributes() as $key => $value) {
    if (is_null($value)) {
        echo "$key: NULL\n";
    } else {
        echo "$key: $value\n";
    }
}
