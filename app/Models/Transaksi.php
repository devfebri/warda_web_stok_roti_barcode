<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Transaksi extends Model
{
    protected $fillable = [
        'kode_transaksi',
        'kasir_id',
        'nama_pelanggan',
        'total_harga',
        'bayar',
        'kembalian',
        'status',
        'metode_pembayaran',
        'catatan',
        'tanggal_transaksi'
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
        'tanggal_transaksi' => 'datetime'
    ];

    // Relasi dengan User (kasir)
    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    // Relasi dengan TransaksiDetail
    public function details(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    // Accessor untuk format total harga
    public function getFormattedTotalHargaAttribute()
    {
        return 'Rp ' . number_format((float)$this->total_harga, 0, ',', '.');
    }

    // Accessor untuk format bayar
    public function getFormattedBayarAttribute()
    {
        return 'Rp ' . number_format((float)$this->bayar, 0, ',', '.');
    }

    // Accessor untuk format kembalian
    public function getFormattedKembalianAttribute()
    {
        return 'Rp ' . number_format((float)$this->kembalian, 0, ',', '.');
    }

    // Generate kode transaksi otomatis
    public static function generateKodeTransaksi()
    {
        $tanggal = Carbon::now()->format('Ymd');
        $last = self::whereDate('created_at', Carbon::now())->latest()->first();
        $nomor = $last ? (int)substr($last->kode_transaksi, -4) + 1 : 1;
        return 'TRX' . $tanggal . str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeFilterByDate($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
    }

    // Scope untuk transaksi hari ini
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_transaksi', Carbon::today());
    }

    // Scope untuk transaksi bulan ini
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal_transaksi', Carbon::now()->month)
                    ->whereYear('tanggal_transaksi', Carbon::now()->year);
    }
}
