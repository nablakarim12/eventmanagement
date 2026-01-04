<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$registration = \App\Models\EventRegistration::find(14);

echo "Registration ID: 14\n";
echo "Selected Category: " . ($registration->selected_category ?? 'NULL') . "\n";
echo "Registration Data: " . json_encode($registration->registration_data, JSON_PRETTY_PRINT) . "\n";
echo "\n";

// Check all registrations for this event
echo "All registrations for event {$registration->event_id}:\n";
$allRegs = \App\Models\EventRegistration::where('event_id', $registration->event_id)->get();
foreach ($allRegs as $reg) {
    echo "\n--- Registration {$reg->id} ---\n";
    echo "User: {$reg->user->name}\n";
    echo "Role: {$reg->role}\n";
    echo "Selected Category: " . ($reg->selected_category ?? 'NULL') . "\n";
    echo "Registration Data: " . json_encode($reg->registration_data, JSON_PRETTY_PRINT) . "\n";
}
