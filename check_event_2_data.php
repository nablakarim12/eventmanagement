<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$event = DB::table('events')->where('id', 2)->first();

echo "Event ID 2 Data:\n";
echo "====================\n\n";

foreach ($event as $key => $value) {
    if ($value !== null) {
        echo "$key: ";
        if (is_string($value) && (strpos($key, 'date') !== false || strpos($key, 'deadline') !== false || strpos($key, 'notification') !== false)) {
            echo $value;
        } else {
            echo $value;
        }
        echo "\n";
    }
}
