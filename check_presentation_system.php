<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PRESENTATION SELECTION SYSTEM CHECK ===\n\n";

// Check 1: Presentation columns
echo "1. Presentation columns in event_registrations:\n";
$columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'event_registrations' AND column_name LIKE 'presentation%'");
foreach($columns as $col) {
    echo "   ✓ {$col->column_name}\n";
}

// Check 2: Average score column
$avgCol = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'event_registrations' AND column_name = 'average_score'");
if ($avgCol) {
    echo "   ✓ average_score\n";
}

// Check 3: Notifications table
echo "\n2. Notifications table: ";
echo Schema::hasTable('notifications') ? "✓ EXISTS\n" : "✗ NOT FOUND (will be created on first notification)\n";

// Check 4: Sample data - participants with reviews
echo "\n3. Participants with Review Data:\n";
$event = App\Models\Event::whereNotNull('delivery_mode')->where('status', 'published')->first();

if ($event) {
    echo "   Event: {$event->title}\n\n";
    
    $participants = App\Models\EventRegistration::where('event_id', $event->id)
        ->where('role', 'participant')
        ->where('status', 'confirmed')
        ->with('juryMappingsAsParticipant')
        ->get();
    
    foreach($participants as $p) {
        $mappings = $p->juryMappingsAsParticipant;
        $scores = $mappings->whereNotNull('score')->pluck('score');
        $avgScore = $scores->isNotEmpty() ? round($scores->avg(), 2) : null;
        
        echo "   • {$p->user->name}\n";
        echo "     - Reviews: {$mappings->count()}\n";
        echo "     - Completed: {$mappings->where('status', 'completed')->count()}\n";
        echo "     - Avg Score: " . ($avgScore ?? 'No reviews') . "\n";
        echo "     - Status: " . ($p->presentation_status ?? 'pending') . "\n\n";
    }
}

echo "=== END CHECK ===\n";
