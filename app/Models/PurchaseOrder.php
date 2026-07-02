<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';

    protected $fillable = [
        'kode_po',
        'produk_id',
        'jumlah',
        'tanggal_butuh',
        'bulan_produksi',
        'status',
        'catatan',
        'user_id',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}