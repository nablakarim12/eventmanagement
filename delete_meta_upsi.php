<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n";
echo "========================================\n";
echo "  DELETING META UPSI ORGANIZER\n";
echo "========================================\n\n";

try {
    $deleted = DB::table('event_organizers')->where('org_name', 'META UPSI')->delete();
    
    if ($deleted) {
        echo "✓ META UPSI has been deleted successfully!\n\n";
        echo "You can now re-register at:\n";
        echo "http://eventmanagement.test/organizer/register\n\n";
        echo "Remember to upload documents during registration.\n\n";
    } else {
        echo "❌ Organizer 'META UPSI' not found.\n\n";
    }
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
}
