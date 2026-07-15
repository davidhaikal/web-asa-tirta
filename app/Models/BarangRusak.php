<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produk;

class BarangRusak extends Model
{
    protected $fillable = [
        'produk_id',
        'jumlah',
        'keterangan',
        'tanggal_rusak',
        'tanggal'
    ];

    // Relasi ke tabel produk
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}