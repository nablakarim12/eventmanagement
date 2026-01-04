<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== JURY MAPPING SYSTEM CHECK ===\n\n";

// Check 1: Table exists
echo "1. jury_mappings table: ";
echo Schema::hasTable('jury_mappings') ? "✓ EXISTS\n" : "✗ NOT FOUND\n";

// Check 2: Model exists
echo "2. JuryMapping model: ";
echo class_exists('App\Models\JuryMapping') ? "✓ EXISTS\n" : "✗ NOT FOUND\n";

// Check 3: Conference events with registrations
echo "\n3. Conference Events with Registrations:\n";
$events = App\Models\Event::whereNotNull('delivery_mode')
    ->where('status', 'published')
    ->withCount([
        'registrations as participants_count' => function($q) {
            $q->where('role', 'participant')->where('status', 'confirmed');
        },
        'registrations as reviewers_count' => function($q) {
            $q->where('role', 'reviewer')->where('status', 'confirmed');
        }
    ])
    ->get();

echo "   Total conference events: " . $events->count() . "\n";
foreach($events as $event) {
    echo "   - {$event->title}\n";
    echo "     Participants: {$event->participants_count}\n";
    echo "     Reviewers: {$event->reviewers_count}\n";
    
    // Check categories
    $participants = App\Models\EventRegistration::where('event_id', $event->id)
        ->where('role', 'participant')
        ->where('status', 'confirmed')
        ->get();
    
    if ($participants->isNotEmpty()) {
        echo "     Participant Categories:\n";
        foreach($participants as $p) {
            echo "       * {$p->user->name} → {$p->selected_category}\n";
        }
    }
    
    $reviewers = App\Models\EventRegistration::where('event_id', $event->id)
        ->where('role', 'reviewer')
        ->where('status', 'confirmed')
        ->get();
    
    if ($reviewers->isNotEmpty()) {
        echo "     Reviewer Categories:\n";
        foreach($reviewers as $r) {
            echo "       * {$r->user->name} → {$r->selected_category}\n";
        }
    }
    
    echo "\n";
}

// Check 4: Current mappings
echo "4. Current Jury Mappings:\n";
$mappings = App\Models\JuryMapping::with(['reviewerRegistration.user', 'participantRegistration.user', 'event'])->get();
echo "   Total mappings: " . $mappings->count() . "\n";
if ($mappings->isNotEmpty()) {
    foreach($mappings as $mapping) {
        echo "   - {$mapping->event->title}:\n";
        echo "     {$mapping->reviewerRegistration->user->name} (Reviewer) → {$mapping->participantRegistration->user->name} (Participant)\n";
    }
}

echo "\n=== END CHECK ===\n";
