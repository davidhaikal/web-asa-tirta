<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    //
     protected $fillable = [
        'produk_id',
        'jenis',
        'jumlah',
        'keterangan'
    ];

    // Relasi ke Produk
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
