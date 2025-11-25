<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$reg = DB::table('event_registrations')->where('id', 5)->first(['certificate_path', 'certificate_filename']);

echo "Path: {$reg->certificate_path}\n";
echo "Filename: {$reg->certificate_filename}\n";
