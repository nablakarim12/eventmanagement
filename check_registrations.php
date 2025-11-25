<?php
// Check registration data structure
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$registrations = App\Models\EventRegistration::with(['user', 'event'])->take(3)->get();

foreach ($registrations as $reg) {
    echo "Registration ID: {$reg->id}\n";
    echo "User: {$reg->user->name} ({$reg->user->email})\n";
    echo "Event: {$reg->event->title}\n";
    echo "Status: {$reg->status}\n";
    echo "Registration Data: " . json_encode($reg->registration_data, JSON_PRETTY_PRINT) . "\n";
    echo "---\n";
}
?>