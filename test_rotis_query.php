<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Test query rotis tanpa deleted_at
    $rotis = DB::table('rotis')->get();
    echo "✅ Success: Query rotis berhasil tanpa error deleted_at\n";
    echo "Total rotis: " . $rotis->count() . "\n";
    
    foreach($rotis as $roti) {
        echo "- {$roti->nama}: Rp " . number_format($roti->harga, 0, ',', '.') . "\n";
    }
    
} catch(Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
