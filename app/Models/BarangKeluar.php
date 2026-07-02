<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';

    protected $fillable = [
        'produk_id',
        'jumlah',
        'tanggal_keluar',
        'tujuan'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}