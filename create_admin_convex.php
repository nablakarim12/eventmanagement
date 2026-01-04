<?php

/**
 * CREATE SPECIFIC ADMIN ACCOUNT
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

echo "\n";
echo "=====================================\n";
echo "  CREATING ADMIN ACCOUNT\n";
echo "=====================================\n\n";

try {
    // Clear existing admins
    DB::table('admins')->truncate();
    echo "✓ Cleared existing admin records\n\n";
    
    // Create new admin with specified credentials
    $admin = Admin::create([
        'username' => 'admin',
        'email' => 'admin@convex.com',
        'password' => Hash::make('adminConvex@123'),
    ]);
    
    echo "✓ Admin account created successfully!\n\n";
    echo "=====================================\n";
    echo "  LOGIN CREDENTIALS\n";
    echo "=====================================\n";
    echo "Email:    admin@convex.com\n";
    echo "Password: adminConvex@123\n";
    echo "URL:      http://localhost/admin/login\n";
    echo "=====================================\n\n";
    
    echo "✓ You can now login to the admin panel!\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
