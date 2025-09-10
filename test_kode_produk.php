<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Cheesecake;
use Carbon\Carbon;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "=== Testing generateKodeproduk Method ===\n\n";

try {
    // Test generate kode produk beberapa kali
    echo "Testing kode produk generation:\n";
    
    for ($i = 1; $i <= 5; $i++) {
        $kode = Cheesecake::generateKodeproduk();
        echo "Attempt $i: $kode\n";
        
        // Check if kode already exists
        $exists = Cheesecake::where('kode_produk', $kode)->exists();
        echo "  - Already exists: " . ($exists ? "YES" : "NO") . "\n";
        
        // Sleep sedikit untuk memastikan tidak ada race condition
        usleep(10000); // 0.01 detik
    }
    
    echo "\n=== Current existing kode produk for today ===\n";
    $today = Carbon::now()->format('Ymd');
    $existing = Cheesecake::where('kode_produk', 'like', 'CSC' . $today . '%')
                          ->orderBy('kode_produk')
                          ->get(['kode_produk', 'created_at']);
    
    foreach ($existing as $item) {
        echo $item->kode_produk . " - " . $item->created_at . "\n";
    }
    
    echo "\nTotal existing codes for today: " . $existing->count() . "\n";
    
    // Test what would be the next kode
    $nextKode = Cheesecake::generateKodeproduk();
    echo "Next suggested kode: $nextKode\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
