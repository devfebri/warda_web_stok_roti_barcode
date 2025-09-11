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
        
        // Group data berdasarkan tanggal_dibuat dan roti_id
        $groupedData = $this->groupCheesecakeData($data);
        
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
            return datatables()->of($groupedData)
                ->addColumn('action', function ($f) {
                    $button = '<div class="tabledit-toolbar btn-toolbar" style="text-align: center;">';
                    $button .= '<div class="btn-group btn-group-sm" style="float: none;">';
                    
                    // Tombol untuk single item atau first item dari group
                    if(Auth::user()->role == 'baker') {
                        $button .= '<button class="tabledit-edit-button btn btn-sm btn-warning edit-post" data-id=' . $f['id'] . ' style="float: none; margin: 2px;" title="Edit"><span class="ti-pencil"></span></button>';
                        
                        // Form hapus dengan PHP
                        $button .= '<form method="POST" action="' . route(Auth::user()->role.'_cheesecakedelete', $f['id']) . '" style="display: inline-block; margin: 2px;" onsubmit="return confirm(\'Yakin ingin menghapus data ini?\')">';
                        $button .= csrf_field();
                        $button .= method_field('DELETE');
                        $button .= '<button type="submit" class="btn btn-sm btn-danger" title="Hapus"><span class="ti-trash"></span></button>';
                        $button .= '</form>';
                    }
                    
                    // Tombol view detail
                    $button .= '<a href="' . route('cheesecakeopen', ['id' => $f['id']]) . '"  class="btn btn-sm btn-info open-view" data-id=' . $f['id'] . ' style="float: none; margin: 2px;" title="Lihat Detail"><span class="ti-eye"></span></a>';
                    
                    // Tombol QR Code
                    $button .= '<button class="btn btn-sm btn-primary qr-code" data-id=' . $f['id'] . ' style="float: none; margin: 2px;" title="QR Code"><span class="ti-sharethis-alt"></span></button>';
                    
                    // Jika data grouped, tampilkan tombol khusus group
                    if ($f['is_grouped'] && $f['group_count'] > 1) {
                        $button .= '<button class="btn btn-sm btn-success view-group" data-group-id="' . $f['group_id'] . '" style="float: none; margin: 2px;" title="Lihat Semua ' . $f['group_count'] . ' Items dalam Group Ini"><span class="ti-layers"></span> ' . $f['group_count'] . '</button>';
                    }
                    
                    $button .= '</div>';
                    $button .= '</div>';

                    return $button;
                })
                ->addColumn('baker_name', function ($f) {
                    if ($f['is_grouped'] && $f['group_count'] > 1) {
                        return $f['baker_name'] . ' <small class="badge badge-secondary">Group of ' . $f['group_count'] . '</small>';
                    }
                    return $f['baker_name'];
                })
                ->addColumn('kode_produk', function ($f) {
                    if ($f['is_grouped'] && $f['group_count'] > 1) {
                        return '<div class="text-center">' .
                               '<span class="badge badge-info mb-1">' . $f['group_count'] . ' items grouped</span><br>' .
                               '<small class="text-muted">' . $f['kode_produk'] . '</small>' .
                               '</div>';
                    }
                    return $f['kode_produk'];
                })
                ->addColumn('nama', function ($f) {
                    if ($f['is_grouped'] && $f['group_count'] > 1) {
                        return '<strong>' . $f['nama'] . '</strong> <small class="text-success"><i class="ti-layers"></i> Grouped</small>';
                    }
                    return $f['nama'];
                })
                ->addColumn('jumlah_display', function ($f) {
                    if ($f['is_grouped'] && $f['group_count'] > 1) {
                        return '<strong>' . $f['total_jumlah'] . '</strong> <small class="text-muted">(' . $f['group_count'] . ' entries)</small>';
                    }
                    return $f['jumlah'];
                })
                ->addColumn('status_expired', function ($f) {
                    if ($f['is_expired'] || !$f['status']) {
                        return '<span class="badge badge-danger">Expired</span><br><small><i>tidak layak di konsumsi</i></small>';
                    } else {
                        $hari = $f['hari_tersisa'];
                        $class = $hari <= 1 ? 'badge-warning' : 'badge-success';
                        return '<span class="badge ' . $class . '">' . $hari . ' hari tersisa</span><br><small><i>layak di konsumsi</i></small>';
                    }
                })
                ->editColumn('harga', function ($f) {
                    if ($f['is_grouped'] && $f['group_count'] > 1) {
                        return 'Rp ' . number_format($f['avg_harga'], 0) . ' <small class="text-muted">(avg)</small>';
                    }
                    return 'Rp ' . number_format($f['harga'], 0);
                })
                ->editColumn('total', function ($f) {
                    if ($f['is_grouped'] && $f['group_count'] > 1) {
                        return 'Rp ' . number_format($f['total_nilai'], 0);
                    }
                    return 'Rp '.number_format($f['total'], 0);
                })
                ->addColumn('harga_raw', function ($f) {
                    return (float) $f['harga'];
                })
                ->editColumn('created_at', function ($f) {
                    if ($f['is_grouped'] && $f['group_count'] > 1) {
                        return $f['created_at_formatted'] . ' <small class="text-success">(Latest)</small>';
                    }
                    return $f['created_at_formatted'];
                })
                ->editColumn('tanggal_dibuat', function ($f) {
                    return $f['tanggal_dibuat_formatted'];
                })
                ->rawColumns(['action', 'status_expired', 'kode_produk', 'nama', 'jumlah_display', 'harga', 'total', 'created_at','tanggal_expired','baker_name'])
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
        
        // Get all data for general statistics (stok, nilai, expired)
        $allData = $query->orderBy('created_at', 'desc')->get();
        
        // Get today's production only for total produk
        $todayQuery = Cheesecake::with('baker');
        if (Auth::user()->role == 'baker') {
            $todayQuery->where('baker_id', Auth::id());
        }
        $todayData = $todayQuery->whereDate('created_at', today())->get();
        
        return response()->json([
            'data' => $allData->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'jumlah' => $item->jumlah,
                    'harga_raw' => (float) $item->harga,
                    'tanggal_dibuat' => $item->tanggal_dibuat ? $item->tanggal_dibuat->format('Y-m-d') : null,
                    'status' => $item->status,
                    'is_expired' => $item->is_expired
                ];
            }),
            'today_production' => $todayData->count(), // Total produk hari ini
            'today_data' => $todayData->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'jumlah' => $item->jumlah,
                    'harga_raw' => (float) $item->harga,
                    'created_at' => $item->created_at->format('Y-m-d H:i:s')
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
            
            // Retry mechanism untuk kode produk yang unique
            $maxRetries = 5;
            $retryCount = 0;
            $saved = false;
            
            while (!$saved && $retryCount < $maxRetries) {
                try {
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
                    $saved = true;
                } catch (\Illuminate\Database\QueryException $e) {
                    // Jika error adalah duplicate entry untuk kode_produk, coba lagi
                    if ($e->errorInfo[1] == 1062 && strpos($e->getMessage(), 'kode_produk') !== false) {
                        $retryCount++;
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
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal generate kode produk unik setelah ' . $maxRetries . ' percobaan'
                ], 500);
            }

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

    /**
     * Method untuk grouping data cheesecake berdasarkan tanggal_dibuat dan roti_id
     */
    private function groupCheesecakeData($data)
    {
        $groupedData = collect();
        
        // Group data berdasarkan tanggal_dibuat dan roti_id
        $groups = $data->groupBy(function ($item) {
            return $item->tanggal_dibuat->format('Y-m-d') . '_' . $item->roti_id;
        });
        
        foreach ($groups as $groupKey => $items) {
            if ($items->count() > 1) {
                // Jika ada lebih dari 1 item, buat group
                $firstItem = $items->first();
                $totalJumlah = $items->sum('jumlah');
                $totalNilai = $items->sum(function ($item) {
                    return $item->jumlah * $item->harga;
                });
                $avgHarga = $items->avg('harga');
                $latestItem = $items->sortByDesc('created_at')->first();
                
                $groupedData->push([
                    'id' => $firstItem->id,
                    'group_id' => $groupKey,
                    'is_grouped' => true,
                    'group_count' => $items->count(),
                    'group_items' => $items,
                    'roti_id' => $firstItem->roti_id,
                    'baker_id' => $firstItem->baker_id,
                    'baker_name' => $firstItem->baker ? $firstItem->baker->name : 'N/A',
                    'kode_produk' => $firstItem->kode_produk . ' (+' . ($items->count() - 1) . ')',
                    'nama' => $firstItem->roti ? $firstItem->roti->nama : '-',
                    'jumlah' => $totalJumlah,
                    'total_jumlah' => $totalJumlah,
                    'harga' => $avgHarga,
                    'avg_harga' => $avgHarga,
                    'total' => $totalNilai,
                    'total_nilai' => $totalNilai,
                    'tanggal_dibuat' => $firstItem->tanggal_dibuat,
                    'tanggal_dibuat_formatted' => $firstItem->tanggal_dibuat ? $firstItem->tanggal_dibuat->format('d-m-Y') : 'Tidak ada tanggal',
                    'tanggal_expired' => $firstItem->tanggal_dibuat ? $firstItem->tanggal_dibuat->addDays(3)->format('d-m-Y') : 'Tidak ada tanggal',
                    'created_at' => $latestItem->created_at,
                    'created_at_formatted' => $latestItem->created_at ? $latestItem->created_at->format('d-m-Y H:i') : 'Tidak ada tanggal',
                    'status' => $firstItem->status,
                    'is_expired' => $firstItem->is_expired,
                    'hari_tersisa' => $firstItem->hari_tersisa,
                    'gambar' => $firstItem->gambar,
                    'qr_code' => $firstItem->qr_code,
                    'deskripsi' => $firstItem->deskripsi . ' (Group of ' . $items->count() . ' items)',
                ]);
            } else {
                // Jika hanya 1 item, tampilkan normal
                $item = $items->first();
                $groupedData->push([
                    'id' => $item->id,
                    'group_id' => null,
                    'is_grouped' => false,
                    'group_count' => 1,
                    'group_items' => collect([$item]),
                    'roti_id' => $item->roti_id,
                    'baker_id' => $item->baker_id,
                    'baker_name' => $item->baker ? $item->baker->name : 'N/A',
                    'kode_produk' => $item->kode_produk,
                    'nama' => $item->roti ? $item->roti->nama : '-',
                    'jumlah' => $item->jumlah,
                    'total_jumlah' => $item->jumlah,
                    'harga' => $item->harga,
                    'avg_harga' => $item->harga,
                    'total' => $item->total,
                    'total_nilai' => $item->total,
                    'tanggal_dibuat' => $item->tanggal_dibuat,
                    'tanggal_dibuat_formatted' => $item->tanggal_dibuat ? $item->tanggal_dibuat->format('d-m-Y') : 'Tidak ada tanggal',
                    'tanggal_expired' => $item->tanggal_dibuat ? $item->tanggal_dibuat->addDays(3)->format('d-m-Y') : 'Tidak ada tanggal',
                    'created_at' => $item->created_at,
                    'created_at_formatted' => $item->created_at ? $item->created_at->format('d-m-Y H:i') : 'Tidak ada tanggal',
                    'status' => $item->status,
                    'is_expired' => $item->is_expired,
                    'hari_tersisa' => $item->hari_tersisa,
                    'gambar' => $item->gambar,
                    'qr_code' => $item->qr_code,
                    'deskripsi' => $item->deskripsi,
                ]);
            }
        }
        
        return $groupedData->sortByDesc('created_at');
    }

    /**
     * Method untuk mendapatkan detail group
     */
    public function getGroupDetails(Request $request, $groupId)
    {
        if (!$request->ajax()) {
            return response()->json(['status' => 'error', 'message' => 'Invalid request'], 400);
        }

        $groupParts = explode('_', $groupId);
        if (count($groupParts) != 2) {
            return response()->json(['status' => 'error', 'message' => 'Invalid group ID'], 400);
        }

        $tanggal = $groupParts[0];
        $rotiId = $groupParts[1];

        $query = Cheesecake::with(['baker', 'roti'])
            ->whereDate('tanggal_dibuat', $tanggal)
            ->where('roti_id', $rotiId);

        // Filter by baker_id if user is baker
        if (Auth::user()->role == 'baker') {
            $query->where('baker_id', Auth::id());
        }

        $items = $query->orderBy('created_at', 'desc')->get();

        if ($items->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $items->map(function ($item) {
                // Generate action buttons untuk setiap item
                $actions = '';
                if (Auth::user()->role == 'baker') {
                    $actions .= '<button class="btn btn-sm btn-warning edit-post" data-id="' . $item->id . '" title="Edit"><span class="ti-pencil"></span></button> ';
                    $actions .= '<button class="btn btn-sm btn-danger delete-item" data-id="' . $item->id . '" title="Hapus"><span class="ti-trash"></span></button> ';
                }
                $actions .= '<a href="' . route('cheesecakeopen', ['id' => $item->id]) . '" class="btn btn-sm btn-info" title="Detail"><span class="ti-eye"></span></a> ';
                $actions .= '<button class="btn btn-sm btn-primary qr-code" data-id="' . $item->id . '" title="QR Code"><span class="ti-sharethis-alt"></span></button>';
                
                return [
                    'id' => $item->id,
                    'kode_produk' => $item->kode_produk,
                    'nama_roti' => $item->roti ? $item->roti->nama : '-',
                    'baker_name' => $item->baker ? $item->baker->name : 'N/A',
                    'jumlah' => $item->jumlah,
                    'harga' => $item->formatted_harga,
                    'total' => 'Rp ' . number_format($item->total, 0),
                    'tanggal_dibuat' => $item->tanggal_dibuat ? $item->tanggal_dibuat->format('d-m-Y') : '-',
                    'created_at' => $item->created_at ? $item->created_at->format('d-m-Y H:i') : '-',
                    'deskripsi' => $item->deskripsi,
                    'status' => $item->status ? 'Aktif' : 'Tidak Aktif',
                    'is_expired' => $item->is_expired,
                    'hari_tersisa' => $item->hari_tersisa,
                    'actions' => $actions,
                ];
            }),
            'summary' => [
                'total_items' => $items->count(),
                'total_jumlah' => $items->sum('jumlah'),
                'total_nilai' => 'Rp ' . number_format($items->sum('total'), 0),
                'avg_harga' => 'Rp ' . number_format($items->avg('harga'), 0),
                'tanggal_dibuat' => $items->first()->tanggal_dibuat ? $items->first()->tanggal_dibuat->format('d-m-Y') : '-',
                'nama_roti' => $items->first()->roti ? $items->first()->roti->nama : '-',
            ]
        ]);
    }
}
