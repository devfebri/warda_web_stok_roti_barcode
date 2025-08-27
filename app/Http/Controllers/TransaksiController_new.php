<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Cheesecake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Transaksi::with(['kasir', 'details'])
                ->latest('tanggal_transaksi')
                ->get();

            return DataTables::of($data)
                ->addColumn('action', function ($f) {
                    $button = '<div class="btn-group" role="group">';
                    $button .= '<button type="button" class="btn btn-sm btn-info detail-transaksi" data-id="' . $f->id . '" title="Detail"><i class="mdi mdi-eye"></i></button>';
                    if ($f->status == 'pending') {
                        $button .= '<button type="button" class="btn btn-sm btn-success bayar-transaksi" data-id="' . $f->id . '" title="Bayar"><i class="mdi mdi-cash"></i></button>';
                        $button .= '<button type="button" class="btn btn-sm btn-danger batal-transaksi" data-id="' . $f->id . '" title="Batal"><i class="mdi mdi-close"></i></button>';
                    }
                    $button .= '</div>';
                    return $button;
                })
                ->editColumn('total_harga', function ($f) {
                    return $f->formatted_total_harga;
                })
                ->editColumn('status', function ($f) {
                    $badges = [
                        'pending' => 'warning',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger'
                    ];
                    $badge = $badges[$f->status] ?? 'secondary';
                    return '<span class="badge badge-' . $badge . '">' . ucfirst($f->status) . '</span>';
                })
                ->editColumn('tanggal_transaksi', function ($f) {
                    return $f->tanggal_transaksi->format('d-m-Y H:i');
                })
                ->addColumn('kasir_name', function ($f) {
                    return $f->kasir ? $f->kasir->name : 'N/A';
                })
                ->addColumn('total_item', function ($f) {
                    return $f->details->sum('jumlah');
                })
                ->rawColumns(['action', 'status'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('transaksi.index');
    }

    public function statistics(Request $request)
    {
        try {
            $today = now()->format('Y-m-d');
            $currentMonth = now()->format('Y-m');
            
            // Base query
            $query = Transaksi::with(['kasir', 'details.cheesecake']);
            
            // Penjualan Hari Ini
            $penjualanHariIni = $query->clone()
                ->whereDate('tanggal_transaksi', $today)
                ->where('status', 'selesai')
                ->sum('total_harga');
            
            // Transaksi Hari Ini
            $transaksiHariIni = $query->clone()
                ->whereDate('tanggal_transaksi', $today)
                ->where('status', 'selesai')
                ->count();
            
            // Penjualan Bulan Ini
            $penjualanBulanIni = $query->clone()
                ->where('tanggal_transaksi', 'like', $currentMonth . '%')
                ->where('status', 'selesai')
                ->sum('total_harga');
            
            // Total Pelanggan Unik
            $totalPelanggan = $query->clone()
                ->whereNotNull('nama_pelanggan')
                ->where('nama_pelanggan', '!=', '')
                ->distinct('nama_pelanggan')
                ->count('nama_pelanggan');
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'penjualan_hari_ini' => 'Rp ' . number_format($penjualanHariIni, 0, ',', '.'),
                    'penjualan_hari_ini_raw' => $penjualanHariIni,
                    'transaksi_hari_ini' => number_format($transaksiHariIni, 0, ',', '.'),
                    'penjualan_bulan_ini' => 'Rp ' . number_format($penjualanBulanIni, 0, ',', '.'),
                    'penjualan_bulan_ini_raw' => $penjualanBulanIni,
                    'total_pelanggan' => number_format($totalPelanggan, 0, ',', '.')
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat statistik: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        $products = Cheesecake::where('jumlah', '>', 0)
            ->where('status', true)
            ->with('baker')
            ->get();
            
        return view('transaksi.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|string',
            'bayar' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,transfer,kartu'
        ]);

        DB::beginTransaction();
        try {
            $totalHarga = 0;
            $items = json_decode($request->items, true);

            if (!$items || !is_array($items) || count($items) === 0) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Items tidak valid'
                ], 400);
            }

            // Validasi stok dan hitung total
            foreach ($items as $item) {
                if (!isset($item['cheesecake_id']) || !isset($item['jumlah'])) {
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'Data item tidak lengkap'
                    ], 400);
                }

                $cheesecake = Cheesecake::find($item['cheesecake_id']);
                if (!$cheesecake) {
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'Produk dengan ID ' . $item['cheesecake_id'] . ' tidak ditemukan'
                    ], 400);
                }

                if ($cheesecake->jumlah < $item['jumlah']) {
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'Stok ' . $cheesecake->nama . ' tidak mencukupi. Stok tersedia: ' . $cheesecake->jumlah
                    ], 400);
                }

                $totalHarga += $cheesecake->harga * $item['jumlah'];
            }

            // Cek apakah bayar cukup
            if ($request->bayar < $totalHarga) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Jumlah bayar tidak mencukupi'
                ], 400);
            }

            // Buat transaksi
            $transaksi = new Transaksi();
            $transaksi->kode_transaksi = 'TRX-' . date('YmdHis') . '-' . Auth::id();
            $transaksi->kasir_id = Auth::id();
            $transaksi->nama_pelanggan = $request->nama_pelanggan;
            $transaksi->total_harga = $totalHarga;
            $transaksi->bayar = $request->bayar;
            $transaksi->kembalian = $request->bayar - $totalHarga;
            $transaksi->metode_pembayaran = $request->metode_pembayaran;
            $transaksi->status = 'selesai';
            $transaksi->tanggal_transaksi = now();
            $transaksi->catatan = $request->catatan;
            $transaksi->save();

            // Simpan detail transaksi dan kurangi stok
            foreach ($items as $item) {
                $cheesecake = Cheesecake::find($item['cheesecake_id']);
                
                $detail = new TransaksiDetail();
                $detail->transaksi_id = $transaksi->id;
                $detail->cheesecake_id = $item['cheesecake_id'];
                $detail->jumlah = $item['jumlah'];
                $detail->harga_satuan = $cheesecake->harga;
                $detail->subtotal = $cheesecake->harga * $item['jumlah'];
                $detail->save();

                // Kurangi stok
                $cheesecake->jumlah -= $item['jumlah'];
                $cheesecake->save();
            }

            DB::commit();
            return response()->json([
                'status' => 'success', 
                'message' => 'Transaksi berhasil disimpan',
                'data' => $transaksi
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error', 
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $transaksi = Transaksi::with(['kasir', 'details.cheesecake'])->findOrFail($id);
            return response()->json($transaksi);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);
            
            if ($transaksi->status == 'selesai') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi yang sudah selesai tidak dapat dibatalkan'
                ], 400);
            }

            DB::beginTransaction();

            // Kembalikan stok jika status pending
            if ($transaksi->status == 'pending') {
                foreach ($transaksi->details as $detail) {
                    $cheesecake = Cheesecake::find($detail->cheesecake_id);
                    if ($cheesecake) {
                        $cheesecake->jumlah += $detail->jumlah;
                        $cheesecake->save();
                    }
                }
            }

            $transaksi->status = 'dibatalkan';
            $transaksi->save();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membatalkan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProduct($id)
    {
        try {
            $product = Cheesecake::findOrFail($id);
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }
    }

    public function getProductByQr(Request $request)
    {
        try {
            $qrData = $request->qr_data;
            
            // Extract ID from QR code URL
            preg_match('/\/cheesecake\/open\/(\d+)/', $qrData, $matches);
            
            if (!isset($matches[1])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR Code tidak valid'
                ], 400);
            }
            
            $id = $matches[1];
            $product = Cheesecake::findOrFail($id);
            
            if ($product->jumlah <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Stok produk habis'
                ], 400);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $product
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }
    }
}
