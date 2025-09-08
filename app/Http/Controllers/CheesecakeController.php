<?php

namespace App\Http\Controllers;

use App\Models\Cheesecake;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CheesecakeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function showQr($id)
    {
        $roti = Cheesecake::findOrFail($id);
        
        // Check if baker can only view QR codes of their own cheesecakes
        if (Auth::user()->role == 'baker' && $roti->baker_id != Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki akses untuk melihat QR code ini'], 403);
        }
        
        if ($roti->qr_code) {
            return response()->json([
                'qr_url' => asset($roti->qr_code)
            ]);
        } else {
            return response()->json([
                'qr_url' => null
            ]);
        }
    }
    public function index(Request $request)
    {
        // Auto-update expired status setiap kali halaman dibuka
        Cheesecake::updateExpiredStatus();
        
        $query = Cheesecake::with(['baker', 'roti']);
        
        // Filter by baker_id if user is baker
        if (Auth::user()->role == 'baker') {
            $query->where('baker_id', Auth::id());
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        $rotis = \App\Models\Roti::all(); // Get rotis for dropdown
        
        // Handle statistics AJAX request
        if ($request->ajax() && $request->has('statistics')) {
            return response()->json([
                'data' => $data->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->roti ? $item->roti->nama : '-',
                        'kode_produk' => $item->kode_produk,
                        'jumlah' => $item->jumlah,
                        'harga_raw' => (float) $item->harga,
                        'tanggal_dibuat' => $item->tanggal_dibuat ? $item->tanggal_dibuat->format('Y-m-d') : null,
                        'status' => $item->status,
                        'is_expired' => $item->is_expired
                    ];
                })
            ]);
        }
        
        // Handle DataTables AJAX request
        if ($request->ajax()) {
            return datatables()->of($data)
                ->addColumn('action', function ($f) {
                    $button = '<div class="tabledit-toolbar btn-toolbar" style="text-align: center;">';
                    $button .= '<div class="btn-group btn-group-sm" style="float: none;">';
                    
                    if(Auth::user()->role == 'baker') {
                        $button .= '<button class="tabledit-edit-button btn btn-sm btn-warning edit-post" data-id=' . $f->id . ' style="float: none; margin: 5px;" title="Edit"><span class="ti-pencil"></span></button>';
                        
                        // Form hapus dengan PHP
                        $button .= '<form method="POST" action="' . route(Auth::user()->role.'_cheesecakedelete', $f->id) . '" style="display: inline-block; margin: 5px;" onsubmit="return confirm(\'Yakin ingin menghapus data ini?\')">';
                        $button .= csrf_field();
                        $button .= method_field('DELETE');
                        $button .= '<button type="submit" class="btn btn-sm btn-danger" title="Hapus"><span class="ti-trash"></span></button>';
                        $button .= '</form>';
                    }
                    
                    $button .= '<a href="' . route('cheesecakeopen', ['id' => $f->id]) . '"  class="btn btn-sm btn-info open-view" data-id=' . $f->id . ' style="float: none; margin: 5px;" title="Lihat Detail"><span class="ti-eye"></span></a>';
                    $button .= '<button class="btn btn-sm btn-primary qr-code" data-id=' . $f->id . ' style="float: none; margin: 5px;" title="QR Code"><span class="ti-sharethis-alt"></span></button>';
                    
                    $button .= '</div>';
                    $button .= '</div>';

                    return $button;
                })
                ->addColumn('baker_name', function ($f) {
                    return $f->baker ? $f->baker->name : 'N/A';
                })
                ->addColumn('kode_produk', function ($f) {
                    return $f->kode_produk;
                })
                ->addColumn('nama', function ($f) {
                    return $f->roti ? $f->roti->nama : '-';
                })
                ->addColumn('status_expired', function ($f) {
                    if ($f->is_expired || !$f->status) {
                        return '<span class="badge badge-danger">Expired</span><br><small><i>tidak layak di konsumsi</i></small>';
                    } else {
                        $hari = $f->hari_tersisa;
                        $class = $hari <= 1 ? 'badge-warning' : 'badge-success';
                        return '<span class="badge ' . $class . '">' . $hari . ' hari tersisa</span><br><small><i>layak di konsumsi</i></small>';
                    }
                })
                ->editColumn('harga', function ($f) {
                    return $f->formatted_harga;
                })
                ->editColumn('total', function ($f) {
                    return 'Rp '.number_format($f->total,0);
                })
                ->addColumn('harga_raw', function ($f) {
                    return (float) $f->harga;
                })
                ->editColumn('created_at', function ($f) {
                    return $f->created_at ? $f->created_at->format('d-m-Y H:i') : 'Tidak ada tanggal';
                })
                ->editColumn('tanggal_dibuat', function ($f) {
                    return $f->tanggal_dibuat ? $f->tanggal_dibuat->format('d-m-Y') : 'Tidak ada tanggal';
                })
                ->rawColumns(['action', 'status_expired'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('cheesecake.index', compact('data', 'rotis'));
    }

    public function statistics(Request $request)
    {
        // Auto-update expired status
        Cheesecake::updateExpiredStatus();
        
        $query = Cheesecake::with('baker');
        
        // Filter by baker_id if user is baker
        if (Auth::user()->role == 'baker') {
            $query->where('baker_id', Auth::id());
        }
        
        $data = $query->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'data' => $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'jumlah' => $item->jumlah,
                    'harga_raw' => (float) $item->harga,
                    'tanggal_dibuat' => $item->tanggal_dibuat ? $item->tanggal_dibuat->format('Y-m-d') : null,
                    'status' => $item->status,
                    'is_expired' => $item->is_expired
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'roti_id' => 'required|exists:rotis,id',
            'jumlah' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'tanggal_dibuat' => 'required|date',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        // Validate request
        $validator = \Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->id) {
            $post = Cheesecake::find($request->id);

            if (!$post) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
            }

            // Check if baker can only edit their own cheesecakes
            if (Auth::user()->role == 'baker' && $post->baker_id != Auth::id()) {
                return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki akses untuk mengedit data ini'], 403);
            }

            if ($request->hasFile('gambar')) {
                // Hapus gambar lama kalau ada
                if ($post->gambar && file_exists(public_path($post->gambar))) {
                    unlink(public_path($post->gambar));
                }

                $file = $request->file('gambar');
                $filename = $file->getClientOriginalName() . '-' . time() . '.' . $file->extension();
                $pathGambar = public_path('storage/cheesecake/' . Auth::user()->username);

                if (!file_exists($pathGambar)) {
                    mkdir($pathGambar, 0775, true);
                }

                $file->move($pathGambar, $filename);
                $post->gambar = 'storage/cheesecake/' . Auth::user()->username . '/' . $filename;
            }

            $post->roti_id = $request->roti_id;
            $post->deskripsi = $request->deskripsi;
            $post->jumlah = $request->jumlah;
            $post->total = $request->total;
            $post->harga = $request->harga;
            $post->total = $request->total;
            $post->tanggal_dibuat = $request->tanggal_dibuat;
            $post->baker_id = Auth::id();
            $post->save();
            return response()->json(['status' => 'success', 'data' => $post]);
        } else {
            // Handle upload gambar roti
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = $file->getClientOriginalName() . '-' . time() . '.' . $file->extension();
                $pathGambar = public_path('storage/cheesecake/' . Auth::user()->username);

                if (!file_exists($pathGambar)) {
                    mkdir($pathGambar, 0775, true);
                }

                $file->move($pathGambar, $filename);
            } else {
                $filename = null;
            }

            // Simpan data roti
            $post = new Cheesecake();
             $post->kode_produk = Cheesecake::generateKodeproduk();
            $post->roti_id = $request->roti_id;
            $post->deskripsi = $request->deskripsi;
            $post->jumlah = $request->jumlah;
            $post->total = $request->total;
            $post->harga = $request->harga;
            $post->tanggal_dibuat = $request->tanggal_dibuat;
            $post->baker_id = Auth::id();
            $post->gambar = $filename ? 'storage/cheesecake/' . Auth::user()->username . '/' . $filename : null;
            $post->save();

            // Generate QR Code
            $qrCode = new QrCode(route('cheesecakeopen', ['id' => $post->id]));
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $qrFileName = 'qrcode_' . time() . '.png';
            $pathQr = public_path('storage/qrcode/' . Auth::user()->username);

            if (!file_exists($pathQr)) {
                mkdir($pathQr, 0775, true);
            }

            $result->saveToFile($pathQr . '/' . $qrFileName);

            // Simpan nama file QR ke database
            $post->qr_code = 'storage/qrcode/' . Auth::user()->username . '/' . $qrFileName;
            $post->save();

            return response()->json([
                'status' => 'success',
                'data' => $post,
                'qr_url' => asset('img/' . $qrFileName)
            ]);
        }
    }


    public function destroy(Request $request, $id)
    {
        try {
            $cheesecake = Cheesecake::findOrFail($id);
            
            // Check if baker can only delete their own cheesecakes
            if (Auth::user()->role == 'baker' && $cheesecake->baker_id != Auth::id()) {
                if ($request->ajax()) {
                    return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki akses untuk menghapus data ini'], 403);
                }
                return redirect()->route(Auth::user()->role.'_cheesecake')->with('error', 'Anda tidak memiliki akses untuk menghapus data ini');
            }
            
            // Hapus file gambar jika ada
            if ($cheesecake->gambar) {
                $gambarPath = public_path($cheesecake->gambar);
                if (file_exists($gambarPath)) {
                    unlink($gambarPath);
                }
            }

            // Hapus file QR code jika ada
            if ($cheesecake->qr_code) {
                $qrCodePath = public_path($cheesecake->qr_code);
                if (file_exists($qrCodePath)) {
                    unlink($qrCodePath);
                }
            }

            $cheesecake->delete();

            // Jika request adalah AJAX, return JSON
            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
            }
            
            // Jika request adalah form submission, redirect dengan pesan sukses
            return redirect()->route(Auth::user()->role.'_cheesecake')->with('success', 'Data berhasil dihapus');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
            }
            
            return redirect()->route(Auth::user()->role.'_cheesecake')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function open($id)
    {
        try {
            $cheesecake = Cheesecake::with('baker')->findOrFail($id);
            
            // Check if baker can only view their own cheesecakes
            if (Auth::user()->role == 'baker' && $cheesecake->baker_id != Auth::id()) {
                abort(403, 'Anda tidak memiliki akses untuk melihat data ini');
            }
            
            return view('cheesecake.open_debug', compact('cheesecake'));
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error loading cheesecake detail: ' . $e->getMessage());
            
            // Return ke view dengan data kosong untuk menampilkan error state
            return view('cheesecake.open_debug', ['cheesecake' => null]);
        }
    }

    public function edit($id)
    {
        $cheesecake = Cheesecake::with('roti')->findOrFail($id);
        
        // Check if baker can only edit their own cheesecakes
        if (Auth::user()->role == 'baker' && $cheesecake->baker_id != Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki akses untuk mengedit data ini'], 403);
        }
        
        return response()->json($cheesecake);
    }
}
