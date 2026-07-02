<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produk;

class Produksi extends Model
{
    //
    protected $fillable = [
        'produk_id',
        'jumlah_produksi',
        'tanggal_produksi',
        'status'
    ];

    // Relasi ke Produk
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    // Relasi ke QC
    public function qc()
    {
        return $this->hasOne(Qc::class);
    }
}
