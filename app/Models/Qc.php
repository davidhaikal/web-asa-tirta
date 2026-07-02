<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qc extends Model
{
    //
    protected $fillable = [
        'produksi_id',
        'hasil',
        'keterangan'
    ];

    // Relasi ke Produksi
    public function produksi()
    {
        return $this->belongsTo(Produksi::class);
    }
}
