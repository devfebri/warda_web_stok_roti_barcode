<?php

namespace App\Http\Controllers;

use App\Models\Cheesecake;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function harian(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        
        $data = Cheesecake::whereDate('created_at', $tanggal)
            ->with('baker')
            ->get();

        $summary = [
            'total_produk' => $data->count(),
            'total_jumlah' => $data->sum('jumlah'),
            'total_nilai' => $data->sum(function($item) {
                return $item->jumlah * $item->harga;
            }),
            'produk_expired' => $data->filter(function($item) {
                return $item->is_expired;
            })->count()
        ];

        if ($request->ajax()) {
            return datatables()->of($data)
                ->addColumn('baker_name', function($row) {
                    return $row->baker ? $row->baker->name : 'N/A';
                })
                ->addColumn('total_nilai', function($row) {
                    return 'Rp ' . number_format($row->jumlah * $row->harga, 0, ',', '.');
                })
                ->addColumn('status_expired', function($row) {
                    return $row->is_expired ? 
                        '<span class="badge badge-danger">Expired</span>' : 
                        '<span class="badge badge-success">' . $row->hari_tersisa . ' hari tersisa</span>';
                })
                ->rawColumns(['status_expired'])
                ->make(true);
        }

        return view('laporan.harian', compact('data', 'summary', 'tanggal'));
    }

    public function bulanan(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->format('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();

        $data = Cheesecake::whereBetween('created_at', [$startDate, $endDate])
            ->with('baker')
            ->get()
            ->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            });

        $summary = [
            'total_produk' => Cheesecake::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_jumlah' => Cheesecake::whereBetween('created_at', [$startDate, $endDate])->sum('jumlah'),
            'total_nilai' => Cheesecake::whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->sum(function($item) {
                    return $item->jumlah * $item->harga;
                }),
            'rata_rata_harian' => Cheesecake::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as jumlah')
                ->groupBy('tanggal')
                ->get()
                ->avg('jumlah')
        ];

        $chartData = $data->map(function($items, $date) {
            return [
                'tanggal' => $date,
                'jumlah' => $items->count(),
                'total_nilai' => $items->sum(function($item) {
                    return $item->jumlah * $item->harga;
                })
            ];
        })->values();

        return view('laporan.bulanan', compact('data', 'summary', 'bulan', 'chartData'));
    }

    public function tahunan(Request $request)
    {
        $tahun = $request->get('tahun', Carbon::now()->year);
        $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfYear();
        $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfYear();

        $data = Cheesecake::whereBetween('created_at', [$startDate, $endDate])
            ->with('baker')
            ->get()
            ->groupBy(function($item) {
                return $item->created_at->format('Y-m');
            });

        $summary = [
            'total_produk' => Cheesecake::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_jumlah' => Cheesecake::whereBetween('created_at', [$startDate, $endDate])->sum('jumlah'),
            'total_nilai' => Cheesecake::whereBetween('created_at', [$startDate, $endDate])
                ->get()
                ->sum(function($item) {
                    return $item->jumlah * $item->harga;
                }),
            'rata_rata_bulanan' => Cheesecake::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('YEAR(created_at) as tahun, MONTH(created_at) as bulan, COUNT(*) as jumlah')
                ->groupBy('tahun', 'bulan')
                ->get()
                ->avg('jumlah')
        ];

        $chartData = $data->map(function($items, $month) {
            return [
                'bulan' => $month,
                'jumlah' => $items->count(),
                'total_nilai' => $items->sum(function($item) {
                    return $item->jumlah * $item->harga;
                })
            ];
        })->values();

        return view('laporan.tahunan', compact('data', 'summary', 'tahun', 'chartData'));
    }

    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'harian');
        $date = $request->get('date');
        
        // Implementasi export Excel bisa ditambahkan nanti
        return redirect()->back()->with('info', 'Fitur export Excel akan segera tersedia');
    }
}
