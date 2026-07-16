<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $fillable = [
        'nama_produk',
        'kode_produk',
        'qty',
        'stok',
        'harga'
    ];

    public function stoks()
    {
        return $this->hasMany(Stok::class);
    }

    public function produksis()
    {
        return $this->hasMany(Produksi::class);
    }

    public function permintaanStok()
    {
        return $this->hasMany(PermintaanStok::class);
    }

    public function barangRusak()
    {
        return $this->hasMany(BarangRusak::class);
    }
}

