<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    DB::table('events')->select('payment_deadline')->first();
    echo "payment_deadline EXISTS\n";
} catch(Exception $e) {
    echo "payment_deadline DOES NOT EXIST\n";
    echo $e->getMessage() . "\n";
}
