<?php

namespace App\Http\Controllers;

use App\Models\Roti;
use Illuminate\Http\Request;

class RotiController extends Controller
{
    // Tampilkan daftar roti
    public function index()
    {
        $rotis = Roti::all();
        return view('roti.index', compact('rotis'));
    }

    // Tampilkan detail roti
    public function show($id)
    {
        $roti = Roti::findOrFail($id);
        return response()->json($roti);
    }

    // Form tambah roti
    public function create()
    {
        return view('roti.create');
    }

    // Simpan roti baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ], [
            'nama.required' => 'Nama roti wajib diisi',
            'nama.string' => 'Nama roti harus berupa teks',
            'nama.max' => 'Nama roti maksimal 255 karakter',
            'harga.required' => 'Harga wajib diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'harga.min' => 'Harga tidak boleh kurang dari 0',
        ]);

        Roti::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
        ]);

        return response()->json(['message' => 'Roti berhasil ditambahkan']);
    }

    // Form edit roti
    public function edit($id)
    {
        $roti = Roti::findOrFail($id);
        return response()->json($roti);
    }

    // Update roti
    public function update(Request $request, $id)
    {
        $roti = Roti::findOrFail($id);
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
        ], [
            'nama.required' => 'Nama roti wajib diisi',
            'nama.string' => 'Nama roti harus berupa teks',
            'nama.max' => 'Nama roti maksimal 255 karakter',
            'harga.required' => 'Harga wajib diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'harga.min' => 'Harga tidak boleh kurang dari 0',
        ]);

        $roti->nama = $request->nama;
        $roti->harga = $request->harga;
        $roti->save();

        return response()->json(['message' => 'Roti berhasil diupdate']);
    }

    // Hapus roti
    public function destroy($id)
    {
        $roti = Roti::findOrFail($id);
        $roti->delete();
        return response()->json(['message' => 'Roti berhasil dihapus']);
    }
}
