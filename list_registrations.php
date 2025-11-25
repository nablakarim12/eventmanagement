<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n========================================\n";
echo "EVENT REGISTRATIONS (Latest First)\n";
echo "========================================\n\n";

$registrations = DB::table('event_registrations')
    ->join('users', 'event_registrations.user_id', '=', 'users.id')
    ->join('events', 'event_registrations.event_id', '=', 'events.id')
    ->select(
        'event_registrations.id',
        'users.name as user_name',
        'users.email',
        'events.title as event_title',
        'event_registrations.role',
        'event_registrations.status',
        'event_registrations.payment_status',
        'event_registrations.approval_status',
        'event_registrations.created_at'
    )
    ->orderBy('event_registrations.created_at', 'desc')
    ->get();

if ($registrations->isEmpty()) {
    echo "No registrations found.\n";
} else {
    foreach ($registrations as $index => $r) {
        echo sprintf(
            "[%d] ID: %s\n" .
            "    User: %s (%s)\n" .
            "    Event: %s\n" .
            "    Role: %s | Status: %s | Payment: %s | Approval: %s\n" .
            "    Registered: %s\n\n",
            $index + 1,
            $r->id,
            $r->user_name,
            $r->email,
            $r->event_title,
            strtoupper($r->role),
            strtoupper($r->status),
            strtoupper($r->payment_status ?? 'N/A'),
            strtoupper($r->approval_status ?? 'N/A'),
            $r->created_at
        );
    }
    
    echo "========================================\n";
    echo "Total Registrations: " . $registrations->count() . "\n";
    echo "========================================\n";
}
