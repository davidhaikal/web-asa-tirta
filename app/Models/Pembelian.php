<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelians';

    protected $fillable = [
        'no_transaksi',
        'supplier',
        'tanggal_pembelian',
        'tanggal_jatuh_tempo',
        'total_harga',
        'total_bayar',
        'status',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    public function details()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    public function pembayaranUtang()
    {
        return $this->hasMany(PembayaranUtang::class);
    }
}