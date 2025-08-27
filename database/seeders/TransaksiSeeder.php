<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    public function run()
    {
        // Ambil user pertama sebagai kasir
        $kasir = User::first();
        
        if (!$kasir) {
            // Buat user jika belum ada
            $kasir = User::create([
                'name' => 'Kasir Test',
                'email' => 'kasir@test.com',
                'password' => bcrypt('password'),
                'role' => 'karyawan'
            ]);
        }

        // Data transaksi hari ini
        for ($i = 1; $i <= 5; $i++) {
            Transaksi::create([
                'kode_transaksi' => 'TRX' . now()->format('Ymd') . str_pad($i, 3, '0', STR_PAD_LEFT),
                'kasir_id' => $kasir->id,
                'nama_pelanggan' => 'Pelanggan ' . $i,
                'total_harga' => rand(50000, 200000),
                'bayar' => rand(200000, 300000),
                'kembalian' => rand(0, 100000),
                'status' => 'selesai',
                'metode_pembayaran' => 'tunai',
                'tanggal_transaksi' => now()
            ]);
        }

        // Data transaksi bulan ini (minggu lalu)
        for ($i = 1; $i <= 10; $i++) {
            Transaksi::create([
                'kode_transaksi' => 'TRX' . now()->subDays(7)->format('Ymd') . str_pad($i, 3, '0', STR_PAD_LEFT),
                'kasir_id' => $kasir->id,
                'nama_pelanggan' => 'Pelanggan Minggu Lalu ' . $i,
                'total_harga' => rand(75000, 250000),
                'bayar' => rand(250000, 350000),
                'kembalian' => rand(0, 100000),
                'status' => 'selesai',
                'metode_pembayaran' => rand(0, 1) ? 'tunai' : 'transfer',
                'tanggal_transaksi' => now()->subDays(7)
            ]);
        }

        // Data transaksi bulan lalu
        for ($i = 1; $i <= 15; $i++) {
            Transaksi::create([
                'kode_transaksi' => 'TRX' . now()->subMonth()->format('Ymd') . str_pad($i, 3, '0', STR_PAD_LEFT),
                'kasir_id' => $kasir->id,
                'nama_pelanggan' => 'Pelanggan Bulan Lalu ' . $i,
                'total_harga' => rand(60000, 180000),
                'bayar' => rand(180000, 280000),
                'kembalian' => rand(0, 100000),
                'status' => 'selesai',
                'metode_pembayaran' => rand(0, 1) ? 'tunai' : 'transfer',
                'tanggal_transaksi' => now()->subMonth()
            ]);
        }
    }
}
