<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Produk;
use App\Models\Stok;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {

        $penjualan = Penjualan::create([
            'tanggal' => now(),
            'total' => $request->total
        ]);

        foreach($request->produk as $item){
        DetailPenjualan::create([
            'penjualan_id' => $penjualan->id,
            'produk_id' => $item['id'],
            'jumlah' => $item['jumlah'],
            'subtotal' => $item['subtotal']
        ]);

        // kurangi stok
        $produk = Produk::find($item['id']);
        $produk->stok -= $item['jumlah'];
        $produk->save();

        // catat stok keluar
        Stok::create([
            'produk_id' => $produk->id,
            'jenis' => 'keluar',
            'jumlah' => $item['jumlah'],
            'keterangan' => 'Penjualan'
        ]);
    }

    return "Penjualan berhasil";
}
}
