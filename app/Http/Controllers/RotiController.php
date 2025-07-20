<?php

namespace App\Http\Controllers;

use App\Models\Roti;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;

class RotiController extends Controller
{

    public function showQr($id)
    {
        $roti = Roti::findOrFail($id);
        if ($roti->qr_code) {
            
            return response()->json([
                'qr_url' => asset( $roti->qr_code)
            ]);
        } else {
            return response()->json([
                'qr_url' => null
            ]);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Roti::orderBy('nama_roti', 'asc')->get();
        // $data=Opd::orderBy('id','asc')->get();
        // dd($data);
        if ($request->ajax()) {
            return datatables()->of($data)
                ->addColumn('action', function ($f) {
                    $button = '<div class="tabledit-toolbar btn-toolbar" style="text-align: center;">';
                    $button .= '<div class="btn-group btn-group-sm" style="float: none;">';
                    // $button .= '<a href="' . route('baker_rotiopen', ['id' => $f->id]) . '" class="tabledit-edit-button btn btn-sm btn-primary edit-post" style="float: none; margin: 5px;"><span class="ti-receipt"></span></a>';

                    $button.='<button class="tabledit-delete-button btn btn-sm btn-danger delete" data-id='.$f->id.' style="float: none; margin: 5px;"><span class="ti-trash"></span></button>';
                    $button.='<button class=" btn btn-sm btn-primary qr-code" data-id='.$f->id. ' style="float: none; margin: 5px;"><span class="ti-sharethis-alt"></span></button>';
                    $button .= '</div>';
                    $button .= '</div>';

                    return $button;
                })
                ->editColumn('harga', function ($f) {
                    if ($f->gambar_roti) {
                        return '<img src="' . asset($f->gambar_roti) . '" class="img-fluid" style="max-width: 100px; height: auto;">';
                    } else {
                        return '<span class="text-muted">Tidak ada gambar</span>';
                    }
                })

                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('roti.index', compact('data'));
    }

   
    public function store(Request $request)
    {
        
        if ($request->id) {
            // EDIT DATA
            $cek = Cuti::find($request->id); //required jenis cuti tidak berubah
            if ($request->has('gambar_roti')) {
                $file = $request->file('gambar_roti');
                $filename = $file->getClientOriginalName() . '-' . time() . '.' . $file->extension();
                $file->move(public_path() . '/storage/roti/' . auth()->user()->username, $filename);
            } else {
                $filename = null;
            }

            $post   =   Cuti::find($request->id);
            $post->nama_roti = $request->nama_roti;
            $post->rasa_roti = $request->rasa_roti;
            $post->deskripsi_roti = $request->deskripsi_roti;
            $post->harga_roti = $request->harga_roti;
            $post->gambar_roti = $filename;
            $post->save();
        } else {
            // Handle upload gambar roti
            if ($request->hasFile('gambar_roti')) {
                $file = $request->file('gambar_roti');
                $filename = $file->getClientOriginalName() . '-' . time() . '.' . $file->extension();
                $pathGambar = public_path('storage/roti/' . auth()->user()->username);

                if (!file_exists($pathGambar)) {
                    mkdir($pathGambar, 0775, true);
                }

                $file->move($pathGambar, $filename);
            } else {
                $filename = null;
            }

            // Simpan data roti
            $post = new Roti();
            $post->nama_roti = $request->nama_roti;
            $post->rasa_roti = $request->rasa_roti;
            $post->deskripsi_roti = $request->deskripsi_roti;
            $post->harga_roti = $request->harga_roti;
            $post->gambar_roti = 'storage/roti/'.auth()->user()->username.'/'.$filename;
            $post->save();

            // Generate QR Code
            $qrCode = new QrCode(url('roti/' . $post->id));
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $qrFileName = 'qrcode_' . time() . '.png';
            $pathQr = public_path('storage/qrcode/' . auth()->user()->username );

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








           
        return response()->json($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
