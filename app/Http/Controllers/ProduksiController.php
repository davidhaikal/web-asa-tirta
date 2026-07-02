<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produksi;

class ProduksiController extends Controller
{
    public function index()
    {
        $produk = Produksi::all();

        return view('produk.index', compact('produksi'));
    }

    //simpan produk baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'harga' => 'required|numeric',
        ]);

        Produk::create([
            'nama_produk' => $request->nama_produk,
            'harga' => $request->harga,
            'stok' => 0
        ]);

        return redirect('/produk')
            ->with('success', 'Produksi berhasil ditambahkan');
    }


    //from edit produk
    public function edit($id)
    {
        $produksi = Produksi::findOrFail($id);

        return view('produksi.edit', compact('produksi'));
    }


    //update Produk
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required',
            'harga' => 'required|numeric',
        ]);

        $produksi = Produksi::findOrFail($id);

        $produk->update([
            'nama_produksi' => $request->nama_produksi,
            'harga' => $request->harga,
        ]);

        return redirect('/produksi')
            ->with('success', 'Produksi berhasil diupdate');
    }

    //hapus produk
    public function destroy($id)
    {
        $produksi = Produksi::findOrFail($id);
        $produksi->delete();

        return redirect('/produksi')
            ->with('success', 'Produk berhasil dihapus');
    }
}