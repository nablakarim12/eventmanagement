<?php

echo "\n=== FORM COLUMN VERIFICATION REPORT ===\n\n";

$files = [
    'Innovation Create' => 'resources/views/organizer/events/create_innovation.blade.php',
    'Innovation Edit' => 'resources/views/organizer/events/edit_innovation.blade.php',
    'Conference Create' => 'resources/views/organizer/events/create_conference.blade.php',
    'Conference Edit' => 'resources/views/organizer/events/edit_conference.blade.php',
];

$removedColumns = [
    'start_date' => 'Replaced by f2f_start_date/online_start_date',
    'end_date' => 'Replaced by f2f_end_date/online_end_date',
    'start_time' => 'Replaced by f2f_start_time/online_start_time',
    'end_time' => 'Replaced by f2f_end_time/online_end_time',
    'reviewer_registration_deadline' => 'Replaced by f2f_reviewer_registration_deadline/online_reviewer_registration_deadline',
    'paper_submission_deadline' => 'Replaced by f2f_paper_submission_deadline/online_paper_submission_deadline',
    'review_deadline' => 'Replaced by f2f_review_deadline/online_review_deadline',
    'acceptance_notification_date' => 'Replaced by f2f_acceptance_notification_date/online_acceptance_notification_date',
];

echo "Checking if Innovation/Conference forms use any REMOVED columns:\n";
echo str_repeat("-", 80) . "\n\n";

$issuesFound = false;

foreach ($files as $label => $file) {
    $fullPath = __DIR__ . '/' . $file;
    
    if (!file_exists($fullPath)) {
        echo "⚠ $label: FILE NOT FOUND\n";
        continue;
    }
    
    $content = file_get_contents($fullPath);
    $foundIssues = [];
    
    foreach ($removedColumns as $column => $replacement) {
        // Check for name="column" or name='column'
        if (preg_match('/name=["\']' . preg_quote($column, '/') . '["\']/', $content)) {
            $foundIssues[] = "  ❌ Uses removed column: $column";
        }
    }
    
    if (empty($foundIssues)) {
        echo "✅ $label: CLEAN (no removed columns)\n";
    } else {
        echo "❌ $label: ISSUES FOUND\n";
        foreach ($foundIssues as $issue) {
            echo "$issue\n";
        }
        $issuesFound = true;
    }
}

echo "\n" . str_repeat("-", 80) . "\n\n";

if (!$issuesFound) {
    echo "✅ SUCCESS: All Innovation and Conference forms use correct column names!\n";
    echo "✅ No forms reference the removed legacy columns.\n";
} else {
    echo "⚠ WARNING: Some forms still reference removed columns.\n";
    echo "These need to be updated to use the f2f_* or online_* versions.\n";
}

echo "\n=== CORRECT COLUMN USAGE ===\n\n";

$correctUsage = [
    'Event Type' => 'Column Used',
    '─────────────' => '──────────────────────────────────────',
    'Innovation/Conference F2F' => 'f2f_start_date, f2f_end_date, f2f_start_time, f2f_end_time',
    'Innovation/Conference Online' => 'online_start_date, online_end_date, online_start_time, online_end_time',
    'Innovation/Conference Hybrid' => 'Both f2f_* and online_* columns',
    'Standard Events' => 'start_date, end_date (still exists for standard events)',
];

foreach ($correctUsage as $type => $columns) {
    printf("%-30s %s\n", $type, $columns);
}

echo "\n";
