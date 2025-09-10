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

// Test data untuk grouping
echo "=== Testing Cheesecake Grouping ===\n\n";

try {
    // Buat roti test jika belum ada
    $roti = Roti::firstOrCreate([
        'nama' => 'Roti Tawar Test'
    ], [
        'harga' => 5000,
        'gambar' => null
    ]);

    echo "Roti ID: " . $roti->id . " - " . $roti->nama . "\n";

    // Buat user baker test jika belum ada
    $baker = User::where('role', 'baker')->first();
    if (!$baker) {
        $baker = User::create([
            'name' => 'Baker Test',
            'email' => 'baker@test.com',
            'username' => 'baker_test',
            'password' => bcrypt('password'),
            'role' => 'baker'
        ]);
    }

    echo "Baker ID: " . $baker->id . " - " . $baker->name . "\n";

    // Buat data cheesecake dengan tanggal dan roti_id yang sama untuk grouping
    $tanggal = Carbon::today();
    $data_test = [
        [
            'roti_id' => $roti->id,
            'jumlah' => 10,
            'harga' => 5000,
            'tanggal_dibuat' => $tanggal,
            'baker_id' => $baker->id,
            'deskripsi' => 'Batch pertama produksi hari ini'
        ],
        [
            'roti_id' => $roti->id,
            'jumlah' => 15,
            'harga' => 5000,
            'tanggal_dibuat' => $tanggal,
            'baker_id' => $baker->id,
            'deskripsi' => 'Batch kedua produksi hari ini'
        ],
        [
            'roti_id' => $roti->id,
            'jumlah' => 8,
            'harga' => 5200,
            'tanggal_dibuat' => $tanggal,
            'baker_id' => $baker->id,
            'deskripsi' => 'Batch ketiga dengan harga sedikit berbeda'
        ]
    ];

    echo "\n=== Creating Test Data ===\n";
    foreach ($data_test as $index => $data) {
        $cheesecake = new Cheesecake();
        // Tambahkan timestamp untuk memastikan unique
        $cheesecake->kode_produk = Cheesecake::generateKodeproduk() . '-' . microtime(true);
        $cheesecake->roti_id = $data['roti_id'];
        $cheesecake->jumlah = $data['jumlah'];
        $cheesecake->harga = $data['harga'];
        $cheesecake->total = $data['jumlah'] * $data['harga'];
        $cheesecake->tanggal_dibuat = $data['tanggal_dibuat'];
        $cheesecake->baker_id = $data['baker_id'];
        $cheesecake->deskripsi = $data['deskripsi'];
        $cheesecake->status = true;
        $cheesecake->save();

        // Sleep untuk memastikan unique kode produk
        usleep(100000); // 0.1 detik

        echo "Data " . ($index + 1) . " created: " . $cheesecake->kode_produk . 
             " - Jumlah: " . $cheesecake->jumlah . 
             " - Harga: " . number_format($cheesecake->harga) . 
             " - Total: " . number_format($cheesecake->total) . "\n";
    }

    echo "\n=== Testing Grouping Logic ===\n";
    
    // Test grouping query
    $query = Cheesecake::with(['baker', 'roti'])->where('baker_id', $baker->id);
    $data = $query->orderBy('created_at', 'desc')->get();

    echo "Total data found: " . $data->count() . "\n";

    // Group data berdasarkan tanggal_dibuat dan roti_id
    $groups = $data->groupBy(function ($item) {
        return $item->tanggal_dibuat->format('Y-m-d') . '_' . $item->roti_id;
    });

    echo "Number of groups: " . $groups->count() . "\n\n";

    foreach ($groups as $groupKey => $items) {
        echo "Group: " . $groupKey . "\n";
        echo "Items count: " . $items->count() . "\n";
        
        if ($items->count() > 1) {
            $totalJumlah = $items->sum('jumlah');
            $totalNilai = $items->sum(function ($item) {
                return $item->jumlah * $item->harga;
            });
            $avgHarga = $items->avg('harga');
            
            echo "Total jumlah: " . $totalJumlah . "\n";
            echo "Total nilai: Rp " . number_format($totalNilai) . "\n";
            echo "Average harga: Rp " . number_format($avgHarga) . "\n";
            echo "--- Items in group ---\n";
            
            foreach ($items as $item) {
                echo "  - " . $item->kode_produk . 
                     " | Jumlah: " . $item->jumlah . 
                     " | Harga: Rp " . number_format($item->harga) . 
                     " | Total: Rp " . number_format($item->total) . 
                     " | " . $item->deskripsi . "\n";
            }
        } else {
            $item = $items->first();
            echo "Single item: " . $item->kode_produk . 
                 " | Jumlah: " . $item->jumlah . 
                 " | Harga: Rp " . number_format($item->harga) . "\n";
        }
        echo "\n";
    }

    echo "=== Test completed successfully! ===\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
