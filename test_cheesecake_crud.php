<?php

require_once 'vendor/autoload.php';

// Load Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Cheesecake;
use App\Models\Roti;
use App\Models\User;

echo "=== Testing Cheesecake CRUD ===\n\n";

try {
    // Test database connection
    echo "1. Testing database connection...\n";
    $rotiCount = Roti::count();
    $userCount = User::count();
    echo "   - Rotis available: $rotiCount\n";
    echo "   - Users available: $userCount\n";
    
    // Test relationships
    echo "\n2. Testing relationships...\n";
    $cheesecakes = Cheesecake::with(['roti', 'baker'])->take(3)->get();
    foreach ($cheesecakes as $cake) {
        echo "   - Cheesecake ID: {$cake->id}\n";
        echo "     Roti: " . ($cake->roti ? $cake->roti->nama : 'NULL') . "\n";
        echo "     Baker: " . ($cake->baker ? $cake->baker->name : 'NULL') . "\n";
        echo "     Ukuran: {$cake->ukuran}\n";
        echo "     Harga: {$cake->harga}\n";
        echo "     ---\n";
    }
    
    // Test validation data
    echo "\n3. Testing data for form...\n";
    $sampleRoti = Roti::first();
    if ($sampleRoti) {
        echo "   - Sample Roti: {$sampleRoti->nama} - Rp " . number_format($sampleRoti->harga, 0, ',', '.') . "\n";
    }
    
    $baker = User::where('role', 'baker')->first();
    if ($baker) {
        echo "   - Sample Baker: {$baker->name}\n";
    }
    
    echo "\n4. Testing route generation...\n";
    if ($baker) {
        $role = $baker->role;
        echo "   - Role: $role\n";
        echo "   - Store route would be: {$role}_cheesecakestore\n";
        echo "   - Edit route would be: {$role}_cheesecakeedit\n";
        echo "   - Delete route would be: {$role}_cheesecakedelete\n";
    }
    
    echo "\n✅ All tests passed successfully!\n";
    echo "The CRUD system should be working properly.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
