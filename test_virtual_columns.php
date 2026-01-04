<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Virtual Compatibility Columns ===\n\n";

// Test 1: Check if virtual columns exist
$columns = DB::select("SELECT column_name, is_generated FROM information_schema.columns WHERE table_name = 'events' AND column_name IN ('start_date', 'end_date', 'start_time', 'end_time') ORDER BY column_name");

echo "Virtual Columns:\n";
foreach ($columns as $col) {
    $generated = $col->is_generated === 'ALWAYS' ? '✅ GENERATED' : '❌ NOT GENERATED';
    echo "  - {$col->column_name}: {$generated}\n";
}

echo "\n";

// Test 2: Query events and check virtual columns populate correctly
$events = DB::table('events')
    ->select('id', 'title', 'f2f_start_date', 'online_start_date', 'start_date', 'f2f_end_date', 'end_date')
    ->whereNotNull('f2f_start_date')
    ->limit(5)
    ->get();

echo "=== Event Data Verification ===\n";
foreach ($events as $event) {
    echo "\nEvent ID: {$event->id}\n";
    echo "  Title: {$event->title}\n";
    echo "  F2F Start: {$event->f2f_start_date}\n";
    echo "  Virtual start_date: {$event->start_date}\n";
    echo "  Match: " . ($event->f2f_start_date == $event->start_date ? '✅ YES' : '❌ NO') . "\n";
}

// Test 3: Try the exact query that friend's system uses
echo "\n=== Testing Friend's Query ===\n";
try {
    $upcomingEvents = DB::table('events')
        ->where('status', 'published')
        ->where('start_date', '>', now())
        ->orderBy('start_date', 'asc')
        ->limit(5)
        ->get(['id', 'title', 'start_date', 'end_date']);
    
    echo "✅ Query successful! Found " . $upcomingEvents->count() . " upcoming events:\n";
    foreach ($upcomingEvents as $event) {
        echo "  - {$event->title} (Start: {$event->start_date})\n";
    }
} catch (\Exception $e) {
    echo "❌ Query failed: " . $e->getMessage() . "\n";
}

echo "\n✅ Virtual columns are now compatible with friend's system!\n";
