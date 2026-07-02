<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use App\Models\Produk;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function masuk(Request $request)
    {
        $produk = Produk::find($request->produk_id);

        $produk->stok += $request->jumlah;
        $produk->save();

        Stok::create([
            'produk_id' => $produk->id,
            'jenis' => 'masuk',
            'jumlah' => $request->jumlah,
            'keterangan' => 'Barang masuk'
        ]);

        return "Stok masuk berhasil";
    }

    public function riwayat()
    {
        $stok = Stok::all();

        return view('stok.index', compact('stok'));
    }
}