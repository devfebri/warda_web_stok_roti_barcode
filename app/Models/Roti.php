<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Roti extends Model
{
    protected $fillable = [
        'nama',
        'harga',
        'gambar'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    // Relasi dengan Cheesecakes
    public function cheesecakes(): HasMany
    {
        return $this->hasMany(Cheesecake::class);
    }

    // Accessor untuk format harga
    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format((float)$this->harga, 0, ',', '.');
    }
}
