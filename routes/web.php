<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheesecakeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RotiController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\BakerMiddleware;
use App\Http\Middleware\KepalaTokoMiddleware;
use App\Http\Middleware\PimpinanMiddleware;
use App\Http\Middleware\AdminMiddeleware;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/proses_login', [AuthController::class, 'proses_login'])->name('proses_login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/cheesecake/open/{id}', [CheesecakeController::class, 'open'])->name('cheesecakeopen');

// Debug route for testing statistics without auth
Route::get('/debug-stats', function() {
    try {
        $today = now()->format('Y-m-d');
        $currentMonth = now()->format('Y-m');
        
        $penjualanHariIni = \App\Models\Transaksi::whereDate('tanggal_transaksi', $today)
            ->where('status', 'selesai')
            ->sum('total_harga') ?: 0;
        
        $transaksiHariIni = \App\Models\Transaksi::whereDate('tanggal_transaksi', $today)
            ->where('status', 'selesai')
            ->count() ?: 0;
        
        $penjualanBulanIni = \App\Models\Transaksi::whereYear('tanggal_transaksi', now()->year)
            ->whereMonth('tanggal_transaksi', now()->month)
            ->where('status', 'selesai')
            ->sum('total_harga') ?: 0;
        
        $totalPelanggan = \App\Models\Transaksi::whereNotNull('nama_pelanggan')
            ->where('nama_pelanggan', '!=', '')
            ->distinct('nama_pelanggan')
            ->count('nama_pelanggan') ?: 0;
        
        $totalTransaksi = \App\Models\Transaksi::count();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'penjualan_hari_ini' => 'Rp ' . number_format($penjualanHariIni, 0, ',', '.'),
                'transaksi_hari_ini' => number_format($transaksiHariIni, 0, ',', '.'),
                'penjualan_bulan_ini' => 'Rp ' . number_format($penjualanBulanIni, 0, ',', '.'),
                'total_pelanggan' => number_format($totalPelanggan, 0, ',', '.')
            ],
            'debug' => [
                'today' => $today,
                'current_month' => $currentMonth,
                'total_transaksi' => $totalTransaksi,
                'raw_penjualan_hari_ini' => $penjualanHariIni,
                'raw_transaksi_hari_ini' => $transaksiHariIni,
                'raw_penjualan_bulan_ini' => $penjualanBulanIni,
                'raw_total_pelanggan' => $totalPelanggan
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);
    }
});

// Test actual controller methods
Route::get('/test-controller-pdf/{type}', function($type) {
    try {
        $controller = new \App\Http\Controllers\LaporanController();
        
        // Create request with export parameter
        $request = new \Illuminate\Http\Request();
        $request->merge(['export' => 'pdf']);
        
        // Set default dates
        if ($type === 'harian') {
            $request->merge(['tanggal' => now()->format('Y-m-d')]);
            return $controller->harian($request);
        } elseif ($type === 'bulanan') {
            $request->merge(['bulan' => now()->format('Y-m')]);
            return $controller->bulanan($request);
        } elseif ($type === 'tahunan') {
            $request->merge(['tahun' => now()->year]);
            return $controller->tahunan($request);
        }
        
        return response()->json(['error' => 'Invalid type']);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
            'trace' => explode("\n", $e->getTraceAsString())
        ]);
    }
});

// Test with proper data structure
Route::get('/test-data-structure/{type}', function($type) {
    try {
        if (!in_array($type, ['harian', 'bulanan', 'tahunan'])) {
            return response()->json(['error' => 'Invalid type']);
        }
        
        // Create proper object structure like database models
        $sampleTransaksi = (object)[
            'tanggal_transaksi' => now(),
            'total_harga' => 50000,
            'metode_pembayaran' => 'cash',
            'kasir' => (object)['name' => 'Admin'],
            'details' => collect([
                (object)[
                    'jumlah' => 2,
                    'harga_satuan' => 15000,
                    'subtotal' => 30000,
                    'cheesecake' => (object)['nama' => 'Chocolate Cake']
                ],
                (object)[
                    'jumlah' => 1,
                    'harga_satuan' => 20000,
                    'subtotal' => 20000,
                    'cheesecake' => (object)['nama' => 'Vanilla Cake']
                ]
            ])
        ];
        
        $sampleData = collect([$sampleTransaksi]);
        
        $sampleSummary = [
            'total_transaksi' => 1,
            'total_penjualan' => 50000,
            'total_item_terjual' => 3,
            'rata_rata_transaksi' => 50000,
            'rata_rata_harian' => 50000,
            'hari_terbaik' => 50000,
            'rata_rata_bulanan' => 300000,
            'bulan_terbaik' => 'Januari',
            'metode_pembayaran' => [
                'cash' => ['count' => 1, 'total' => 50000]
            ]
        ];
        
        $company = [
            'name' => 'Bakery Warda',
            'address' => 'Jl. Contoh No. 123, Kota',
            'phone' => '0812-3456-7890',
            'email' => 'info@bakerywarda.com'
        ];
        
        $period = now()->format('Y-m-d');
        $chartData = collect([
            (object)['tanggal' => now()->format('d'), 'jumlah_transaksi' => 1, 'total_penjualan' => 50000]
        ]);
        
        // Generate PDF with proper structure
        $pdf = PDF::loadView("laporan.pdf.{$type}", [
            'data' => $sampleData,
            'summary' => $sampleSummary,
            'period' => $period,
            'chartData' => $chartData,
            'company' => $company
        ]);
        
        return $pdf->download("test-struktur-{$type}.pdf");
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
            'trace' => explode("\n", $e->getTraceAsString())
        ]);
    }
});

// Test individual PDF downloads
Route::get('/test-download/{type}', function($type) {
    try {
        if (!in_array($type, ['harian', 'bulanan', 'tahunan'])) {
            return response()->json(['error' => 'Invalid type']);
        }
        
        // Sample data
        $sampleData = collect([
            (object)[
                'tanggal' => now()->format('Y-m-d'),
                'total_penjualan' => 50000,
                'jumlah_transaksi' => 5,
                'metode_pembayaran' => 'cash'
            ]
        ]);
        
        $sampleSummary = [
            'total_transaksi' => 5,
            'total_penjualan' => 50000,
            'total_item_terjual' => 10,
            'rata_rata_transaksi' => 10000,
            'rata_rata_harian' => 10000,
            'hari_terbaik' => 15000,
            'rata_rata_bulanan' => 300000,
            'bulan_terbaik' => 'Januari',
            'metode_pembayaran' => collect([
                ['metode' => 'cash', 'total' => 30000],
                ['metode' => 'transfer', 'total' => 20000]
            ])
        ];
        
        $company = [
            'name' => 'Bakery Warda',
            'address' => 'Jl. Contoh No. 123, Kota',
            'phone' => '0812-3456-7890',
            'email' => 'info@bakerywarda.com'
        ];
        
        $period = now()->format('Y-m-d');
        $chartData = collect(['Jan' => 100000, 'Feb' => 120000]);
        
        // Generate PDF
        $pdf = PDF::loadView("laporan.pdf.{$type}", [
            'data' => $sampleData,
            'summary' => $sampleSummary,
            'period' => $period,
            'chartData' => $chartData,
            'company' => $company
        ]);
        
        return $pdf->download("test-laporan-{$type}.pdf");
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Test PDF generation with real data comparison
Route::get('/test-pdf-comparison', function() {
    try {
        // Sample data mirip dengan yang sesungguhnya
        $sampleData = collect([
            (object)[
                'tanggal' => now()->format('Y-m-d'),
                'total_penjualan' => 50000,
                'jumlah_transaksi' => 5,
                'metode_pembayaran' => 'cash'
            ]
        ]);
        
        $sampleSummary = [
            'total_transaksi' => 5,
            'total_penjualan' => 50000,
            'total_item_terjual' => 10,
            'rata_rata_transaksi' => 10000,
            'rata_rata_harian' => 10000,
            'hari_terbaik' => 15000,
            'rata_rata_bulanan' => 300000,
            'bulan_terbaik' => 'Januari',
            'metode_pembayaran' => collect([
                ['metode' => 'cash', 'total' => 30000],
                ['metode' => 'transfer', 'total' => 20000]
            ])
        ];
        
        $company = [
            'name' => 'Bakery Warda',
            'address' => 'Jl. Contoh No. 123, Kota',
            'phone' => '0812-3456-7890',
            'email' => 'info@bakerywarda.com'
        ];
        
        $period = now()->format('Y-m-d');
        $chartData = collect(['Jan' => 100000, 'Feb' => 120000]);
        
        $results = [];
        
        // Test each PDF type
        foreach (['harian', 'bulanan', 'tahunan'] as $type) {
            try {
                $pdf = PDF::loadView("laporan.pdf.{$type}", [
                    'data' => $sampleData,
                    'summary' => $sampleSummary,
                    'period' => $period,
                    'chartData' => $chartData,
                    'company' => $company
                ]);
                
                $results[$type] = [
                    'status' => 'SUCCESS',
                    'output_size' => strlen($pdf->output()),
                    'can_download' => true
                ];
                
            } catch (\Exception $e) {
                $results[$type] = [
                    'status' => 'ERROR',
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => basename($e->getFile())
                ];
            }
        }
        
        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'global_error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile())
        ]);
    }
});

// Test HTML rendering for all PDF views
Route::get('/test-html-rendering', function() {
    try {
        $data = collect([]);
        $summary = [
            'total_transaksi' => 0,
            'total_penjualan' => 0,
            'total_item_terjual' => 0,
            'rata_rata_transaksi' => 0,
            'rata_rata_harian' => 0,
            'hari_terbaik' => 0,
            'rata_rata_bulanan' => 0,
            'bulan_terbaik' => null,
            'metode_pembayaran' => collect([])
        ];
        $company = [
            'name' => 'Bakery Warda',
            'address' => 'Jl. Contoh No. 123, Kota',
            'phone' => '0812-3456-7890',
            'email' => 'info@bakerywarda.com'
        ];
        $period = now()->format('Y-m-d');
        $chartData = collect([]);
        
        $results = [];
        
        // Test Harian
        try {
            $harianView = view('laporan.pdf.harian', compact('data', 'summary', 'period', 'chartData', 'company'));
            $harianHtml = $harianView->render();
            $results['harian'] = 'OK - ' . strlen($harianHtml) . ' chars';
        } catch (\Exception $e) {
            $results['harian'] = 'ERROR: ' . $e->getMessage();
        }
        
        // Test Bulanan
        try {
            $bulananView = view('laporan.pdf.bulanan', compact('data', 'summary', 'period', 'chartData', 'company'));
            $bulananHtml = $bulananView->render();
            $results['bulanan'] = 'OK - ' . strlen($bulananHtml) . ' chars';
        } catch (\Exception $e) {
            $results['bulanan'] = 'ERROR: ' . $e->getMessage();
        }
        
        // Test Tahunan
        try {
            $tahunanView = view('laporan.pdf.tahunan', compact('data', 'summary', 'period', 'chartData', 'company'));
            $tahunanHtml = $tahunanView->render();
            $results['tahunan'] = 'OK - ' . strlen($tahunanHtml) . ' chars';
        } catch (\Exception $e) {
            $results['tahunan'] = 'ERROR: ' . $e->getMessage();
        }
        
        return response()->json($results);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile())
        ]);
    }
});

// Test PDF generation step by step
Route::get('/test-pdf-step-by-step', function() {
    try {
        // Step 1: Prepare minimal data
        $data = collect([]);
        $summary = [
            'total_transaksi' => 0,
            'total_penjualan' => 0,
            'total_item_terjual' => 0,
            'rata_rata_transaksi' => 0,
            'metode_pembayaran' => collect([])
        ];
        $company = [
            'name' => 'Bakery Warda',
            'address' => 'Jl. Contoh No. 123, Kota',
            'phone' => '0812-3456-7890',
            'email' => 'info@bakerywarda.com'
        ];
        $period = now()->format('Y-m-d');
        $chartData = null;
        
        // Step 2: Test view rendering first (without PDF)
        $view = view('laporan.pdf.harian', compact('data', 'summary', 'period', 'chartData', 'company'));
        $htmlContent = $view->render();
        
        // Step 3: Try PDF generation
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($htmlContent);
        
        return $pdf->stream('test-step-by-step.pdf');
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
            'step' => 'Failed during PDF generation'
        ]);
    }
});

// Direct exportPDF test
Route::get('/test-export-pdf-direct', function() {
    try {
        $controller = new \App\Http\Controllers\LaporanController();
        
        // Prepare data like controller does
        $tanggal = now()->format('Y-m-d');
        $transaksi = \App\Models\Transaksi::with(['kasir', 'details.cheesecake'])
            ->whereDate('tanggal_transaksi', $tanggal)
            ->where('status', 'selesai')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $summary = [
            'total_transaksi' => $transaksi->count(),
            'total_penjualan' => $transaksi->sum('total_harga'),
            'total_item_terjual' => $transaksi->sum(function($t) {
                return $t->details->sum('jumlah');
            }),
            'rata_rata_transaksi' => $transaksi->count() > 0 ? $transaksi->avg('total_harga') : 0,
            'metode_pembayaran' => $transaksi->groupBy('metode_pembayaran')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_harga')
                ];
            })
        ];
        
        // Call exportPDF directly using reflection
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('exportPDF');
        $method->setAccessible(true);
        
        return $method->invokeArgs($controller, ['harian', $transaksi, $summary, $tanggal]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
            'trace' => substr($e->getTraceAsString(), 0, 1000)
        ]);
    }
});

// Test specific PDF methods
Route::get('/test-pdf-methods', function() {
    try {
        $controller = new \App\Http\Controllers\LaporanController();
        
        // Test harian PDF
        $requestHarian = new \Illuminate\Http\Request([
            'tanggal' => now()->format('Y-m-d'),
            'export' => 'pdf'
        ]);
        
        $harianResult = $controller->harian($requestHarian);
        
        return response()->json([
            'harian_result_type' => get_class($harianResult),
            'harian_headers' => $harianResult->headers->all() ?? 'no headers',
            'status' => 'harian tested successfully'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
            'trace' => substr($e->getTraceAsString(), 0, 500)
        ]);
    }
});

// Compare data structures
Route::get('/compare-data', function() {
    try {
        $controllerHarian = new \App\Http\Controllers\LaporanController();
        $controllerBulanan = new \App\Http\Controllers\LaporanController();
        $controllerTahunan = new \App\Http\Controllers\LaporanController();
        
        // Get data for each report type
        $requestHarian = new \Illuminate\Http\Request(['tanggal' => now()->format('Y-m-d')]);
        $requestBulanan = new \Illuminate\Http\Request(['bulan' => now()->format('Y-m')]);
        $requestTahunan = new \Illuminate\Http\Request(['tahun' => now()->year]);
        
        // Call each method without export to see data structure
        $harianView = $controllerHarian->harian($requestHarian);
        $bulananView = $controllerBulanan->bulanan($requestBulanan);
        $tahunanView = $controllerTahunan->tahunan($requestTahunan);
        
        return response()->json([
            'harian_data' => [
                'view_name' => $harianView->getName(),
                'data_keys' => array_keys($harianView->getData())
            ],
            'bulanan_data' => [
                'view_name' => $bulananView->getName(),
                'data_keys' => array_keys($bulananView->getData())
            ],
            'tahunan_data' => [
                'view_name' => $tahunanView->getName(),
                'data_keys' => array_keys($tahunanView->getData())
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile())
        ]);
    }
});

// Real data PDF test
Route::get('/real-pdf-test', function() {
    try {
        $tanggal = now()->format('Y-m-d');
        
        $transaksi = \App\Models\Transaksi::with(['kasir', 'details.cheesecake'])
            ->whereDate('tanggal_transaksi', $tanggal)
            ->where('status', 'selesai')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $summary = [
            'total_transaksi' => $transaksi->count(),
            'total_penjualan' => $transaksi->sum('total_harga'),
            'total_item_terjual' => $transaksi->sum(function($t) {
                return $t->details->sum('jumlah');
            }),
            'rata_rata_transaksi' => $transaksi->count() > 0 ? $transaksi->avg('total_harga') : 0,
            'metode_pembayaran' => $transaksi->groupBy('metode_pembayaran')->map(function($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_harga')
                ];
            })
        ];
        
        $company = [
            'name' => 'Bakery Warda',
            'address' => 'Jl. Contoh No. 123, Kota',
            'phone' => '0812-3456-7890',
            'email' => 'info@bakerywarda.com'
        ];
        $period = $tanggal;
        $chartData = null;
        $data = $transaksi;
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf.harian', compact('data', 'summary', 'period', 'chartData', 'company'));
        return $pdf->stream('real-test.pdf');
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
            'trace' => substr($e->getTraceAsString(), 0, 1000)
        ]);
    }
});

// Simple PDF test
Route::get('/simple-pdf-test', function() {
    try {
        $data = collect([]);
        $summary = [
            'total_transaksi' => 0,
            'total_penjualan' => 0,
            'total_item_terjual' => 0,
            'rata_rata_transaksi' => 0,
            'metode_pembayaran' => collect([])
        ];
        $company = [
            'name' => 'Bakery Warda',
            'address' => 'Jl. Contoh No. 123, Kota',
            'phone' => '0812-3456-7890',
            'email' => 'info@bakerywarda.com'
        ];
        $period = now()->format('Y-m-d');
        $chartData = null;
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf.harian', compact('data', 'summary', 'period', 'chartData', 'company'));
        return $pdf->stream('simple-test.pdf');
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile())
        ]);
    }
});

// Debug PDF routes
Route::get('/debug-pdf-harian', function() {
    try {
        $controller = new \App\Http\Controllers\LaporanController();
        $request = request();
        $request->merge(['export' => 'pdf']);
        return $controller->harian($request);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

Route::get('/debug-pdf-tahunan', function() {
    try {
        $controller = new \App\Http\Controllers\LaporanController();
        $request = request();
        $request->merge(['export' => 'pdf']);
        return $controller->tahunan($request);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Test laporan routes (untuk debugging)
Route::get('/test-laporan-harian', function() {
    $controller = new \App\Http\Controllers\LaporanController();
    return $controller->harian(request());
});

Route::get('/test-laporan-bulanan', function() {
    $controller = new \App\Http\Controllers\LaporanController();
    return $controller->bulanan(request());
});

Route::get('/test-laporan-tahunan', function() {
    $controller = new \App\Http\Controllers\LaporanController();
    return $controller->tahunan(request());
});

// Test PDF generation
Route::get('/test-pdf', function() {
    try {
        $data = \App\Models\Transaksi::with(['kasir', 'details.cheesecake'])
            ->where('status', 'selesai')
            ->limit(5)
            ->get();
            
        $summary = [
            'total_transaksi' => 5,
            'total_penjualan' => 500000,
            'total_item_terjual' => 25,
            'rata_rata_transaksi' => 100000,
            'metode_pembayaran' => collect([
                'tunai' => ['count' => 3, 'total' => 300000],
                'transfer' => ['count' => 2, 'total' => 200000]
            ])
        ];
        
        $company = [
            'name' => 'Bakery Warda',
            'address' => 'Jl. Contoh No. 123, Kota',
            'phone' => '0812-3456-7890',
            'email' => 'info@bakerywarda.com'
        ];
        
        $period = now()->format('Y-m-d');
        $chartData = null;
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf.harian', compact('data', 'summary', 'period', 'chartData', 'company'));
        
        return $pdf->stream('test-laporan.pdf');
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// Test debug route untuk laporan
Route::get('/test-laporan', function() {
    try {
        $controller = new \App\Http\Controllers\LaporanController();
        return response()->json([
            'message' => 'LaporanController accessible',
            'today' => now()->format('Y-m-d'),
            'transaksi_count' => \App\Models\Transaksi::count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile())
        ]);
    }
});

// Test page for statistics
Route::get('/test-stats-page', function() {
    return view('test-stats');
});

// Quick test route to check if server is working
Route::get('/quick-test', function() {
    return response()->json([
        'message' => 'Server is working!',
        'time' => now()->format('Y-m-d H:i:s'),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version()
    ]);
});

// Debug route untuk mengecek koneksi database
Route::get('/test-db', function() {
    try {
        // Test basic database connection
        $pdo = DB::connection()->getPdo();
        
        // Test simple query
        $tables = DB::select('SHOW TABLES');
        
        // Test Model connection
        $userCount = \App\Models\User::count();
        
        // Test if transaksis table exists and get structure
        $transaksiTableExists = false;
        $transaksiCount = 0;
        try {
            $columns = DB::select('DESCRIBE transaksis');
            $transaksiTableExists = true;
            $transaksiCount = \App\Models\Transaksi::count();
        } catch (\Exception $e) {
            $transaksiTableExists = false;
        }
        
        return response()->json([
            'database_connection' => 'OK',
            'pdo_connected' => isset($pdo),
            'tables_count' => count($tables),
            'tables' => array_map(function($table) {
                return array_values((array)$table)[0];
            }, $tables),
            'users_count' => $userCount,
            'transaksis_table_exists' => $transaksiTableExists,
            'transaksi_count' => $transaksiCount,
            'today' => now()->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone', 'UTC')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Debug route for creating test data without auth
Route::get('/create-test-data', function() {
    try {
        // Create test transactions if none exist
        if (\App\Models\Transaksi::count() == 0) {
            // Get a user for kasir_id
            $user = \App\Models\User::first();
            if (!$user) {
                return response()->json(['error' => 'No users found in database']);
            }

            // Create today's transaction
            \App\Models\Transaksi::create([
                'kode_transaksi' => 'TRX-TEST-001',
                'kasir_id' => $user->id,
                'nama_pelanggan' => 'Pelanggan Test 1',
                'total_harga' => 150000,
                'bayar' => 200000,
                'kembalian' => 50000,
                'status' => 'selesai',
                'metode_pembayaran' => 'tunai',
                'tanggal_transaksi' => now(),
                'catatan' => 'Test transaction hari ini'
            ]);

            // Create another transaction today
            \App\Models\Transaksi::create([
                'kode_transaksi' => 'TRX-TEST-002',
                'kasir_id' => $user->id,
                'nama_pelanggan' => 'Pelanggan Test 2',
                'total_harga' => 75000,
                'bayar' => 100000,
                'kembalian' => 25000,
                'status' => 'selesai',
                'metode_pembayaran' => 'transfer',
                'tanggal_transaksi' => now()->subHours(2),
                'catatan' => 'Test transaction 2'
            ]);

            // Create a monthly transaction
            \App\Models\Transaksi::create([
                'kode_transaksi' => 'TRX-TEST-003',
                'kasir_id' => $user->id,
                'nama_pelanggan' => 'Pelanggan Test 3',
                'total_harga' => 300000,
                'bayar' => 300000,
                'kembalian' => 0,
                'status' => 'selesai',
                'metode_pembayaran' => 'kartu',
                'tanggal_transaksi' => now()->subDays(5),
                'catatan' => 'Test transaction bulan ini'
            ]);

            return response()->json(['success' => 'Test data created successfully', 'count' => 3]);
        }

        return response()->json(['info' => 'Test data already exists', 'count' => \App\Models\Transaksi::count()]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

// Route khusus admin
Route::middleware(AdminMiddeleware::class)->prefix('admin')->name('admin_')->group(function () {
    Route::resource('users', UserController::class);
    // Tambahkan route admin lain di sini jika perlu
});
Route::prefix('pimpinan')->middleware(PimpinanMiddleware::class)->name('pimpinan_')->group(function () {
    // Dashboard
    Route::get('/cheesecake', [CheesecakeController::class, 'index'])->name('cheesecake');
    Route::get('/cheesecake/statistics', [CheesecakeController::class, 'statistics'])->name('cheesecake_statistics');
    Route::get('/cheesecake/{id}/qrcode_show', [CheesecakeController::class, 'showQr'])->name('qrcode_show');
    Route::delete('/cheesecake/{id}', [CheesecakeController::class, 'destroy'])->name('cheesecakedelete');
    
    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/harian', [LaporanController::class, 'harian'])->name('laporan_harian');
    Route::get('/laporan/bulanan', [LaporanController::class, 'bulanan'])->name('laporan_bulanan');
    Route::get('/laporan/tahunan', [LaporanController::class, 'tahunan'])->name('laporan_tahunan');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan_export');
});

// Route laporan umum (accessible untuk semua role yang ada akses)
Route::middleware(['auth'])->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/harian', [LaporanController::class, 'harian'])->name('laporan.harian');
    Route::get('/laporan/bulanan', [LaporanController::class, 'bulanan'])->name('laporan.bulanan');
    Route::get('/laporan/tahunan', [LaporanController::class, 'tahunan'])->name('laporan.tahunan');
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');
});

Route::prefix('baker')->middleware(BakerMiddleware::class)->name('baker_')->group(function () {

    Route::get('/cheesecake', [CheesecakeController::class, 'index'])->name('cheesecake');
    Route::get('/cheesecake/statistics', [CheesecakeController::class, 'statistics'])->name('cheesecake_statistics');
    Route::post('/cheesecake/store', [CheesecakeController::class, 'store'])->name('cheesecakestore');
    Route::get('/cheesecake/{id}/edit', [CheesecakeController::class, 'edit'])->name('cheesecakeedit');
    Route::get('/cheesecake/{id}/qrcode_show', [CheesecakeController::class, 'showQr'])->name('qrcode_show');
    Route::delete('/cheesecake/{id}', [CheesecakeController::class, 'destroy'])->name('cheesecakedelete');
    
});

Route::prefix('kepalatoko')->middleware(KepalaTokoMiddleware::class)->name('kepalatoko_')->group(function () {
    // Dashboard dan view produk
    Route::get('/cheesecake', [CheesecakeController::class, 'index'])->name('cheesecake');
    Route::get('/cheesecake/statistics', [CheesecakeController::class, 'statistics'])->name('cheesecake_statistics');
    Route::get('/cheesecake/{id}/qrcode_show', [CheesecakeController::class, 'showQr'])->name('qrcode_show');
    Route::delete('/cheesecake/{id}', [CheesecakeController::class, 'destroy'])->name('cheesecakedelete');
    
    // Transaksi Management
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::get('/transaksi/create', [TransaksiController::class, 'create'])->name('transaksi_create');
    Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi_store');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi_show');
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy'])->name('transaksi_destroy');
    Route::get('/product/{id}', [TransaksiController::class, 'getProduct'])->name('get_product');
    Route::post('/scan-qr', [TransaksiController::class, 'getProductByQr'])->name('scan_qr');
    Route::get('/transaksi/statistics', [TransaksiController::class, 'statistics'])->name('kepalatoko_transaksi_statistics');
    Route::get('/transaksi/create-test-data', [TransaksiController::class, 'createTestData'])->name('create_test_data');
});

// Alias routes untuk karyawan (redirect ke kepalatoko)
Route::prefix('karyawan')->middleware(KepalaTokoMiddleware::class)->name('karyawan_')->group(function () {
    Route::get('/cheesecake', [CheesecakeController::class, 'index'])->name('cheesecake');
    Route::get('/cheesecake/statistics', [CheesecakeController::class, 'statistics'])->name('transaksi_statistics');
    Route::get('/cheesecake/{id}/qrcode_show', [CheesecakeController::class, 'showQr'])->name('qrcode_show');
    Route::delete('/cheesecake/{id}', [CheesecakeController::class, 'destroy'])->name('cheesecakedelete');
    
    // Transaksi Management
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::get('/transaksi/create', [TransaksiController::class, 'create'])->name('transaksi_create');
    Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi_store');
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show'])->name('transaksi_show');
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy'])->name('transaksi_destroy');
    Route::get('/product/{id}', [TransaksiController::class, 'getProduct'])->name('get_product');
    Route::post('/scan-qr', [TransaksiController::class, 'getProductByQr'])->name('scan_qr');
    Route::get('/transaksi/statistics', [TransaksiController::class, 'statistics'])->name('karyawan_transaksi_statistics');
});
