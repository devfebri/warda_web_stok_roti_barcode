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
                if (!$cheesecake || $cheesecake->jumlah < $item['jumlah']) {
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'Stok ' . ($cheesecake ? $cheesecake->nama : 'produk') . ' tidak mencukupi'
                    ], 400);
                }
                $totalHarga += $cheesecake->harga * $item['jumlah'];
            }

            // Validasi pembayaran
            if ($request->bayar < $totalHarga) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Jumlah bayar tidak mencukupi'
                ], 400);
            }

            // Create transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => Transaksi::generateKodeTransaksi(),
                'kasir_id' => Auth::id(),
                'nama_pelanggan' => $request->nama_pelanggan,
                'total_harga' => $totalHarga,
                'bayar' => $request->bayar,
                'kembalian' => $request->bayar - $totalHarga,
                'status' => 'selesai',
                'metode_pembayaran' => $request->metode_pembayaran,
                'catatan' => $request->catatan,
                'tanggal_transaksi' => Carbon::now()
            ]);

            // Create detail dan update stok
            foreach ($items as $item) {
                $cheesecake = Cheesecake::find($item['cheesecake_id']);
                
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'cheesecake_id' => $item['cheesecake_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $cheesecake->harga,
                    'subtotal' => $cheesecake->harga * $item['jumlah']
                ]);

                // Update stok
                $cheesecake->decrement('jumlah', $item['jumlah']);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil diproses',
                'data' => $transaksi
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['kasir', 'details.cheesecake'])->findOrFail($id);
        return response()->json($transaksi);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $transaksi = Transaksi::with('details')->findOrFail($id);
            
            if ($transaksi->status == 'selesai') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi yang sudah selesai tidak dapat dibatalkan'
                ], 400);
            }

            // Kembalikan stok jika transaksi sudah mengurangi stok
            foreach ($transaksi->details as $detail) {
                $detail->cheesecake->increment('jumlah', $detail->jumlah);
            }

            $transaksi->update(['status' => 'dibatalkan']);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProduct($id)
    {
        $product = Cheesecake::with('baker')->find($id);
        if (!$product) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }
        return response()->json($product);
    }

    public function getProductByQr(Request $request)
    {
        $qrData = $request->qr_data;
        // Ekstrak ID dari QR data (asumsi format tertentu)
        preg_match('/id=(\d+)/', $qrData, $matches);
        $id = $matches[1] ?? null;
        
        if (!$id) {
            return response()->json(['error' => 'QR Code tidak valid'], 400);
        }

        return $this->getProduct($id);
    }
}
