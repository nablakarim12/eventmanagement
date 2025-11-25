<?php

require_once 'vendor/autoload.php';

use App\Models\Event;

// Laravel Bootstrap
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== CLEANING UP INVALID IMAGE PATHS ===\n\n";

// Find all events with featured_image that don't exist in storage
$eventsWithImages = Event::whereNotNull('featured_image')
    ->where('featured_image', '!=', '')
    ->get();

echo "Found " . $eventsWithImages->count() . " events with image paths.\n\n";

$fixed = 0;

foreach ($eventsWithImages as $event) {
    echo "Checking Event ID {$event->id}: {$event->title}\n";
    echo "  Image path: {$event->featured_image}\n";
    
    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($event->featured_image)) {
        echo "  ❌ File not found - clearing path\n";
        $event->update(['featured_image' => null]);
        $fixed++;
    } else {
        echo "  ✅ File exists\n";
    }
    echo "\n";
}

echo "🔧 Fixed {$fixed} events with invalid image paths.\n";
echo "✅ Database cleanup complete!\n\n";

echo "=== CLEANUP COMPLETE ===\n";