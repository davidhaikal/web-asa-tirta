<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanStok extends Model
{
    protected $fillable = [
        'produk_id',
        'qty',
        'jumlah',
        'tanggal',
        'status'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
