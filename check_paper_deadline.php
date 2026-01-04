<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Event;

$event = Event::whereNotNull('delivery_mode')
    ->whereIn('delivery_mode', ['face_to_face', 'online', 'hybrid'])
    ->first();

if ($event) {
    echo "=== Conference Event: {$event->title} ===\n\n";
    
    echo "F2F PAPER DEADLINE DATA:\n";
    echo "  - f2f_paper_deadline (formatted): " . ($event->f2f_paper_deadline ?? 'NULL') . "\n";
    echo "  - f2f_paper_deadline (raw from DB): " . ($event->getRawOriginal('f2f_paper_deadline') ?? 'NULL') . "\n";
    
    echo "\nONLINE PAPER DEADLINE DATA:\n";
    echo "  - online_paper_deadline (formatted): " . ($event->online_paper_deadline ?? 'NULL') . "\n";
    echo "  - online_paper_deadline (raw from DB): " . ($event->getRawOriginal('online_paper_deadline') ?? 'NULL') . "\n";
    
    echo "\n=== CHECKING IF DATETIME-LOCAL FORMAT WORKS ===\n\n";
    
    if ($event->f2f_paper_deadline) {
        $f2fFormatted = \Carbon\Carbon::parse($event->f2f_paper_deadline)->format('Y-m-d\TH:i');
        echo "F2F formatted for datetime-local input: {$f2fFormatted}\n";
    } else {
        echo "F2F Paper Deadline: No data in database\n";
    }
    
    if ($event->online_paper_deadline) {
        $onlineFormatted = \Carbon\Carbon::parse($event->online_paper_deadline)->format('Y-m-d\TH:i');
        echo "Online formatted for datetime-local input: {$onlineFormatted}\n";
    } else {
        echo "Online Paper Deadline: No data in database\n";
    }
    
    echo "\n=== ALL DEADLINE FIELDS ===\n\n";
    $deadlineFields = [
        'f2f_reviewer_registration_deadline',
        'f2f_paper_deadline',
        'f2f_review_deadline',
        'f2f_acceptance_date',
        'f2f_payment_deadline',
        'online_reviewer_registration_deadline',
        'online_paper_deadline',
        'online_review_deadline',
        'online_acceptance_date',
        'online_payment_deadline',
    ];
    
    foreach ($deadlineFields as $field) {
        $value = $event->$field ?? 'NULL';
        $raw = $event->getRawOriginal($field) ?? 'NULL';
        echo sprintf("%-40s: %s (raw: %s)\n", $field, $value, $raw);
    }
}
