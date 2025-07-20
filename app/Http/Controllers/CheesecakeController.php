<?php

namespace App\Http\Controllers;

use App\Models\Cheesecake;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;

class CheesecakeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function showQr($id)
    {
        $roti = Cheesecake::findOrFail($id);
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
        $data = Cheesecake::orderBy('nama', 'asc')->get();
        // $data=Opd::orderBy('id','asc')->get();
        // dd($data);
        if ($request->ajax()) {
            return datatables()->of($data)
                ->addColumn('action', function ($f) {
                    $button = '<div class="tabledit-toolbar btn-toolbar" style="text-align: center;">';
                    $button .= '<div class="btn-group btn-group-sm" style="float: none;">';
                    if(auth()->user()->role == 'baker') {
                        $button .= '<button class="tabledit-edit-button btn btn-sm btn-warning edit-post" data-id=' . $f->id . ' style="float: none; margin: 5px;"><span class="ti-pencil"></span></button>';
                        $button .= '<button class="tabledit-delete-button btn btn-sm btn-danger delete" data-id=' . $f->id . ' style="float: none; margin: 5px;"><span class="ti-trash"></span></button>';
                    }
                    $button .= '<a href="' . route('cheesecakeopen', ['id' => $f->id]) . '"  class="btn btn-sm btn-primary open-view" data-id=' . $f->id . ' style="float: none; margin: 5px;"><span class="ti-receipt"></span></a>';
                    $button .= '<button class=" btn btn-sm btn-primary qr-code" data-id=' . $f->id . ' style="float: none; margin: 5px;"><span class="ti-sharethis-alt"></span></button>';
                    $button .= '</div>';
                    $button .= '</div>';

                    return $button;
                })
                ->editColumn('harga', function ($f) {
                    return $f->harga ? 'Rp. ' . number_format($f->harga, 0, ',', '.') : 'Tidak ada harga';
                })
                ->editColumn('created_at', function ($f) {
                    return $f->created_at ? $f->created_at->format('d-m-Y') : 'Tidak ada tanggal';
                })

                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('cheesecake.index', compact('data'));
    }

    public function store(Request $request)
    {
        if ($request->id) {
            $post = Cheesecake::find($request->id);

            if (!$post) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
            }

            if ($request->hasFile('gambar')) {
                // Hapus gambar lama kalau ada
                if ($post->gambar && file_exists(public_path($post->gambar))) {
                    unlink(public_path($post->gambar));
                }

                $file = $request->file('gambar');
                $filename = $file->getClientOriginalName() . '-' . time() . '.' . $file->extension();
                $pathGambar = public_path('storage/cheesecake/' . auth()->user()->username);

                if (!file_exists($pathGambar)) {
                    mkdir($pathGambar, 0775, true);
                }

                $file->move($pathGambar, $filename);
                $post->gambar = 'storage/cheesecake/' . auth()->user()->username . '/' . $filename;
            }

            $post->nama = $request->nama;
            $post->ukuran = $request->ukuran;
            $post->deskripsi = $request->deskripsi;
            $post->jumlah = $request->jumlah;
            $post->harga = $request->harga;
            $post->tanggal_dibuat = $request->tanggal_dibuat;
            $post->save();
            return response()->json(['status' => 'success', 'data' => $post]);
        } else {
            // Handle upload gambar roti
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = $file->getClientOriginalName() . '-' . time() . '.' . $file->extension();
                $pathGambar = public_path('storage/cheesecake/' . auth()->user()->username);

                if (!file_exists($pathGambar)) {
                    mkdir($pathGambar, 0775, true);
                }

                $file->move($pathGambar, $filename);
            } else {
                $filename = null;
            }

            // Simpan data roti
            $post = new Cheesecake();
            $post->nama = $request->nama;
            $post->ukuran = $request->ukuran;
            $post->deskripsi = $request->deskripsi;
            $post->harga = $request->harga;
            $post->tanggal_dibuat = $request->tanggal_dibuat;
            $post->gambar = 'storage/cheesecake/' . auth()->user()->username . '/' . $filename;
            $post->save();

            // Generate QR Code
            $qrCode = new QrCode(route('cheesecakeopen', ['id' => $post->id]));
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $qrFileName = 'qrcode_' . time() . '.png';
            $pathQr = public_path('storage/qrcode/' . auth()->user()->username);

            if (!file_exists($pathQr)) {
                mkdir($pathQr, 0775, true);
            }

            $result->saveToFile($pathQr . '/' . $qrFileName);

            // Simpan nama file QR ke database
            $post->qr_code = 'storage/qrcode/' . auth()->user()->username . '/' . $qrFileName;
            $post->save();

            return response()->json([
                'status' => 'success',
                'data' => $post,
                'qr_url' => asset('img/' . $qrFileName)
            ]);
        }
    }


    public function destroy($id)
    {
        $cheesecake = Cheesecake::findOrFail($id);
        if ($cheesecake->gambar) {
            $gambarPath = public_path($cheesecake->gambar);
            if (file_exists($gambarPath)) {
                unlink($gambarPath);
            }
        }

        if ($cheesecake->qr_code) {
            $qrCodePath = public_path($cheesecake->qr_code);
            if (file_exists($qrCodePath)) {
                unlink($qrCodePath);
            }
        }

        $cheesecake->delete();

        return response()->json(['status' => 'success', 'message' => 'Cheesecake deleted successfully']);
    }

    public function open($id)
    {
        $cheesecake = Cheesecake::findOrFail($id);
        return view('cheesecake.open', compact('cheesecake'));
    }

    public function edit($id)
    {
        $cheesecake = Cheesecake::findOrFail($id);
        return response()->json($cheesecake);
    }
}
