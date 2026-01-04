<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\EventOrganizer;

try {
    $organizer = EventOrganizer::where('org_name', 'META UPSI')->first();
    
    if ($organizer) {
        $organizer->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1, // Admin ID
        ]);
        
        echo "\n✓ META UPSI has been approved!\n";
        echo "  You can now login with:\n";
        echo "  Email: {$organizer->org_email}\n";
        echo "  Status: " . $organizer->status . "\n\n";
        echo "Test document upload with a NEW organizer registration.\n\n";
    } else {
        echo "\n❌ Organizer not found\n";
    }
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}
