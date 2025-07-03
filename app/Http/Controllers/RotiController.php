<?php

namespace App\Http\Controllers;

use App\Models\Roti;
use Illuminate\Http\Request;

class RotiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Roti $roti)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Roti $roti)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Roti $roti)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Roti $roti)
    {
        //
    }

    // List semua roti
    public function indexApi()
    {
        $roti = Roti::all();
        return response()->json(['status' => true, 'data' => $roti]);
    }

    // Detail roti
    public function showApi($id)
    {
        $roti = Roti::find($id);
        if (!$roti) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json(['status' => true, 'data' => $roti]);
    }

    // Tambah roti
    public function storeApi(Request $request)
    {
        
        $roti= new Roti();
        $roti->nama_roti = $request->nama_roti;
        $roti->harga_roti = $request->harga_roti;
        $roti->deskripsi_roti = $request->deskripsi_roti;
        $roti->save();
        
        return response()->json(['status' => true, 'message' => 'Data berhasil ditambah', 'data' => $roti], 201);   
    }

    // Update roti
    public function updateApi(Request $request, $id)
    {
        $roti = Roti::find($id);
        if (!$roti) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'harga' => 'sometimes|required|numeric',
            'stok' => 'sometimes|required|integer'
        ]);
        $roti->nama_roti = $request->nama_roti;
        $roti->harga_roti = $request->harga_roti;
        $roti->deskripsi_roti = $request->deskripsi_roti;
        $roti->save();
        return response()->json(['status' => true, 'message' => 'Data berhasil diupdate', 'data' => $roti]);
    }

    // Hapus roti
    public function destroyApi($id)
    {
        $roti = Roti::find($id);
        if (!$roti) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan'], 404);
        }
        $roti->delete();
        return response()->json(['status' => true, 'message' => 'Data berhasil dihapus']);
    }
}
