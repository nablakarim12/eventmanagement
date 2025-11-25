<?php

require_once 'vendor/autoload.php';

// Laravel Bootstrap
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== TESTING STORAGE PATHS ===\n\n";

// Test basic storage operations
try {
    echo "1. Testing Storage disk access...\n";
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    echo "   ✅ Storage disk created successfully\n";
    
    echo "2. Testing directory structure...\n";
    $eventsPath = 'events';
    $postersPath = 'events/posters';
    
    // Check if events directory exists
    if (!$disk->exists($eventsPath)) {
        echo "   📁 Creating events directory...\n";
        $disk->makeDirectory($eventsPath);
    }
    echo "   ✅ Events directory exists\n";
    
    // Check if posters directory exists
    if (!$disk->exists($postersPath)) {
        echo "   📁 Creating posters directory...\n";
        $disk->makeDirectory($postersPath);
    }
    echo "   ✅ Posters directory exists\n";
    
    echo "3. Testing file operations...\n";
    $testContent = 'test content';
    $testPath = 'events/posters/test_' . time() . '.txt';
    
    // Test putting a file
    $result = $disk->put($testPath, $testContent);
    if ($result) {
        echo "   ✅ Test file created: {$testPath}\n";
        
        // Test reading the file
        $content = $disk->get($testPath);
        echo "   ✅ Test file read successfully\n";
        
        // Test deleting the file
        $deleted = $disk->delete($testPath);
        echo "   ✅ Test file deleted: " . ($deleted ? 'YES' : 'NO') . "\n";
    } else {
        echo "   ❌ Failed to create test file\n";
    }
    
    echo "4. Checking storage paths...\n";
    echo "   Storage path: " . storage_path('app/public') . "\n";
    echo "   Public path: " . public_path('storage') . "\n";
    echo "   Events path exists: " . (file_exists(storage_path('app/public/events')) ? 'YES' : 'NO') . "\n";
    echo "   Posters path exists: " . (file_exists(storage_path('app/public/events/posters')) ? 'YES' : 'NO') . "\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== STORAGE TEST COMPLETE ===\n";