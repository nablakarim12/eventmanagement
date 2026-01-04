<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SimpleCertificateTemplate;

$template = SimpleCertificateTemplate::where('event_id', 2)
    ->where('certificate_type', 'participant')
    ->first();

if ($template) {
    echo "=== Certificate Template for Event 2 ===\n\n";
    echo "Template Design: {$template->template_design}\n";
    echo "Use Custom Template: " . ($template->use_custom_template ? 'YES' : 'NO') . "\n";
    echo "Use AI Generation: " . ($template->use_ai_generation ? 'YES' : 'NO') . "\n";
    echo "Custom Template URL: " . ($template->custom_template_url ?? 'NULL') . "\n";
    echo "Template File URL: " . ($template->template_file_url ?? 'NULL') . "\n";
    echo "Template File Type: " . ($template->template_file_type ?? 'NULL') . "\n";
    echo "Generation Prompt: " . ($template->generation_prompt ?? 'NULL') . "\n";
} else {
    echo "No template found for event 2\n";
}
