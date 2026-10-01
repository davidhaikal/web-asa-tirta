<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'invoice_no',
        'penjualan_id',
        'pelanggan',
        'total',
        'tanggal',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}
