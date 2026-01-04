<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EventRegistration;

echo "=== Recent Conference Registrations ===\n\n";

$registrations = EventRegistration::with(['user', 'event'])
    ->whereHas('event', function($q) {
        $q->whereNotNull('conference_categories');
    })
    ->latest()
    ->take(5)
    ->get();

foreach ($registrations as $reg) {
    echo "Code: {$reg->registration_code}\n";
    echo "User: {$reg->user->name} ({$reg->user->email})\n";
    echo "Event: {$reg->event->title}\n";
    echo "Categories: " . implode(', ', $reg->event->conference_categories ?? []) . "\n";
    echo "Selected: " . ($reg->selected_category ?? 'None') . "\n";
    echo "---\n";
}
