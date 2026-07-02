<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $fillable = [
        'kode',
        'tanggal',
        'pelanggan',
        'total',
        'metode',
        'status',
        'user_id',
    ];

    // Relasi ke Detail Penjualan
    public function detailPenjualans()
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    // Relasi ke Pengiriman
    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class);
    }

    // Relasi ke User (kasir)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}