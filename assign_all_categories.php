<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EventRegistration;

echo "=== Assigning Selected Categories to All Conference Registrations ===\n\n";

$registrations = EventRegistration::with(['user', 'event'])
    ->whereHas('event', function($q) {
        $q->whereNotNull('conference_categories');
    })
    ->whereNull('selected_category')
    ->get();

echo "Found {$registrations->count()} registrations without selected category\n\n";

$updated = 0;

foreach ($registrations as $registration) {
    if ($registration->event->conference_categories && count($registration->event->conference_categories) > 0) {
        // Assign a random category from the event's available categories
        $categories = $registration->event->conference_categories;
        $randomIndex = array_rand($categories);
        $selectedCategory = $categories[$randomIndex];
        
        $registration->selected_category = $selectedCategory;
        $registration->save();
        
        echo "✅ {$registration->user->name} ({$registration->registration_code})\n";
        echo "   Event: {$registration->event->title}\n";
        echo "   Selected: {$selectedCategory}\n\n";
        
        $updated++;
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ Updated {$updated} registrations with selected categories\n";
echo str_repeat("=", 60) . "\n";
