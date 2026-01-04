<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GeneratedCertificate;

$deleted = GeneratedCertificate::where('event_id', 2)->delete();

echo "Deleted {$deleted} certificates for event ID 2\n";
