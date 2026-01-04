<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Rubric Item Scores ===\n\n";

// Get sample scores
$scores = DB::select("SELECT * FROM rubric_item_scores LIMIT 10");

foreach ($scores as $score) {
    echo "Score ID: {$score->id}\n";
    echo "  Jury Mapping ID: {$score->jury_mapping_id}\n";
    echo "  Rubric Item ID: {$score->rubric_item_id}\n";
    echo "  Event Paper ID: {$score->event_paper_id}\n";
    echo "  Evaluator ID: {$score->evaluator_id}\n";
    echo "  Score: {$score->score}\n";
    echo "  Comment: " . ($score->comment ?: 'N/A') . "\n";
    echo "  Created: {$score->created_at}\n";
    echo "\n";
}

echo "\n=== Statistics ===\n\n";

// Count by evaluator
$byEvaluator = DB::select("SELECT evaluator_id, COUNT(*) as count FROM rubric_item_scores GROUP BY evaluator_id");
echo "Scores by Evaluator:\n";
foreach ($byEvaluator as $stat) {
    echo "  Evaluator {$stat->evaluator_id}: {$stat->count} scores\n";
}

// Count by paper
$byPaper = DB::select("SELECT event_paper_id, COUNT(*) as count FROM rubric_item_scores GROUP BY event_paper_id");
echo "\nScores by Paper:\n";
foreach ($byPaper as $stat) {
    echo "  Paper {$stat->event_paper_id}: {$stat->count} scores\n";
}

// Get rubric items
echo "\n=== Rubric Items Being Scored ===\n\n";
$items = DB::select("SELECT ri.id, ri.name, ri.max_score, rc.name as category_name 
                     FROM rubric_items ri 
                     JOIN rubric_categories rc ON ri.rubric_category_id = rc.id");
foreach ($items as $item) {
    echo "{$item->category_name} > {$item->name} (Max: {$item->max_score})\n";
}
