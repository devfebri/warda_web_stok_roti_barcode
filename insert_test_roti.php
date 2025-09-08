<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Insert test data
DB::table('rotis')->insert([
    ['nama' => 'Roti Tawar', 'harga' => 15000, 'created_at' => now(), 'updated_at' => now()],
    ['nama' => 'Roti Coklat', 'harga' => 18000, 'created_at' => now(), 'updated_at' => now()],
    ['nama' => 'Roti Keju', 'harga' => 20000, 'created_at' => now(), 'updated_at' => now()],
]);

echo "Test roti data created successfully!\n";
?>
