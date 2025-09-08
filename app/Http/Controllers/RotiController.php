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
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nama.required' => 'Nama roti wajib diisi',
            'nama.string' => 'Nama roti harus berupa teks',
            'nama.max' => 'Nama roti maksimal 255 karakter',
            'harga.required' => 'Harga wajib diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'harga.min' => 'Harga tidak boleh kurang dari 0',
            'gambar.required' => 'Gambar wajib diupload',
            'gambar.image' => 'File harus berupa gambar',
            'gambar.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'gambar.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        // Handle upload gambar
        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $pathGambar = public_path('storage/roti');

            if (!file_exists($pathGambar)) {
                mkdir($pathGambar, 0775, true);
            }

            $file->move($pathGambar, $filename);
            $gambarPath = 'storage/roti/' . $filename;
        }

        Roti::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'gambar' => $gambarPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Roti berhasil ditambahkan'
        ]);
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
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nama.required' => 'Nama roti wajib diisi',
            'nama.string' => 'Nama roti harus berupa teks',
            'nama.max' => 'Nama roti maksimal 255 karakter',
            'harga.required' => 'Harga wajib diisi',
            'harga.numeric' => 'Harga harus berupa angka',
            'harga.min' => 'Harga tidak boleh kurang dari 0',
            'gambar.image' => 'File harus berupa gambar',
            'gambar.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'gambar.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        // Handle upload gambar jika ada
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($roti->gambar && file_exists(public_path($roti->gambar))) {
                unlink(public_path($roti->gambar));
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $pathGambar = public_path('storage/roti');

            if (!file_exists($pathGambar)) {
                mkdir($pathGambar, 0775, true);
            }

            $file->move($pathGambar, $filename);
            $roti->gambar = 'storage/roti/' . $filename;
        }

        $roti->nama = $request->nama;
        $roti->harga = $request->harga;
        $roti->save();

        return response()->json([
            'success' => true,
            'message' => 'Roti berhasil diupdate'
        ]);
    }

    // Hapus roti
    public function destroy($id)
    {
        $roti = Roti::findOrFail($id);
        
        // Hapus gambar jika ada
        if ($roti->gambar && file_exists(public_path($roti->gambar))) {
            unlink(public_path($roti->gambar));
        }
        
        $roti->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Roti berhasil dihapus'
        ]);
    }
}
