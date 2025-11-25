<?php

require_once 'vendor/autoload.php';

use App\Models\Event;

// Laravel Bootstrap
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== DEBUGGING EVENT UPDATE ISSUE ===\n\n";

// Get the event that's causing issues (assuming it's ID 18 from the URL)
$eventId = 18;
$event = Event::find($eventId);

if (!$event) {
    echo "❌ Event with ID {$eventId} not found!\n";
    exit(1);
}

echo "✅ Found event:\n";
echo "ID: {$event->id}\n";
echo "Title: {$event->title}\n";
echo "Slug: {$event->slug}\n";
echo "Featured Image: " . ($event->featured_image ?: 'null') . "\n";
echo "Organizer ID: {$event->organizer_id}\n";
echo "Status: {$event->status}\n\n";

// Test slug generation
echo "Testing slug generation:\n";
$testTitle = "Test Event Title";
$slug = \Illuminate\Support\Str::slug($testTitle);
echo "Input: '{$testTitle}' → Slug: '{$slug}'\n\n";

// Check if there are any null/empty fields that might cause issues
$fillableFields = [
    'title', 'description', 'venue_name', 'venue_address', 
    'city', 'country', 'start_date', 'end_date', 'start_time', 'end_time'
];

echo "Checking required fields:\n";
foreach ($fillableFields as $field) {
    $value = $event->{$field};
    $status = empty($value) ? '❌ EMPTY' : '✅ OK';
    echo "- {$field}: {$status} (" . (is_null($value) ? 'null' : (is_string($value) ? "'{$value}'" : $value)) . ")\n";
}

echo "\n=== DEBUG COMPLETE ===\n";