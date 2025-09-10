<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Models\Cheesecake;
use App\Models\Roti;
use App\Models\User;
use Carbon\Carbon;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "=== Testing Save with Duplicate Prevention ===\n\n";

try {
    // Ambil data roti dan user
    $roti = Roti::first();
    $baker = User::where('role', 'baker')->first();
    
    if (!$roti) {
        echo "Error: No roti found\n";
        exit;
    }
    
    if (!$baker) {
        echo "Error: No baker found\n";
        exit;
    }
    
    echo "Using Roti: {$roti->nama} (ID: {$roti->id})\n";
    echo "Using Baker: {$baker->name} (ID: {$baker->id})\n\n";
    
    // Test create multiple data secara berurutan (simulasi kondisi real)
    echo "Creating multiple cheesecake data:\n";
    
    for ($i = 1; $i <= 3; $i++) {
        try {
            $post = new Cheesecake();
            
            // Simulasi retry mechanism seperti di controller
            $maxRetries = 5;
            $retryCount = 0;
            $saved = false;
            
            while (!$saved && $retryCount < $maxRetries) {
                try {
                    $post->kode_produk = Cheesecake::generateKodeproduk();
                    $post->roti_id = $roti->id;
                    $post->deskripsi = "Test data batch $i";
                    $post->jumlah = 10 + $i;
                    $post->harga = 5000;
                    $post->total = (10 + $i) * 5000;
                    $post->tanggal_dibuat = Carbon::now();
                    $post->baker_id = $baker->id;
                    $post->status = true;
                    $post->save();
                    $saved = true;
                    
                    echo "Data $i saved successfully: {$post->kode_produk}\n";
                    
                } catch (\Illuminate\Database\QueryException $e) {
                    // Jika error adalah duplicate entry untuk kode_produk, coba lagi
                    if ($e->errorInfo[1] == 1062 && strpos($e->getMessage(), 'kode_produk') !== false) {
                        $retryCount++;
                        echo "  Retry attempt $retryCount for data $i (duplicate kode_produk)\n";
                        $post = new Cheesecake(); // Reset model
                        usleep(10000); // Sleep 0.01 detik sebelum retry
                        continue;
                    } else {
                        // Jika bukan duplicate entry error, throw kembali
                        throw $e;
                    }
                }
            }
            
            if (!$saved) {
                echo "ERROR: Failed to save data $i after $maxRetries attempts\n";
            }
            
        } catch (Exception $e) {
            echo "ERROR creating data $i: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== Final Check ===\n";
    $today = Carbon::now()->format('Ymd');
    $todayCheesecakes = Cheesecake::where('kode_produk', 'like', 'CSC' . $today . '%')
                                 ->orderBy('kode_produk')
                                 ->get(['kode_produk', 'jumlah', 'created_at']);
    
    echo "Total cheesecakes created today: " . $todayCheesecakes->count() . "\n";
    echo "Latest codes:\n";
    
    foreach ($todayCheesecakes->take(10) as $item) {
        echo "  {$item->kode_produk} - Jumlah: {$item->jumlah} - {$item->created_at}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
