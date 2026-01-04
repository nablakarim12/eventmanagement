<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$event = App\Models\Event::find(4);

echo "Before update:\n";
echo "f2f_registration_deadline: " . ($event->f2f_registration_deadline ?? 'NULL') . "\n";
echo "f2f_payment_deadline: " . ($event->f2f_payment_deadline ?? 'NULL') . "\n\n";

// Try to update
$event->f2f_registration_deadline = '2026-01-04 23:59:00';
$event->f2f_payment_deadline = '2026-01-08 23:59:00';
$event->save();

echo "After update:\n";
$event = App\Models\Event::find(4);
echo "f2f_registration_deadline: " . ($event->f2f_registration_deadline ?? 'NULL') . "\n";
echo "f2f_payment_deadline: " . ($event->f2f_payment_deadline ?? 'NULL') . "\n";
