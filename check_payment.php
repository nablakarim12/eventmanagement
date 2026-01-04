<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$reg = App\Models\EventRegistration::find(20);

echo "Registration ID: 20 (Aisyah)\n";
echo "payment_receipt_path: " . ($reg->payment_receipt_path ?? 'NULL') . "\n";
echo "payment_status: " . ($reg->payment_status ?? 'NULL') . "\n";
echo "payment_submitted_at: " . ($reg->payment_submitted_at ?? 'NULL') . "\n";
echo "payment_approved_at: " . ($reg->payment_approved_at ?? 'NULL') . "\n";

if ($reg->payment_receipt_path) {
    $fullPath = storage_path('app/public/' . $reg->payment_receipt_path);
    echo "\nFull path: " . $fullPath . "\n";
    echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    
    $assetPath = asset('storage/' . $reg->payment_receipt_path);
    echo "Asset URL: " . $assetPath . "\n";
}
