<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EventRegistration;

echo "=== Updating Test Registration with Selected Category ===\n\n";

// Find the test registration REG-12888820
$registration = EventRegistration::where('registration_code', 'REG-12888820')->first();

if (!$registration) {
    echo "Registration REG-12888820 not found.\n";
    exit;
}

echo "Found Registration: {$registration->registration_code}\n";
echo "User: {$registration->user->name}\n";
echo "Event: {$registration->event->title}\n\n";

if ($registration->event->conference_categories && count($registration->event->conference_categories) > 0) {
    echo "Available Categories:\n";
    foreach ($registration->event->conference_categories as $index => $category) {
        echo "  " . ($index + 1) . ". $category\n";
    }
    
    // Set the selected category to the first one for this test user
    $selectedCategory = $registration->event->conference_categories[0];
    
    $registration->selected_category = $selectedCategory;
    $registration->save();
    
    echo "\n✅ Updated registration with selected category: $selectedCategory\n";
} else {
    echo "This event has no conference categories.\n";
}

echo "\nDone!\n";
