<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiDetail extends Model
{
    protected $fillable = [
        'transaksi_id',
        'cheesecake_id',
        'jumlah',
        'harga_satuan',
        'subtotal'
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    // Relasi dengan Transaksi
    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class);
    }

    // Relasi dengan Cheesecake
    public function cheesecake(): BelongsTo
    {
        return $this->belongsTo(Cheesecake::class);
    }

    // Accessor untuk format harga satuan
    public function getFormattedHargaSatuanAttribute()
    {
        return 'Rp ' . number_format((float)$this->harga_satuan, 0, ',', '.');
    }

    // Accessor untuk format subtotal
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format((float)$this->subtotal, 0, ',', '.');
    }
}
