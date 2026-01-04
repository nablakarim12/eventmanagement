<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Event;

echo "Checking all events delivery_mode:\n";
echo str_repeat("=", 80) . "\n";

$events = Event::select('id', 'title', 'delivery_mode')->get();

foreach ($events as $event) {
    echo sprintf(
        "ID: %d | Title: %s | Delivery Mode: %s\n",
        $event->id,
        $event->title,
        $event->delivery_mode ?? 'NULL'
    );
}

echo str_repeat("=", 80) . "\n";
