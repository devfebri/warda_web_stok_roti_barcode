<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Cheesecake extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'ukuran', 
        'deskripsi',
        'jumlah',
        'harga',
        'gambar',
        'tanggal_dibuat',
        'qr_code',
        'status',
        'baker_id'
    ];

    protected $casts = [
        'tanggal_dibuat' => 'datetime',
        'status' => 'boolean',
        'harga' => 'decimal:2'
    ];

    protected $dates = ['deleted_at'];

    // Relasi dengan user (baker)
    public function baker()
    {
        return $this->belongsTo(User::class, 'baker_id');
    }

    // Relasi dengan transaksi detail
    public function transaksiDetails()
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    // Accessor untuk format harga
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format((float)$this->harga, 0, ',', '.');
    }

    // Accessor untuk tanggal expired
    public function getTanggalExpiredAttribute()
    {
        if (!$this->tanggal_dibuat) {
            return null;
        }
        return $this->tanggal_dibuat->addDays(3);
    }

    // Accessor untuk status expired
    public function getIsExpiredAttribute()
    {
        if (!$this->tanggal_dibuat) {
            return false;
        }
        $tanggalExpired = $this->tanggal_dibuat->copy()->addDays(3);
        return $tanggalExpired < Carbon::now();
    }

    // Accessor untuk hari tersisa
    public function getHariTersisaAttribute()
    {
        if (!$this->tanggal_dibuat) {
            return 0;
        }
        $tanggalExpired = $this->tanggal_dibuat->copy()->addDays(3);
        $diff = Carbon::now()->diffInDays($tanggalExpired, false);
        return $diff > 0 ? (int) ceil($diff) : 0;
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeFilterByDate($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_dibuat', [$startDate, $endDate]);
    }

    // Scope untuk produk aktif
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Scope untuk produk yang belum expired
    public function scopeNotExpired($query)
    {
        return $query->where('tanggal_dibuat', '>=', Carbon::now()->subDays(3));
    }

    // Method untuk update status expired otomatis
    public static function updateExpiredStatus()
    {
        $expiredProducts = self::where('status', true)
            ->where('tanggal_dibuat', '<', Carbon::now()->subDays(3))
            ->get();
        
        foreach ($expiredProducts as $product) {
            $product->update(['status' => false]);
        }
        
        return $expiredProducts->count();
    }

    public static function generateKodeproduk()
    {
        $tanggal = Carbon::now()->format('Ymd');
        $last = self::whereDate('created_at', Carbon::now())->latest()->first();
        $nomor = $last ? (int)substr($last->kode_produk, -4) + 1 : 1;
        return 'CSC' . $tanggal . str_pad($nomor, 4, '0', STR_PAD_LEFT);
    }
}
