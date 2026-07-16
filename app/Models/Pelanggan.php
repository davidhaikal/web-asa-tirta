<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $fillable = [
        'nama_pelanggan',
        'kota',
        'no_telp',
        'alamat',
        'status',
    ];
}
