<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n========================================\n";
echo "ALL JURY REGISTRATIONS\n";
echo "========================================\n\n";

$registrations = DB::table('event_registrations')
    ->where('role', 'jury')
    ->orWhere('role', 'both')
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($registrations as $reg) {
    echo "ID: {$reg->id}\n";
    echo "User ID: {$reg->user_id}\n";
    echo "Role: {$reg->role}\n";
    echo "Created: {$reg->created_at}\n";
    echo "Certificate Path: " . ($reg->certificate_path ?? 'NULL') . "\n";
    echo "Certificate Filename: " . ($reg->certificate_filename ?? 'NULL') . "\n";
    echo "Jury Documents: " . ($reg->jury_qualification_documents ?? 'NULL') . "\n";
    echo "----------------------------------------\n\n";
}

echo "Total: " . $registrations->count() . " registrations\n";
