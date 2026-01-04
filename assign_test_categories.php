<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EventRegistration;

echo "=== Updating Registration with Selected Category ===\n\n";

// Update the first registration
$registration = EventRegistration::where('registration_code', 'REG-A9FCEF40')->first();

if ($registration && $registration->event->conference_categories) {
    $categories = $registration->event->conference_categories;
    
    // Assign different categories to different users for testing
    $registration->selected_category = $categories[0]; // EdTech
    $registration->save();
    
    echo "✅ Updated {$registration->user->name} - Selected: {$registration->selected_category}\n";
}

// Update second registration
$registration2 = EventRegistration::where('registration_code', 'REG-7978E4C5')->first();
if ($registration2 && $registration2->event->conference_categories) {
    $categories = $registration2->event->conference_categories;
    $registration2->selected_category = $categories[1]; // E-Learning
    $registration2->save();
    echo "✅ Updated {$registration2->user->name} - Selected: {$registration2->selected_category}\n";
}

// Update third registration
$registration3 = EventRegistration::where('registration_code', 'REG-3F265C52')->first();
if ($registration3 && $registration3->event->conference_categories) {
    $categories = $registration3->event->conference_categories;
    $registration3->selected_category = $categories[2]; // Digital Pedagogy
    $registration3->save();
    echo "✅ Updated {$registration3->user->name} - Selected: {$registration3->selected_category}\n";
}

echo "\nDone! Refresh the registrations page to see the selected categories.\n";
