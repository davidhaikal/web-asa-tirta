<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    //
    protected $table = 'barang_masuk';

    protected $fillable = [
        'produk_id',
        'qty',
        'jumlah',
        'tanggal_masuk',
        'catatan'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
