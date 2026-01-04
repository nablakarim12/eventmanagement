<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "Choose an option:\n";
echo "1. Delete META UPSI organizer (so you can re-register with documents)\n";
echo "2. Keep the organizer (approve manually)\n\n";
echo "Enter choice (1 or 2): ";

$handle = fopen ("php://stdin","r");
$choice = trim(fgets($handle));

if ($choice == "1") {
    try {
        $deleted = DB::table('event_organizers')->where('org_name', 'META UPSI')->delete();
        if ($deleted) {
            echo "\n✓ Organizer deleted successfully!\n";
            echo "You can now re-register with document uploads.\n\n";
        } else {
            echo "\n❌ Organizer not found or already deleted.\n\n";
        }
    } catch (\Exception $e) {
        echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "\nOrganizer kept. You can approve them from the admin panel.\n\n";
}
