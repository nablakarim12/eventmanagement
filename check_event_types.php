<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;

echo "Checking all events type classification:\n";
echo str_repeat("=", 80) . "\n";

$events = Event::with('category')->get();

foreach ($events as $event) {
    echo sprintf(
        "ID: %d | Title: %s\n",
        $event->id,
        $event->title
    );
    echo sprintf(
        "  Category: %s\n",
        $event->category ? $event->category->name : 'NULL'
    );
    echo sprintf(
        "  Delivery Mode: %s\n",
        $event->delivery_mode ?? 'NULL'
    );
    echo sprintf(
        "  Event Type: %s\n",
        $event->event_type ?? 'NULL'
    );
    echo str_repeat("-", 80) . "\n";
}
