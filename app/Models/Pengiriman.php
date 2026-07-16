<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    protected $table = 'pengiriman';
    protected $fillable = [
        'penjualan_id',
        'tanggal_kirim',
        'status',
        'bukti_foto'
    ];

    // Relasi ke Penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}
