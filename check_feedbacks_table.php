<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "=== Feedbacks Table Structure ===\n\n";

if (Schema::hasTable('feedbacks')) {
    $columns = Schema::getColumnListing('feedbacks');
    echo "Columns:\n";
    foreach ($columns as $column) {
        echo "- $column\n";
    }
    
    echo "\n=== Sample Feedback Record ===\n";
    $feedback = DB::table('feedbacks')->first();
    if ($feedback) {
        print_r($feedback);
    } else {
        echo "No feedback records found.\n";
    }
} else {
    echo "Feedbacks table does not exist.\n";
}
