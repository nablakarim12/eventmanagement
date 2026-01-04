<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EventOrganizer;
use App\Models\OrganizerDocument;

echo "\n";
echo "========================================\n";
echo "  CHECKING ORGANIZER DOCUMENTS\n";
echo "========================================\n\n";

try {
    // Get the META UPSI organizer
    $organizer = EventOrganizer::where('org_name', 'META UPSI')->first();
    
    if (!$organizer) {
        echo "❌ Organizer 'META UPSI' not found\n";
        exit;
    }
    
    echo "✓ Found organizer: {$organizer->org_name}\n";
    echo "  ID: {$organizer->id}\n";
    echo "  Email: {$organizer->org_email}\n\n";
    
    // Check documents in database
    echo "Checking organizer_documents table:\n";
    echo "------------------------------------\n";
    
    $allDocs = DB::table('organizer_documents')->get();
    echo "Total documents in table: " . $allDocs->count() . "\n\n";
    
    if ($allDocs->count() > 0) {
        foreach ($allDocs as $doc) {
            echo "Document ID: {$doc->id}\n";
            echo "  event_organizer_id: {$doc->event_organizer_id}\n";
            echo "  file_path: {$doc->file_path}\n";
            echo "  original_name: {$doc->original_name}\n";
            echo "  document_type: {$doc->document_type}\n";
            echo "  created_at: {$doc->created_at}\n\n";
        }
    }
    
    // Check using relationship
    echo "Checking using relationship:\n";
    echo "------------------------------------\n";
    $docs = $organizer->documents;
    echo "Documents count via relationship: " . $docs->count() . "\n\n";
    
    if ($docs->count() > 0) {
        foreach ($docs as $doc) {
            echo "✓ Document: {$doc->original_name}\n";
            echo "  Path: {$doc->file_path}\n";
        }
    } else {
        echo "❌ No documents found via relationship\n";
        echo "\nDEBUG INFO:\n";
        echo "Organizer ID: {$organizer->id}\n";
        echo "Looking for documents with event_organizer_id = {$organizer->id}\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
