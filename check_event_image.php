<?php

require_once 'vendor/autoload.php';

use App\Models\Event;

// Laravel Bootstrap
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n=== CHECKING EVENT IMAGE PATH ===\n\n";

// Get event 18
$event = Event::find(18);

if (!$event) {
    echo "❌ Event 18 not found!\n";
    exit(1);
}

echo "Event ID: {$event->id}\n";
echo "Title: {$event->title}\n";
echo "Featured Image Value: " . var_export($event->featured_image, true) . "\n";
echo "Featured Image Type: " . gettype($event->featured_image) . "\n";
echo "Is Empty String: " . (($event->featured_image === '') ? 'YES' : 'NO') . "\n";
echo "Is Null: " . (is_null($event->featured_image) ? 'YES' : 'NO') . "\n";

if ($event->featured_image) {
    echo "Path Length: " . strlen($event->featured_image) . "\n";
    echo "Trimmed Path: '" . trim($event->featured_image) . "'\n";
    echo "File Exists Check: " . (\Illuminate\Support\Facades\Storage::disk('public')->exists($event->featured_image) ? 'EXISTS' : 'NOT FOUND') . "\n";
}

echo "\n=== CHECK COMPLETE ===\n";