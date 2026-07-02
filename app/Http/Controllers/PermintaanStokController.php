<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanStok;
use App\Models\Produk;

class PermintaanStokController extends Controller
{
    // TAMPIL HALAMAN
    public function index()
    {
        $produk = Produk::all();

        $permintaan = PermintaanStok::with('produk')->get();

        return view(
            'gudang.permintaan_stok',
            compact('produk', 'permintaan')
        );
    }


    // SIMPAN DATA
    public function store(Request $request)
    {
        PermintaanStok::create([

            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'status' => 'Menunggu'

        ]);

        return redirect('/gudang/permintaan-stok');
    }


    // FORM EDIT
    public function edit($id)
    {
        $permintaan = PermintaanStok::findOrFail($id);

        $produk = Produk::all();

        return view(
            'gudang.edit_permintaan_stok',
            compact('permintaan', 'produk')
        );
    }


    // UPDATE DATA
    public function update(Request $request, $id)
    {
        $permintaan = PermintaanStok::findOrFail($id);

        $permintaan->update([

            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'status' => $request->status

        ]);

        return redirect('/gudang/permintaan-stok');
    }


    // HAPUS DATA
    public function destroy($id)
    {
        PermintaanStok::destroy($id);

        return redirect('/gudang/permintaan-stok');
    }
}