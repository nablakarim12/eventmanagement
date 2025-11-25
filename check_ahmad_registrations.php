<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n========================================\n";
echo "ALL REGISTRATIONS FOR AHMAD MASLAN\n";
echo "========================================\n\n";

$registrations = DB::table('event_registrations')
    ->join('users', 'event_registrations.user_id', '=', 'users.id')
    ->where('users.email', 'hariterbukaperpaduan@gmail.com')
    ->select('event_registrations.*')
    ->orderBy('event_registrations.created_at', 'desc')
    ->get();

foreach ($registrations as $reg) {
    echo "ID: {$reg->id}\n";
    echo "Role: {$reg->role}\n";
    echo "Status: {$reg->status}\n";
    echo "Created: {$reg->created_at}\n";
    echo "Jury Summary: " . ($reg->jury_qualification_summary ?? 'NULL') . "\n";
    echo "Jury Documents: " . ($reg->jury_qualification_documents ?? 'NULL') . "\n";
    echo "Institution: " . ($reg->jury_institution ?? 'NULL') . "\n";
    echo "----------------------------------------\n\n";
}

echo "Total: " . $registrations->count() . " registrations\n";
