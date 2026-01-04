<?php

/**
 * TEST CLOUDINARY UPLOAD
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

echo "\n";
echo "========================================\n";
echo "  TESTING CLOUDINARY CONNECTION\n";
echo "========================================\n\n";

try {
    echo "Cloudinary Config:\n";
    echo "Cloud Name: " . config('cloudinary.cloud_name') . "\n";
    echo "API Key: " . config('cloudinary.api_key') . "\n\n";
    
    echo "Testing upload (creating a test file)...\n";
    
    // Create a test PDF file
    $testContent = "%PDF-1.4\nTest Document";
    $testFile = storage_path('app/test_document.pdf');
    file_put_contents($testFile, $testContent);
    
    echo "Test file created: $testFile\n";
    echo "Uploading to Cloudinary...\n";
    
    $result = Cloudinary::upload($testFile, [
        'folder' => 'organizer-documents',
        'resource_type' => 'raw',
        'public_id' => 'test_' . time(),
    ]);
    
    echo "\n✓ Upload successful!\n";
    echo "Secure URL: " . $result->getSecurePath() . "\n";
    echo "Public ID: " . $result->getPublicId() . "\n";
    echo "Resource Type: " . $result->getFileType() . "\n";
    
    // Clean up
    unlink($testFile);
    echo "\nTest file deleted.\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
