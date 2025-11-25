<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n========================================\n";
echo "JURY REGISTRATION DATA - ID #9\n";
echo "========================================\n\n";

$reg = DB::table('event_registrations')->where('id', 9)->first();

if ($reg) {
    echo "User ID: {$reg->user_id}\n";
    echo "Event ID: {$reg->event_id}\n";
    echo "Role: {$reg->role}\n\n";
    
    echo "--- Jury Qualification Fields ---\n";
    echo "Summary: " . ($reg->jury_qualification_summary ?? 'NULL') . "\n";
    echo "Experience: " . ($reg->jury_experience ?? 'NULL') . "\n";
    echo "Expertise Areas: " . ($reg->jury_expertise_areas ?? 'NULL') . "\n";
    echo "Institution: " . ($reg->jury_institution ?? 'NULL') . "\n";
    echo "Position: " . ($reg->jury_position ?? 'NULL') . "\n";
    echo "Years Experience: " . ($reg->jury_years_experience ?? 'NULL') . "\n";
    echo "Documents (JSON): " . ($reg->jury_qualification_documents ?? 'NULL') . "\n";
    echo "Approval Status: " . ($reg->approval_status ?? 'NULL') . "\n";
} else {
    echo "Registration not found!\n";
}
