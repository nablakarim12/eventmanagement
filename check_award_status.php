<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Get all participant awards for event 2
echo "Participant Awards for Event 2:\n";
echo "================================\n\n";

$awards = DB::table('participant_awards as pa')
    ->join('users as u', 'pa.user_id', '=', 'u.id')
    ->join('event_awards as ea', 'pa.event_award_id', '=', 'ea.id')
    ->where('pa.event_id', 2)
    ->select('u.id as user_id', 'u.name', 'ea.name as award_name', 'pa.id as pa_id')
    ->get();

foreach ($awards as $award) {
    echo "User ID: {$award->user_id} | Name: {$award->name} | Award: {$award->award_name}\n";
}

echo "\n\nParticipants in evaluation results:\n";
echo "====================================\n\n";

$papers = DB::table('event_papers as ep')
    ->leftJoin('users as u', 'ep.user_id', '=', 'u.id')
    ->where('ep.event_id', 2)
    ->select('ep.id as paper_id', 'ep.user_id', 'u.name', 'ep.title')
    ->get();

foreach ($papers as $paper) {
    echo "Paper ID: {$paper->paper_id} | User ID: {$paper->user_id} | Name: {$paper->name} | Title: {$paper->title}\n";
}
