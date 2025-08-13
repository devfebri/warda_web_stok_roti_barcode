<?php

namespace App\Http\Controllers;

use App\Models\Cheesecake;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index()
    {
        // Dashboard laporan dengan summary data
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->startOfYear();

        $summary = [
            'penjualan_hari_ini' => Transaksi::whereDate('tanggal_transaksi', $today)
                ->where('status', 'selesai')
                ->sum('total_harga'),
            'transaksi_hari_ini' => Transaksi::whereDate('tanggal_transaksi', $today)
                ->where('status', 'selesai')
                ->count(),
            'penjualan_bulan_ini' => Transaksi::where('tanggal_transaksi', '>=', $thisMonth)
                ->where('status', 'selesai')
                ->sum('total_harga'),
            'penjualan_tahun_ini' => Transaksi::where('tanggal_transaksi', '>=', $thisYear)
                ->where('status', 'selesai')
                ->sum('total_harga')
        ];

        return view('laporan.index', compact('summary'));
    }

    public function harian(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        
        $transaksi = Transaksi::with(['kasir', 'details.cheesecake'])
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

        if ($request->ajax()) {
            return datatables()->of($transaksi)
                ->addColumn('kasir_name', function($row) {
                    return $row->kasir ? $row->kasir->name : 'N/A';
                })
                ->addColumn('total_item', function($row) {
                    return $row->details->sum('jumlah');
                })
                ->addColumn('formatted_total', function($row) {
                    return 'Rp ' . number_format($row->total_harga, 0, ',', '.');
                })
                ->addColumn('waktu', function($row) {
                    return $row->tanggal_transaksi->format('H:i:s');
                })
                ->make(true);
        }

        if ($request->get('export') === 'pdf') {
            return $this->exportPDF('harian', $transaksi, $summary, $tanggal);
        }

        return view('laporan.harian', compact('transaksi', 'summary', 'tanggal'));
    }

    public function bulanan(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->format('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();

        $transaksi = Transaksi::with(['kasir', 'details.cheesecake'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        // Group by date for chart
        $dailyData = $transaksi->groupBy(function($item) {
            return $item->tanggal_transaksi->format('Y-m-d');
        });

        $summary = [
            'total_transaksi' => $transaksi->count(),
            'total_penjualan' => $transaksi->sum('total_harga'),
            'total_item_terjual' => $transaksi->sum(function($t) {
                return $t->details->sum('jumlah');
            }),
            'rata_rata_harian' => $dailyData->count() > 0 ? $transaksi->sum('total_harga') / $dailyData->count() : 0,
            'hari_terbaik' => $dailyData->map(function($dayTransaksi) {
                return $dayTransaksi->sum('total_harga');
            })->max()
        ];

        $chartData = $dailyData->map(function($dayTransaksi, $date) {
            return [
                'tanggal' => $date,
                'jumlah_transaksi' => $dayTransaksi->count(),
                'total_penjualan' => $dayTransaksi->sum('total_harga')
            ];
        })->values();

        if ($request->get('export') === 'pdf') {
            return $this->exportPDF('bulanan', $transaksi, $summary, $bulan, $chartData);
        }

        return view('laporan.bulanan', compact('transaksi', 'summary', 'bulan', 'chartData'));
    }

    public function tahunan(Request $request)
    {
        $tahun = $request->get('tahun', Carbon::now()->year);
        $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfYear();
        $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfYear();

        $transaksi = Transaksi::with(['kasir', 'details.cheesecake'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->where('status', 'selesai')
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        // Group by month for chart
        $monthlyData = $transaksi->groupBy(function($item) {
            return $item->tanggal_transaksi->format('Y-m');
        });

        $summary = [
            'total_transaksi' => $transaksi->count(),
            'total_penjualan' => $transaksi->sum('total_harga'),
            'total_item_terjual' => $transaksi->sum(function($t) {
                return $t->details->sum('jumlah');
            }),
            'rata_rata_bulanan' => $monthlyData->count() > 0 ? $transaksi->sum('total_harga') / $monthlyData->count() : 0,
            'bulan_terbaik' => $monthlyData->map(function($monthTransaksi, $month) {
                return [
                    'bulan' => $month,
                    'total' => $monthTransaksi->sum('total_harga')
                ];
            })->sortByDesc('total')->first()
        ];

        $chartData = $monthlyData->map(function($monthTransaksi, $month) {
            return [
                'bulan' => $month,
                'jumlah_transaksi' => $monthTransaksi->count(),
                'total_penjualan' => $monthTransaksi->sum('total_harga')
            ];
        })->values();

        if ($request->get('export') === 'pdf') {
            return $this->exportPDF('tahunan', $transaksi, $summary, $tahun, $chartData);
        }

        return view('laporan.tahunan', compact('transaksi', 'summary', 'tahun', 'chartData'));
    }

    private function exportPDF($type, $data, $summary, $period, $chartData = null)
    {
        $company = [
            'name' => 'Bakery Warda',
            'address' => 'Jl. Contoh No. 123, Kota',
            'phone' => '0812-3456-7890',
            'email' => 'info@bakerywarda.com'
        ];

        $pdf = PDF::loadView('laporan.pdf.' . $type, compact('data', 'summary', 'period', 'chartData', 'company'));
        
        $filename = 'laporan_' . $type . '_' . $period . '.pdf';
        
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'harian');
        $date = $request->get('date');
        
        // Implementasi export Excel bisa ditambahkan nanti
        return redirect()->back()->with('info', 'Fitur export Excel akan segera tersedia');
    }
}
