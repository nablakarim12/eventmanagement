<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$email = 'nabilaabkarim41200@gmail.com';
$newPassword = 'Nabila@123'; // Must match the strong password requirements

echo "Resetting password for: $email\n";

$organizer = DB::table('event_organizers')
    ->where('org_email', $email)
    ->first();

if (!$organizer) {
    echo "ERROR: Organizer not found!\n";
    exit;
}

echo "Found organizer: {$organizer->org_name}\n";

$hashedPassword = Hash::make($newPassword);

DB::table('event_organizers')
    ->where('org_email', $email)
    ->update(['password' => $hashedPassword]);

echo "\n✓ Password reset successfully!\n";
echo "New credentials:\n";
echo "Email: $email\n";
echo "Password: $newPassword\n";
